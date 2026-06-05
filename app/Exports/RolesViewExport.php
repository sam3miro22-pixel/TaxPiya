<?php 

namespace App\Exports;
use App\Models\Roles;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class RolesViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Roles::exportViewFields());
        $this->rec_id = $rec_id;
    }


    public function query()
    {
        return $this->query->where("role_id", $this->rec_id);
    }


	public function headings(): array
    {
        return [
			'Role Id',
			'Role Name'
        ];
    }


    public function map($record): array
    {
        return [
			$record->role_id,
			$record->role_name
        ];
    }
}
