<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class AuditoriaEventos extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'auditoria_eventos';
	

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
		'actor_user_id','actor_rol','origen','accion','tabla_objetivo','registro_pk','detalles','viaje_id','conductor_id','before_json','after_json','ip','user_agent','request_id'
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
				tabla_objetivo LIKE ?  OR 
				registro_pk LIKE ?  OR 
				detalles LIKE ?  OR 
				before_json LIKE ?  OR 
				after_json LIKE ?  OR 
				ip LIKE ?  OR 
				user_agent LIKE ?  OR 
				request_id LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"actor_user_id",

			"actor_rol",

			"origen",

			"accion",

			"tabla_objetivo",

			"registro_pk",

			"detalles",

			"viaje_id",

			"conductor_id",

			"before_json",

			"after_json",

			"ip",

			"user_agent",

			"request_id",

			"created_at" 
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

			"actor_user_id",

			"actor_rol",

			"origen",

			"accion",

			"tabla_objetivo",

			"registro_pk",

			"detalles",

			"viaje_id",

			"conductor_id",

			"before_json",

			"after_json",

			"ip",

			"user_agent",

			"request_id",

			"created_at" 
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

			"actor_user_id",

			"actor_rol",

			"origen",

			"accion",

			"tabla_objetivo",

			"registro_pk",

			"detalles",

			"viaje_id",

			"conductor_id",

			"before_json",

			"after_json",

			"ip",

			"user_agent",

			"request_id",

			"created_at" 
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

			"actor_user_id",

			"actor_rol",

			"origen",

			"accion",

			"tabla_objetivo",

			"registro_pk",

			"detalles",

			"viaje_id",

			"conductor_id",

			"before_json",

			"after_json",

			"ip",

			"user_agent",

			"request_id",

			"created_at" 
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

			"actor_user_id",

			"actor_rol",

			"origen",

			"accion",

			"tabla_objetivo",

			"registro_pk",

			"detalles",

			"viaje_id",

			"conductor_id",

			"before_json",

			"after_json",

			"ip",

			"user_agent",

			"request_id" 
		];
	}
}
