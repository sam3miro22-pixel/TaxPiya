<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;

class WhatsAppController extends Controller
{
    public function index()
    {
        $wa = app(WhatsAppService::class);
        $status = $wa->getStatus();

        $supportPhone = DB::table('settings')
            ->where('key', 'whatsapp_support_phone')
            ->value('value');

        return view('pages.admin.whatsapp', [
            'status'       => $status,
            'supportPhone' => $supportPhone ?? '',
        ]);
    }

    public function status()
    {
        $wa = app(WhatsAppService::class);
        return response()->json($wa->getStatus());
    }

    public function logout()
    {
        $wa = app(WhatsAppService::class);
        $result = $wa->logout();
        return response()->json($result);
    }

    public function saveConfig(Request $request)
    {
        $phone = trim((string) $request->input('support_phone', ''));
        $phone = preg_replace('/\D/', '', $phone);

        if ($phone === '') {
            return back()->withErrors(['support_phone' => 'Ingresa un número de WhatsApp válido.']);
        }

        // Upsert the support phone in a settings table
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'whatsapp_support_phone'],
                ['value' => $phone, 'updated_at' => now()->toDateTimeString()]
            );
        } else {
            // If settings table doesn't exist, store in cache file
            $path = storage_path('app/whatsapp-support-phone.txt');
            file_put_contents($path, $phone);
        }

        return back()->with('saved', '✅ Número de soporte actualizado correctamente.');
    }
}
