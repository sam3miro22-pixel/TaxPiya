<?php 

namespace App\Exports;
use App\Models\NotasOperacion;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class NotasoperacionViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(NotasOperacion::exportViewFields());
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
			'Entity Type',
			'Entity Id',
			'Viaje Id',
			'Conductor Id',
			'User Id',
			'Titulo',
			'Nota',
			'Adjunto Url',
			'Adjunto Mime',
			'Visibilidad',
			'Pinned',
			'Created By',
			'Created By Rol',
			'Created At',
			'Updated At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->entity_type,
			$record->entity_id,
			$record->viaje_id,
			$record->conductor_id,
			$record->user_id,
			$record->titulo,
			$record->nota,
			$record->adjunto_url,
			$record->adjunto_mime,
			$record->visibilidad,
			$record->pinned,
			$record->created_by,
			$record->created_by_rol,
			$record->created_at,
			$record->updated_at
        ];
    }
}
