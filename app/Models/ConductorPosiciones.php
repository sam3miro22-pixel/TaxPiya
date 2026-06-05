<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ConductorPosiciones extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'conductor_posiciones';
	

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
		'conductor_id','viaje_id','lat','lng','ubicacion','precision_m','velocidad_kmh','heading','origen','provider','bateria','app_estado','received_at'
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
				id LIKE ? 
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
			"id",

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

			"received_at" 
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

			"received_at" 
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

			"received_at" 
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

			"received_at" 
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

			"received_at" 
		];
	}
}
