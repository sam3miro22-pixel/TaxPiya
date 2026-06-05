<?php 

namespace App\Exports;
use App\Models\Notificaciones;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class NotificacionesListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(Notificaciones::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
    }
	
	public function headings(): array
    {
        return [
			'Id',
			'User Id',
			'Viaje Id',
			'Canal',
			'Proveedor',
			'Titulo',
			'Cuerpo',
			'Data Json',
			'Device Token Snapshot',
			'Estado',
			'Programada At',
			'Enviada At',
			'Entregada At',
			'Abierta At',
			'Fallida At',
			'Provider Message Id',
			'Error Code',
			'Error Message',
			'Idempotencia',
			'Prioridad',
			'Origen Evento',
			'Created At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->id,
			$record->user_id,
			$record->viaje_id,
			$record->canal,
			$record->proveedor,
			$record->titulo,
			$record->cuerpo,
			$record->data_json,
			$record->device_token_snapshot,
			$record->estado,
			$record->programada_at,
			$record->enviada_at,
			$record->entregada_at,
			$record->abierta_at,
			$record->fallida_at,
			$record->provider_message_id,
			$record->error_code,
			$record->error_message,
			$record->idempotencia,
			$record->prioridad,
			$record->origen_evento,
			$record->created_at
        ];
    }
}
