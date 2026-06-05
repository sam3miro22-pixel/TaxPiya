<?php 

namespace App\Exports;
use App\Models\Calificaciones;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class CalificacionesViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Calificaciones::exportViewFields());
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
			'Rater Id',
			'Rater Rol',
			'Ratee Id',
			'Ratee Rol',
			'Puntuacion',
			'Comentario',
			'Etiquetas Json',
			'Visible',
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
			$record->rater_id,
			$record->rater_rol,
			$record->ratee_id,
			$record->ratee_rol,
			$record->puntuacion,
			$record->comentario,
			$record->etiquetas_json,
			$record->visible,
			$record->moderado,
			$record->moderado_motivo,
			$record->ip,
			$record->created_at
        ];
    }
}
