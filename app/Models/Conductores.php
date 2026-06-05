<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Conductores extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'conductores';
	

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
		'user_id','estado_operitivo','disponible','last_online_at','rating_promedio','total_viajes','licencia_numero','licencia_categoria','licencia_expira','soat_numero','soat_expira','seguro_numero','verificacion_estado','verificacion_nivel','verificacion_notas','contacto_emergencia_nombre','contacto_emergencia_telefono','location_permission'
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
				licencia_numero LIKE ?  OR 
				licencia_categoria LIKE ?  OR 
				soat_numero LIKE ?  OR 
				seguro_numero LIKE ?  OR 
				verificacion_notas LIKE ?  OR 
				contacto_emergencia_nombre LIKE ?  OR 
				contacto_emergencia_telefono LIKE ? 
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

			"user_id",

			"estado_operitivo",

			"disponible",

			"last_online_at",

			"rating_promedio",

			"total_viajes",

			"licencia_numero",

			"licencia_categoria",

			"licencia_expira",

			"soat_numero",

			"soat_expira",

			"seguro_numero",

			"verificacion_estado",

			"verificacion_nivel",

			"verificacion_notas",

			"contacto_emergencia_nombre",

			"contacto_emergencia_telefono",

			"location_permission",

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

			"user_id",

			"estado_operitivo",

			"disponible",

			"last_online_at",

			"rating_promedio",

			"total_viajes",

			"licencia_numero",

			"licencia_categoria",

			"licencia_expira",

			"soat_numero",

			"soat_expira",

			"seguro_numero",

			"verificacion_estado",

			"verificacion_nivel",

			"verificacion_notas",

			"contacto_emergencia_nombre",

			"contacto_emergencia_telefono",

			"location_permission",

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

			"user_id",

			"estado_operitivo",

			"disponible",

			"last_online_at",

			"rating_promedio",

			"total_viajes",

			"licencia_numero",

			"licencia_categoria",

			"licencia_expira",

			"soat_numero",

			"soat_expira",

			"seguro_numero",

			"verificacion_estado",

			"verificacion_nivel",

			"verificacion_notas",

			"contacto_emergencia_nombre",

			"contacto_emergencia_telefono",

			"location_permission",

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

			"user_id",

			"estado_operitivo",

			"disponible",

			"last_online_at",

			"rating_promedio",

			"total_viajes",

			"licencia_numero",

			"licencia_categoria",

			"licencia_expira",

			"soat_numero",

			"soat_expira",

			"seguro_numero",

			"verificacion_estado",

			"verificacion_nivel",

			"verificacion_notas",

			"contacto_emergencia_nombre",

			"contacto_emergencia_telefono",

			"location_permission",

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

			"user_id",

			"estado_operitivo",

			"disponible",

			"last_online_at",

			"rating_promedio",

			"total_viajes",

			"licencia_numero",

			"licencia_categoria",

			"licencia_expira",

			"soat_numero",

			"soat_expira",

			"seguro_numero",

			"verificacion_estado",

			"verificacion_nivel",

			"verificacion_notas",

			"contacto_emergencia_nombre",

			"contacto_emergencia_telefono",

			"location_permission" 
		];
	}
}
