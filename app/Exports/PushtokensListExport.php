<?php 

namespace App\Exports;
use App\Models\PushTokens;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class PushtokensListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(PushTokens::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
    }
	
	public function headings(): array
    {
        return [
			'Id',
			'Dispositivo Id',
			'Provider',
			'Token',
			'Token Hash',
			'Estado',
			'Scope',
			'Ultimo Uso At',
			'Invalidado At',
			'Motivo Invalidez',
			'Idempotencia',
			'Created At',
			'Updated At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->id,
			$record->dispositivo_id,
			$record->provider,
			$record->token,
			$record->token_hash,
			$record->estado,
			$record->scope,
			$record->ultimo_uso_at,
			$record->invalidado_at,
			$record->motivo_invalidez,
			$record->idempotencia,
			$record->created_at,
			$record->updated_at
        ];
    }
}
