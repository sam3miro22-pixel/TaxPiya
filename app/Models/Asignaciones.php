<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Asignaciones extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'asignaciones';
	

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
		'viaje_id','conductor_id','estado','expira_at','respondido_at','motivo_rechazo','distancia_m','eta_min','radio_usado_m','metodo','intento','prioridad'
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
				motivo_rechazo LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%"
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

			"conductor_id",

			"estado",

			"ofertado_at",

			"expira_at",

			"respondido_at",

			"motivo_rechazo",

			"distancia_m",

			"eta_min",

			"radio_usado_m",

			"metodo",

			"intento",

			"prioridad",

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

			"conductor_id",

			"estado",

			"ofertado_at",

			"expira_at",

			"respondido_at",

			"motivo_rechazo",

			"distancia_m",

			"eta_min",

			"radio_usado_m",

			"metodo",

			"intento",

			"prioridad",

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

			"conductor_id",

			"estado",

			"ofertado_at",

			"expira_at",

			"respondido_at",

			"motivo_rechazo",

			"distancia_m",

			"eta_min",

			"radio_usado_m",

			"metodo",

			"intento",

			"prioridad",

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

			"conductor_id",

			"estado",

			"ofertado_at",

			"expira_at",

			"respondido_at",

			"motivo_rechazo",

			"distancia_m",

			"eta_min",

			"radio_usado_m",

			"metodo",

			"intento",

			"prioridad",

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

			"conductor_id",

			"estado",

			"expira_at",

			"respondido_at",

			"motivo_rechazo",

			"distancia_m",

			"eta_min",

			"radio_usado_m",

			"metodo",

			"intento",

			"prioridad" 
		];
	}
}
