<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ConductorPosicionActual extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'conductor_posicion_actual';
	

	/**
     * The table primary key field
     *
     * @var string
     */
	protected $primaryKey = 'conductor_id';

	public $incrementing = false;
	

	/**
     * Table fillable fields
     *
     * @var array
     */
	protected $fillable = [
		'conductor_id','viaje_id','lat','lng','ubicacion','precision_m','velocidad_kmh','heading','origen','provider','bateria','app_estado'
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
				conductor_id LIKE ? 
		)';
		$search_params = [
			"%$text%"
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
			"conductor_id",

			"viaje_id",

			"lat",

			"lng",

			"ubicacion",

			"precision_m",

			"velocidad_kmh",

			"heading",

			"origen",

			"provider",

			"bateria",

			"app_estado",

			"created_at",

			"actualizada_at" 
		];
	}

	

	/**
     * return exportList page fields of the model.
     * 
     * @return array
     */
	public static function exportListFields(){
		return [ 
			"conductor_id",

			"viaje_id",

			"lat",

			"lng",

			"ubicacion",

			"precision_m",

			"velocidad_kmh",

			"heading",

			"origen",

			"provider",

			"bateria",

			"app_estado",

			"created_at",

			"actualizada_at" 
		];
	}

	

	/**
     * return view page fields of the model.
     * 
     * @return array
     */
	public static function viewFields(){
		return [ 
			"conductor_id",

			"viaje_id",

			"lat",

			"lng",

			"ubicacion",

			"precision_m",

			"velocidad_kmh",

			"heading",

			"origen",

			"provider",

			"bateria",

			"app_estado",

			"created_at",

			"actualizada_at" 
		];
	}

	

	/**
     * return exportView page fields of the model.
     * 
     * @return array
     */
	public static function exportViewFields(){
		return [ 
			"conductor_id",

			"viaje_id",

			"lat",

			"lng",

			"ubicacion",

			"precision_m",

			"velocidad_kmh",

			"heading",

			"origen",

			"provider",

			"bateria",

			"app_estado",

			"created_at",

			"actualizada_at" 
		];
	}

	

	/**
     * return edit page fields of the model.
     * 
     * @return array
     */
	public static function editFields(){
		return [ 
			"conductor_id",

			"viaje_id",

			"lat",

			"lng",

			"ubicacion",

			"precision_m",

			"velocidad_kmh",

			"heading",

			"origen",

			"provider",

			"bateria",

			"app_estado" 
		];
	}
}
