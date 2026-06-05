<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Notificaciones extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'notificaciones';
	

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
		'user_id','viaje_id','canal','proveedor','titulo','cuerpo','data_json','device_token_snapshot','estado','programada_at','enviada_at','entregada_at','abierta_at','fallida_at','provider_message_id','error_code','error_message','idempotencia','prioridad','origen_evento'
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
				cuerpo LIKE ?  OR 
				data_json LIKE ?  OR 
				device_token_snapshot LIKE ?  OR 
				provider_message_id LIKE ?  OR 
				error_code LIKE ?  OR 
				error_message LIKE ?  OR 
				idempotencia LIKE ?  OR 
				origen_evento LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"user_id",

			"viaje_id",

			"canal",

			"proveedor",

			"titulo",

			"cuerpo",

			"data_json",

			"device_token_snapshot",

			"estado",

			"programada_at",

			"enviada_at",

			"entregada_at",

			"abierta_at",

			"fallida_at",

			"provider_message_id",

			"error_code",

			"error_message",

			"idempotencia",

			"prioridad",

			"origen_evento",

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

			"user_id",

			"viaje_id",

			"canal",

			"proveedor",

			"titulo",

			"cuerpo",

			"data_json",

			"device_token_snapshot",

			"estado",

			"programada_at",

			"enviada_at",

			"entregada_at",

			"abierta_at",

			"fallida_at",

			"provider_message_id",

			"error_code",

			"error_message",

			"idempotencia",

			"prioridad",

			"origen_evento",

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

			"user_id",

			"viaje_id",

			"canal",

			"proveedor",

			"titulo",

			"cuerpo",

			"data_json",

			"device_token_snapshot",

			"estado",

			"programada_at",

			"enviada_at",

			"entregada_at",

			"abierta_at",

			"fallida_at",

			"provider_message_id",

			"error_code",

			"error_message",

			"idempotencia",

			"prioridad",

			"origen_evento",

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

			"user_id",

			"viaje_id",

			"canal",

			"proveedor",

			"titulo",

			"cuerpo",

			"data_json",

			"device_token_snapshot",

			"estado",

			"programada_at",

			"enviada_at",

			"entregada_at",

			"abierta_at",

			"fallida_at",

			"provider_message_id",

			"error_code",

			"error_message",

			"idempotencia",

			"prioridad",

			"origen_evento",

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

			"user_id",

			"viaje_id",

			"canal",

			"proveedor",

			"titulo",

			"cuerpo",

			"data_json",

			"device_token_snapshot",

			"estado",

			"programada_at",

			"enviada_at",

			"entregada_at",

			"abierta_at",

			"fallida_at",

			"provider_message_id",

			"error_code",

			"error_message",

			"idempotencia",

			"prioridad",

			"origen_evento" 
		];
	}
}
