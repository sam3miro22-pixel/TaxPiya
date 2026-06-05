<?php 

namespace App\Exports;
use App\Models\Permissions;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class PermissionsListExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	
	protected $query;
	
    public function __construct($query)
    {
        $this->query = $query->select(Permissions::exportListFields());
    }
	
    public function query()
    {
        return $this->query;
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
