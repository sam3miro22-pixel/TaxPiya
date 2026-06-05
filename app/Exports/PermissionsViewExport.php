<?php 

namespace App\Exports;
use App\Models\Permissions;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class PermissionsViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Permissions::exportViewFields());
        $this->rec_id = $rec_id;
    }


    public function query()
    {
        return $this->query->where("permission_id", $this->rec_id);
    }


	public function headings(): array
    {
        return [
			'Permission Id',
			'Permission',
			'Role Id'
        ];
    }


    public function map($record): array
    {
        return [
			$record->permission_id,
			$record->permission,
			$record->role_id
        ];
    }
}
