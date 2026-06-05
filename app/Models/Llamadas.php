<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Llamadas extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'llamadas';
	

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
		'viaje_id','llamador_user_id','llamador_rol','receptor_user_id','receptor_rol','tipo','provider','provider_call_id','provider_room_id','caller_phone_snapshot','callee_phone_snapshot','proxy_number','masked','estado','ring_start_at','connected_at','ended_at','duracion_seg','dispositivo_id','ip','idempotencia'
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
				provider_call_id LIKE ?  OR 
				provider_room_id LIKE ?  OR 
				caller_phone_snapshot LIKE ?  OR 
				callee_phone_snapshot LIKE ?  OR 
				proxy_number LIKE ?  OR 
				ip LIKE ?  OR 
				idempotencia LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"viaje_id",

			"llamador_user_id",

			"llamador_rol",

			"receptor_user_id",

			"receptor_rol",

			"tipo",

			"provider",

			"provider_call_id",

			"provider_room_id",

			"caller_phone_snapshot",

			"callee_phone_snapshot",

			"proxy_number",

			"masked",

			"estado",

			"call_start_at",

			"ring_start_at",

			"connected_at",

			"ended_at",

			"duracion_seg",

			"dispositivo_id",

			"ip",

			"idempotencia",

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

			"viaje_id",

			"llamador_user_id",

			"llamador_rol",

			"receptor_user_id",

			"receptor_rol",

			"tipo",

			"provider",

			"provider_call_id",

			"provider_room_id",

			"caller_phone_snapshot",

			"callee_phone_snapshot",

			"proxy_number",

			"masked",

			"estado",

			"call_start_at",

			"ring_start_at",

			"connected_at",

			"ended_at",

			"duracion_seg",

			"dispositivo_id",

			"ip",

			"idempotencia",

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

			"viaje_id",

			"llamador_user_id",

			"llamador_rol",

			"receptor_user_id",

			"receptor_rol",

			"tipo",

			"provider",

			"provider_call_id",

			"provider_room_id",

			"caller_phone_snapshot",

			"callee_phone_snapshot",

			"proxy_number",

			"masked",

			"estado",

			"call_start_at",

			"ring_start_at",

			"connected_at",

			"ended_at",

			"duracion_seg",

			"dispositivo_id",

			"ip",

			"idempotencia",

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

			"viaje_id",

			"llamador_user_id",

			"llamador_rol",

			"receptor_user_id",

			"receptor_rol",

			"tipo",

			"provider",

			"provider_call_id",

			"provider_room_id",

			"caller_phone_snapshot",

			"callee_phone_snapshot",

			"proxy_number",

			"masked",

			"estado",

			"call_start_at",

			"ring_start_at",

			"connected_at",

			"ended_at",

			"duracion_seg",

			"dispositivo_id",

			"ip",

			"idempotencia",

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

			"viaje_id",

			"llamador_user_id",

			"llamador_rol",

			"receptor_user_id",

			"receptor_rol",

			"tipo",

			"provider",

			"provider_call_id",

			"provider_room_id",

			"caller_phone_snapshot",

			"callee_phone_snapshot",

			"proxy_number",

			"masked",

			"estado",

			"ring_start_at",

			"connected_at",

			"ended_at",

			"duracion_seg",

			"dispositivo_id",

			"ip",

			"idempotencia" 
		];
	}
}
