<?php 

namespace App\Exports;
use App\Models\Asignaciones;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class AsignacionesListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(Asignaciones::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
    }
	
	public function headings(): array
    {
        return [
			'Id',
			'Viaje Id',
			'Conductor Id',
			'Estado',
			'Ofertado At',
			'Expira At',
			'Respondido At',
			'Motivo Rechazo',
			'Distancia M',
			'Eta Min',
			'Radio Usado M',
			'Metodo',
			'Intento',
			'Prioridad',
			'Created At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->id,
			$record->viaje_id,
			$record->conductor_id,
			$record->estado,
			$record->ofertado_at,
			$record->expira_at,
			$record->respondido_at,
			$record->motivo_rechazo,
			$record->distancia_m,
			$record->eta_min,
			$record->radio_usado_m,
			$record->metodo,
			$record->intento,
			$record->prioridad,
			$record->created_at
        ];
    }
}
