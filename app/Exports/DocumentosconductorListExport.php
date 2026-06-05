<?php 

namespace App\Exports;
use App\Models\DocumentosConductor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class DocumentosconductorListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(DocumentosConductor::exportListFields());
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
			'Tipo',
			'Numero',
			'Emisor',
			'Expedido At',
			'Expira At',
			'Archivo Url',
			'Archivo Mime',
			'Archivo Size Kb',
			'Hash Sha256',
			'Estado Verificacion',
			'Verificado Por',
			'Verificado At',
			'Rechazo Motivo',
			'Notas',
			'Created At',
			'Updated At'
        ];
    }
	
    public function map($record): array
    {
        return [
			$record->id,
			$record->conductor_id,
			$record->tipo,
			$record->numero,
			$record->emisor,
			$record->expedido_at,
			$record->expira_at,
			$record->archivo_url,
			$record->archivo_mime,
			$record->archivo_size_kb,
			$record->hash_sha256,
			$record->estado_verificacion,
			$record->verificado_por,
			$record->verificado_at,
			$record->rechazo_motivo,
			$record->notas,
			$record->created_at,
			$record->updated_at
        ];
    }
}
