<?php 

namespace App\Exports;
use App\Models\Users;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class UsersAccountviewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Users::exportAccountviewFields());
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
			'Name',
			'Email',
			'Remember Token',
			'Estado',
			'Telefono',
			'User Role Id'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->name,
			$record->email,
			$record->remember_token,
			$record->estado,
			$record->telefono,
			$record->user_role_id
        ];
    }
}
