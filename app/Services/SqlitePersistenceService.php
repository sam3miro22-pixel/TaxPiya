<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class SqlitePersistenceService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 120,
            'http_errors' => false,
        ]);
    }

    public function isEnabled(): bool
    {
        return $this->persistenceActive();
    }

    public function canBackup(): bool
    {
        return $this->persistenceActive() && $this->githubToken() !== '';
    }

    public function databasePath(): string
    {
        $path = (string) config('database.connections.sqlite.database');

        if ($path === ':memory:') {
            throw new RuntimeException('SQLite en memoria no admite respaldo persistente.');
        }

        return $path;
    }

    public function restore(): bool
    {
        if (!$this->persistenceActive()) {
            return false;
        }

        if ($this->githubToken() !== '' && $this->restoreFromGithubApi()) {
            return true;
        }

        return $this->restoreFromPublicRaw();
    }

    public function backup(): bool
    {
        if (!$this->canBackup()) {
            return false;
        }

        $path = $this->databasePath();
        if (!is_file($path)) {
            Log::warning('SqlitePersistence: no hay SQLite local para respaldar.', ['path' => $path]);
            return false;
        }

        try {
            $this->checkpointWal($path);
            $binary = file_get_contents($path);
            $size = strlen((string) $binary);

            if ($size < 1024) {
                Log::warning('SqlitePersistence: BD demasiado pequeña; respaldo omitido.', ['bytes' => $size]);
                return false;
            }

            $existing = $this->fetchRemoteMeta();
            $payload = [
                'message' => 'TaxPiya SQLite backup ' . now()->toIso8601String(),
                'content' => base64_encode($binary),
            ];

            if (!empty($existing['sha'])) {
                $payload['sha'] = $existing['sha'];
            }

            $response = $this->http->request('PUT', $this->contentsUrl(), [
                'headers' => $this->githubHeaders(),
                'json'    => $payload,
            ]);

            if ($response->getStatusCode() >= 300) {
                Log::error('SqlitePersistence: GitHub upload falló', [
                    'status' => $response->getStatusCode(),
                    'body'   => (string) $response->getBody(),
                ]);
                return false;
            }

            Log::info('SqlitePersistence: respaldo subido a GitHub.', ['bytes' => $size]);

            return true;
        } catch (Throwable $e) {
            Log::error('SqlitePersistence: error al respaldar en GitHub', ['err' => $e->getMessage()]);
            return false;
        }
    }

    public function status(): array
    {
        $path = $this->databasePath();
        $localSize = is_file($path) ? (int) filesize($path) : 0;
        $remote = null;

        if ($this->persistenceActive()) {
            try {
                $remote = $this->githubToken() !== ''
                    ? $this->fetchRemoteMeta()
                    : ['public_raw' => $this->publicRawUrl()];
            } catch (Throwable $e) {
                $remote = ['error' => $e->getMessage()];
            }
        }

        return [
            'enabled'    => $this->persistenceActive(),
            'can_backup' => $this->canBackup(),
            'provider'   => 'github',
            'repo'       => $this->repoSlug(),
            'path'       => $this->remotePath(),
            'local_path' => $path,
            'local_size' => $localSize,
            'remote'     => $remote,
        ];
    }

    private function persistenceActive(): bool
    {
        if (config('database.default') !== 'sqlite') {
            return false;
        }

        return (bool) config('taxpiya.persistence.enabled', true);
    }

    private function restoreFromGithubApi(): bool
    {
        try {
            $meta = $this->fetchRemoteMeta();
            if ($meta === null) {
                return false;
            }

            return $this->writeRestoredBinary(base64_decode($meta['content'], true), ['source' => 'github-api']);
        } catch (Throwable $e) {
            Log::error('SqlitePersistence: error restore GitHub API', ['err' => $e->getMessage()]);
            return false;
        }
    }

    private function restoreFromPublicRaw(): bool
    {
        if (!config('taxpiya.persistence.public_restore_fallback', true)) {
            return false;
        }

        try {
            $response = $this->http->request('GET', $this->publicRawUrl(), ['timeout' => 120]);
            if ($response->getStatusCode() >= 300) {
                Log::info('SqlitePersistence: sin respaldo público en GitHub raw.');
                return false;
            }

            $binary = (string) $response->getBody();

            return $this->writeRestoredBinary($binary, ['source' => 'raw']);
        } catch (Throwable $e) {
            Log::error('SqlitePersistence: error restore raw GitHub', ['err' => $e->getMessage()]);
            return false;
        }
    }

    private function writeRestoredBinary(?string $binary, array $meta): bool
    {
        if ($binary === null || !$this->isValidSqliteBinary($binary)) {
            Log::warning('SqlitePersistence: respaldo inválido.');
            return false;
        }

        if (!$this->backupHasCriticalTables($binary)) {
            Log::warning('SqlitePersistence: respaldo sin tablas críticas; se conserva BD local.');
            return false;
        }

        $target = $this->databasePath();
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $tmp = $target . '.restore.' . getmypid();
        file_put_contents($tmp, $binary);

        if (is_file($target)) {
            @unlink($target);
        }

        rename($tmp, $target);
        @unlink($target . '-wal');
        @unlink($target . '-shm');
        @chmod($target, 0664);

        Log::info('SqlitePersistence: BD restaurada.', [
            'bytes'  => strlen($binary),
            'source' => $meta['source'] ?? 'github',
        ]);

        return true;
    }

    private function publicRawUrl(): string
    {
        [$owner, $repo] = explode('/', $this->repoSlug(), 2);

        return "https://raw.githubusercontent.com/{$owner}/{$repo}/main/" . $this->remotePath();
    }

    private function githubToken(): string
    {
        return trim((string) config('taxpiya.persistence.github_token', ''));
    }

    private function repoSlug(): string
    {
        $owner = config('taxpiya.persistence.github_owner', 'sam3miro22-pixel');
        $repo = config('taxpiya.persistence.github_repo', 'taxpiya-db-backup');

        return "{$owner}/{$repo}";
    }

    private function remotePath(): string
    {
        return (string) config('taxpiya.persistence.github_path', 'taxpiya.sqlite');
    }

    private function contentsUrl(): string
    {
        [$owner, $repo] = explode('/', $this->repoSlug(), 2);

        return 'https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo)
            . '/contents/' . $this->remotePath();
    }

    private function githubHeaders(): array
    {
        return [
            'Authorization'        => 'Bearer ' . $this->githubToken(),
            'Accept'               => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent'           => 'TaxPiya-Backup',
        ];
    }

    private function fetchRemoteMeta(): ?array
    {
        $response = $this->http->request('GET', $this->contentsUrl(), [
            'headers' => $this->githubHeaders(),
        ]);

        if ($response->getStatusCode() === 404) {
            return null;
        }

        if ($response->getStatusCode() >= 300) {
            throw new RuntimeException('GitHub GET failed: HTTP ' . $response->getStatusCode());
        }

        $data = json_decode((string) $response->getBody(), true);
        if (!is_array($data) || empty($data['content'])) {
            return null;
        }

        return [
            'sha'     => $data['sha'] ?? null,
            'size'    => $data['size'] ?? null,
            'content' => preg_replace('/\s+/', '', (string) $data['content']),
        ];
    }

    private function checkpointWal(string $path): void
    {
        try {
            if (Schema::hasTable('users')) {
                DB::statement('PRAGMA wal_checkpoint(TRUNCATE)');
                return;
            }
        } catch (Throwable) {
            // ignore
        }

        $pdo = new \PDO('sqlite:' . $path, null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec('PRAGMA wal_checkpoint(TRUNCATE);');
    }

    private function isValidSqliteBinary(string $binary): bool
    {
        return str_starts_with($binary, 'SQLite format 3');
    }

    private function backupHasCriticalTables(string $binary): bool
    {
        $tmp = sys_get_temp_dir() . '/taxpiya_validate_' . getmypid() . '.sqlite';
        try {
            file_put_contents($tmp, $binary);
            $pdo = new \PDO('sqlite:' . $tmp, null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            $required = ['users', 'conductores', 'conductor_posicion_actual', 'viajes', 'vehiculos'];
            foreach ($required as $table) {
                $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = ? LIMIT 1");
                $stmt->execute([$table]);
                if (!$stmt->fetchColumn()) {
                    return false;
                }
            }
            return true;
        } catch (Throwable) {
            return false;
        } finally {
            @unlink($tmp);
            @unlink($tmp . '-wal');
            @unlink($tmp . '-shm');
        }
    }
}
