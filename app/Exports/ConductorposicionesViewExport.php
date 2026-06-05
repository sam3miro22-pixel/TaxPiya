<?php 

namespace App\Exports;
use App\Models\ConductorPosiciones;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ConductorposicionesViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(ConductorPosiciones::exportViewFields());
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
			'Viaje Id',
			'Lat',
			'Lng',
			'Ubicacion',
			'Precision M',
			'Velocidad Kmh',
			'Heading',
			'Origen',
			'Provider',
			'Bateria',
			'App Estado',
			'Created At',
			'Received At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->conductor_id,
			$record->viaje_id,
			$record->lat,
			$record->lng,
			$record->ubicacion,
			$record->precision_m,
			$record->velocidad_kmh,
			$record->heading,
			$record->origen,
			$record->provider,
			$record->bateria,
			$record->app_estado,
			$record->created_at,
			$record->received_at
        ];
    }
}
