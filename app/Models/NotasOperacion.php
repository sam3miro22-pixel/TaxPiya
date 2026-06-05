<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class NotasOperacion extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'notas_operacion';
	

	/**
     * The table primary key field
     *
     * @var string
     */
	protected $primaryKey = 'id';
	

	/**
     * Table fillable fields
     *
     * @var array
     */
	protected $fillable = [
		'entity_type','entity_id','viaje_id','conductor_id','user_id','titulo','nota','adjunto_url','adjunto_mime','visibilidad','pinned','created_by','created_by_rol'
	];
	public $timestamps = false;
	

	/**
     * Set search query for the model
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $text
     */
	public static function search($query, $text){
		//search table record 
		$search_condition = '(
				id LIKE ?  OR 
				titulo LIKE ?  OR 
				nota LIKE ?  OR 
				adjunto_url LIKE ?  OR 
				adjunto_mime LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%"
		];
		//setting search conditions
		$query->whereRaw($search_condition, $search_params);

	}
	

	/**
     * return list page fields of the model.
     * 
     * @return array
     */
	public static function listFields(){
		return [ 
			"id",

			"entity_type",

			"entity_id",

			"viaje_id",

			"conductor_id",

			"user_id",

			"titulo",

			"nota",

			"adjunto_url",

			"adjunto_mime",

			"visibilidad",

			"pinned",

			"created_by",

			"created_by_rol",

			"created_at",

			"updated_at" 
		];
	}

	

	/**
     * return exportList page fields of the model.
     * 
     * @return array
     */
	public static function exportListFields(){
		return [ 
			"id",

			"entity_type",

			"entity_id",

			"viaje_id",

			"conductor_id",

			"user_id",

			"titulo",

			"nota",

			"adjunto_url",

			"adjunto_mime",

			"visibilidad",

			"pinned",

			"created_by",

			"created_by_rol",

			"created_at",

			"updated_at" 
		];
	}

	

	/**
     * return view page fields of the model.
     * 
     * @return array
     */
	public static function viewFields(){
		return [ 
			"id",

			"entity_type",

			"entity_id",

			"viaje_id",

			"conductor_id",

			"user_id",

			"titulo",

			"nota",

			"adjunto_url",

			"adjunto_mime",

			"visibilidad",

			"pinned",

			"created_by",

			"created_by_rol",

			"created_at",

			"updated_at" 
		];
	}

	

	/**
     * return exportView page fields of the model.
     * 
     * @return array
     */
	public static function exportViewFields(){
		return [ 
			"id",

			"entity_type",

			"entity_id",

			"viaje_id",

			"conductor_id",

			"user_id",

			"titulo",

			"nota",

			"adjunto_url",

			"adjunto_mime",

			"visibilidad",

			"pinned",

			"created_by",

			"created_by_rol",

			"created_at",

			"updated_at" 
		];
	}

	

	/**
     * return edit page fields of the model.
     * 
     * @return array
     */
	public static function editFields(){
		return [ 
			"id",

			"entity_type",

			"entity_id",

			"viaje_id",

			"conductor_id",

			"user_id",

			"titulo",

			"nota",

			"adjunto_url",

			"adjunto_mime",

			"visibilidad",

			"pinned",

			"created_by",

			"created_by_rol" 
		];
	}
}
