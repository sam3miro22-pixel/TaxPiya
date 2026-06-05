<?php 

namespace App\Exports;
use App\Models\ViajeEstadosLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ViajeestadoslogViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(ViajeEstadosLog::exportViewFields());
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
			'From Estado',
			'To Estado',
			'Actor Tipo',
			'Actor Id',
			'Motivo Codigo',
			'Motivo Texto',
			'App Origen',
			'Ip',
			'Created At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->viaje_id,
			$record->from_estado,
			$record->to_estado,
			$record->actor_tipo,
			$record->actor_id,
			$record->motivo_codigo,
			$record->motivo_texto,
			$record->app_origen,
			$record->ip,
			$record->created_at
        ];
    }
}
