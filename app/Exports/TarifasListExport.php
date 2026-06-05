<?php 

namespace App\Exports;
use App\Models\Tarifas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class TarifasListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(Tarifas::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
    }
	
	public function headings(): array
    {
        return [
			'Id',
			'Nombre',
			'Descripcion',
			'Scope',
			'Ciudad',
			'Categoria',
			'Horario',
			'Origen Ref',
			'Destino Ref',
			'Moneda',
			'Monto Fijo',
			'Recargo Nocturno',
			'Recargo Festivo',
			'Recargo Aeropuerto',
			'Incluye Peajes',
			'Minutos Espera Incluidos',
			'Valor Minuto Espera',
			'Vigente Desde',
			'Vigente Hasta',
			'Activa',
			'Prioridad',
			'Version',
			'Created At',
			'Updated At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->id,
			$record->nombre,
			$record->descripcion,
			$record->scope,
			$record->ciudad,
			$record->categoria,
			$record->horario,
			$record->origen_ref,
			$record->destino_ref,
			$record->moneda,
			$record->monto_fijo,
			$record->recargo_nocturno,
			$record->recargo_festivo,
			$record->recargo_aeropuerto,
			$record->incluye_peajes,
			$record->minutos_espera_incluidos,
			$record->valor_minuto_espera,
			$record->vigente_desde,
			$record->vigente_hasta,
			$record->activa,
			$record->prioridad,
			$record->version,
			$record->created_at,
			$record->updated_at
        ];
    }
}
