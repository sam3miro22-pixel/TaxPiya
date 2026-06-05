<?php 

namespace App\Exports;
use App\Models\ConductorPosiciones;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ConductorposicionesListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(ConductorPosiciones::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
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
