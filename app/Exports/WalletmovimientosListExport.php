<?php 

namespace App\Exports;
use App\Models\WalletMovimientos;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class WalletmovimientosListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(WalletMovimientos::exportListFields());
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
			'Admin User Id',
			'Sentido',
			'Motivo',
			'Monto',
			'Moneda',
			'Saldo Antes',
			'Saldo Despues',
			'Descripcion',
			'Referencia Externa',
			'Idempotencia',
			'Anulado',
			'Anulado Por',
			'Anulado Motivo',
			'Anulado At',
			'Created At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->id,
			$record->conductor_id,
			$record->viaje_id,
			$record->admin_user_id,
			$record->sentido,
			$record->motivo,
			$record->monto,
			$record->moneda,
			$record->saldo_antes,
			$record->saldo_despues,
			$record->descripcion,
			$record->referencia_externa,
			$record->idempotencia,
			$record->anulado,
			$record->anulado_por,
			$record->anulado_motivo,
			$record->anulado_at,
			$record->created_at
        ];
    }
}
