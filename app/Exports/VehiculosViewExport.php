<?php 

namespace App\Exports;
use App\Models\Vehiculos;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class VehiculosViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Vehiculos::exportViewFields());
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
			'Conductor Id',
			'Placa',
			'Vin',
			'Marca',
			'Linea',
			'Modelo Anio',
			'Color',
			'Categoria',
			'Asientos',
			'Soat Numero',
			'Soat Expira',
			'Tecnomecanica Expira',
			'Seguro Extracontractual Numero',
			'Seguro Extracontractual Expira',
			'Estado Vehiculo',
			'Verificacion Estado',
			'Verificacion Notas',
			'Foto Principal',
			'Created At',
			'Updated At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->conductor_id,
			$record->placa,
			$record->vin,
			$record->marca,
			$record->linea,
			$record->modelo_anio,
			$record->color,
			$record->categoria,
			$record->asientos,
			$record->soat_numero,
			$record->soat_expira,
			$record->tecnomecanica_expira,
			$record->seguro_extracontractual_numero,
			$record->seguro_extracontractual_expira,
			$record->estado_vehiculo,
			$record->verificacion_estado,
			$record->verificacion_notas,
			$record->foto_principal,
			$record->created_at,
			$record->updated_at
        ];
    }
}
