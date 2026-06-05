<?php 

namespace App\Exports;
use App\Models\UsuarioDispositivos;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class UsuariodispositivosViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(UsuarioDispositivos::exportViewFields());
        $this->rec_id = $rec_id;
    }


    public function query()
    {
        return $this->query->where("id", $this->rec_id);
    }


	public function headings(): array
    {
        return [
			'Id',
			'User Id',
			'Device Uuid',
			'Plataforma',
			'App Version',
			'Os Version',
			'Idioma',
			'Zona Horaria',
			'Fabricante',
			'Modelo',
			'Notificaciones Activas',
			'Activo',
			'Is Emulador',
			'Root Jailbreak',
			'Installed At',
			'Last Seen At',
			'Last Ip',
			'Created At',
			'Updated At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->user_id,
			$record->device_uuid,
			$record->plataforma,
			$record->app_version,
			$record->os_version,
			$record->idioma,
			$record->zona_horaria,
			$record->fabricante,
			$record->modelo,
			$record->notificaciones_activas,
			$record->activo,
			$record->is_emulador,
			$record->root_jailbreak,
			$record->installed_at,
			$record->last_seen_at,
			$record->last_ip,
			$record->created_at,
			$record->updated_at
        ];
    }
}
