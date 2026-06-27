<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Tarifas extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'tarifas';
	

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
		'nombre','descripcion','scope','ciudad','categoria','horario','origen_ref','destino_ref','moneda','monto_fijo','tarifa_base','tarifa_por_km','tarifa_minima','recargo_nocturno','recargo_festivo','recargo_aeropuerto','incluye_peajes','minutos_espera_incluidos','valor_minuto_espera','vigente_desde','vigente_hasta','activa','prioridad','version'
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
				nombre LIKE ?  OR 
				descripcion LIKE ?  OR 
				ciudad LIKE ?  OR 
				origen_ref LIKE ?  OR 
				destino_ref LIKE ?  OR 
				moneda LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"nombre",

			"descripcion",

			"scope",

			"ciudad",

			"categoria",

			"horario",

			"origen_ref",

			"destino_ref",

			"moneda",

			"monto_fijo",

			"recargo_nocturno",

			"recargo_festivo",

			"recargo_aeropuerto",

			"incluye_peajes",

			"minutos_espera_incluidos",

			"valor_minuto_espera",

			"vigente_desde",

			"vigente_hasta",

			"activa",

			"prioridad",

			"version",

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

			"nombre",

			"descripcion",

			"scope",

			"ciudad",

			"categoria",

			"horario",

			"origen_ref",

			"destino_ref",

			"moneda",

			"monto_fijo",

			"recargo_nocturno",

			"recargo_festivo",

			"recargo_aeropuerto",

			"incluye_peajes",

			"minutos_espera_incluidos",

			"valor_minuto_espera",

			"vigente_desde",

			"vigente_hasta",

			"activa",

			"prioridad",

			"version",

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

			"nombre",

			"descripcion",

			"scope",

			"ciudad",

			"categoria",

			"horario",

			"origen_ref",

			"destino_ref",

			"moneda",

			"monto_fijo",

			"recargo_nocturno",

			"recargo_festivo",

			"recargo_aeropuerto",

			"incluye_peajes",

			"minutos_espera_incluidos",

			"valor_minuto_espera",

			"vigente_desde",

			"vigente_hasta",

			"activa",

			"prioridad",

			"version",

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

			"nombre",

			"descripcion",

			"scope",

			"ciudad",

			"categoria",

			"horario",

			"origen_ref",

			"destino_ref",

			"moneda",

			"monto_fijo",

			"recargo_nocturno",

			"recargo_festivo",

			"recargo_aeropuerto",

			"incluye_peajes",

			"minutos_espera_incluidos",

			"valor_minuto_espera",

			"vigente_desde",

			"vigente_hasta",

			"activa",

			"prioridad",

			"version",

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

			"nombre",

			"descripcion",

			"scope",

			"ciudad",

			"categoria",

			"horario",

			"origen_ref",

			"destino_ref",

			"moneda",

			"monto_fijo",

			"recargo_nocturno",

			"recargo_festivo",

			"recargo_aeropuerto",

			"incluye_peajes",

			"minutos_espera_incluidos",

			"valor_minuto_espera",

			"vigente_desde",

			"vigente_hasta",

			"activa",

			"prioridad",

			"version" 
		];
	}
}
