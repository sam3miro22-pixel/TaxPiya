<?php 

namespace App\Exports;
use App\Models\WalletSaldos;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class WalletsaldosListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(WalletSaldos::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
    }
	
	public function headings(): array
    {
        return [
			'Conductor Id',
			'Saldo Actual',
			'Saldo Reservado',
			'Min Operativo',
			'Moneda',
			'Last Movimiento Id',
			'Last Movimiento At',
			'Bloqueado',
			'Motivo Bloqueo',
			'Created At',
			'Updated At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->conductor_id,
			$record->saldo_actual,
			$record->saldo_reservado,
			$record->min_operativo,
			$record->moneda,
			$record->last_movimiento_id,
			$record->last_movimiento_at,
			$record->bloqueado,
			$record->motivo_bloqueo,
			$record->created_at,
			$record->updated_at
        ];
    }
}
