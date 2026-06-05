<?php 

namespace App\Exports;
use App\Models\AuditoriaEventos;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class AuditoriaeventosViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(AuditoriaEventos::exportViewFields());
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
			'Actor User Id',
			'Actor Rol',
			'Origen',
			'Accion',
			'Tabla Objetivo',
			'Registro Pk',
			'Detalles',
			'Viaje Id',
			'Conductor Id',
			'Before Json',
			'After Json',
			'Ip',
			'User Agent',
			'Request Id',
			'Created At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->actor_user_id,
			$record->actor_rol,
			$record->origen,
			$record->accion,
			$record->tabla_objetivo,
			$record->registro_pk,
			$record->detalles,
			$record->viaje_id,
			$record->conductor_id,
			$record->before_json,
			$record->after_json,
			$record->ip,
			$record->user_agent,
			$record->request_id,
			$record->created_at
        ];
    }
}
