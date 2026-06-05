<?php 

namespace App\Exports;
use App\Models\ChatMensajes;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ChatmensajesViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(ChatMensajes::exportViewFields());
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
			'Viaje Id',
			'Remitente Id',
			'Remitente Rol',
			'Tipo',
			'Mensaje',
			'Media Url',
			'Media Tipo',
			'Reply To Id',
			'Lat',
			'Lng',
			'Leido Por Pasajero At',
			'Leido Por Conductor At',
			'Moderado',
			'Moderado Motivo',
			'Ip',
			'Created At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->viaje_id,
			$record->remitente_id,
			$record->remitente_rol,
			$record->tipo,
			$record->mensaje,
			$record->media_url,
			$record->media_tipo,
			$record->reply_to_id,
			$record->lat,
			$record->lng,
			$record->leido_por_pasajero_at,
			$record->leido_por_conductor_at,
			$record->moderado,
			$record->moderado_motivo,
			$record->ip,
			$record->created_at
        ];
    }
}
