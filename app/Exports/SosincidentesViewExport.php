<?php 

namespace App\Exports;
use App\Models\SosIncidentes;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class SosincidentesViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(SosIncidentes::exportViewFields());
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
			'Actor Tipo',
			'Actor User Id',
			'Conductor Id',
			'Categoria',
			'Severidad',
			'Estado',
			'Descripcion',
			'Telefono Contacto',
			'Lat',
			'Lng',
			'Ubicacion',
			'Operador Id',
			'Asignado At',
			'Reconocido At',
			'Atendido At',
			'Resuelto At',
			'Cerrado At',
			'Nivel Escalamiento',
			'Sla Minutos',
			'Breach At',
			'Contacto Inicial',
			'Contacto Resultado',
			'Evidencia Url',
			'Notas Operacion',
			'Created At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->viaje_id,
			$record->actor_tipo,
			$record->actor_user_id,
			$record->conductor_id,
			$record->categoria,
			$record->severidad,
			$record->estado,
			$record->descripcion,
			$record->telefono_contacto,
			$record->lat,
			$record->lng,
			$record->ubicacion,
			$record->operador_id,
			$record->asignado_at,
			$record->reconocido_at,
			$record->atendido_at,
			$record->resuelto_at,
			$record->cerrado_at,
			$record->nivel_escalamiento,
			$record->sla_minutos,
			$record->breach_at,
			$record->contacto_inicial,
			$record->contacto_resultado,
			$record->evidencia_url,
			$record->notas_operacion,
			$record->created_at
        ];
    }
}
