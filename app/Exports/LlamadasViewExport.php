<?php 

namespace App\Exports;
use App\Models\Llamadas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class LlamadasViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Llamadas::exportViewFields());
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
			'Llamador User Id',
			'Llamador Rol',
			'Receptor User Id',
			'Receptor Rol',
			'Tipo',
			'Provider',
			'Provider Call Id',
			'Provider Room Id',
			'Caller Phone Snapshot',
			'Callee Phone Snapshot',
			'Proxy Number',
			'Masked',
			'Estado',
			'Call Start At',
			'Ring Start At',
			'Connected At',
			'Ended At',
			'Duracion Seg',
			'Dispositivo Id',
			'Ip',
			'Idempotencia',
			'Created At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->viaje_id,
			$record->llamador_user_id,
			$record->llamador_rol,
			$record->receptor_user_id,
			$record->receptor_rol,
			$record->tipo,
			$record->provider,
			$record->provider_call_id,
			$record->provider_room_id,
			$record->caller_phone_snapshot,
			$record->callee_phone_snapshot,
			$record->proxy_number,
			$record->masked,
			$record->estado,
			$record->call_start_at,
			$record->ring_start_at,
			$record->connected_at,
			$record->ended_at,
			$record->duracion_seg,
			$record->dispositivo_id,
			$record->ip,
			$record->idempotencia,
			$record->created_at
        ];
    }
}
