<?php 

namespace App\Exports;
use App\Models\Conductores;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ConductoresViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Conductores::exportViewFields());
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
			'Estado Operitivo',
			'Disponible',
			'Last Online At',
			'Rating Promedio',
			'Total Viajes',
			'Licencia Numero',
			'Licencia Categoria',
			'Licencia Expira',
			'Soat Numero',
			'Soat Expira',
			'Seguro Numero',
			'Verificacion Estado',
			'Verificacion Nivel',
			'Verificacion Notas',
			'Contacto Emergencia Nombre',
			'Contacto Emergencia Telefono',
			'Location Permission',
			'Created At',
			'Updated At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->user_id,
			$record->estado_operitivo,
			$record->disponible,
			$record->last_online_at,
			$record->rating_promedio,
			$record->total_viajes,
			$record->licencia_numero,
			$record->licencia_categoria,
			$record->licencia_expira,
			$record->soat_numero,
			$record->soat_expira,
			$record->seguro_numero,
			$record->verificacion_estado,
			$record->verificacion_nivel,
			$record->verificacion_notas,
			$record->contacto_emergencia_nombre,
			$record->contacto_emergencia_telefono,
			$record->location_permission,
			$record->created_at,
			$record->updated_at
        ];
    }
}
