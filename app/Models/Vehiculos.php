<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Vehiculos extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'vehiculos';
	

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
		'conductor_id','placa','vin','marca','linea','modelo_anio','color','categoria','asientos','soat_numero','soat_expira','tecnomecanica_expira','seguro_extracontractual_numero','seguro_extracontractual_expira','estado_vehiculo','verificacion_estado','verificacion_notas','foto_principal'
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
				placa LIKE ?  OR 
				vin LIKE ?  OR 
				marca LIKE ?  OR 
				linea LIKE ?  OR 
				color LIKE ?  OR 
				soat_numero LIKE ?  OR 
				seguro_extracontractual_numero LIKE ?  OR 
				verificacion_notas LIKE ?  OR 
				foto_principal LIKE ? 
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

			"conductor_id",

			"placa",

			"vin",

			"marca",

			"linea",

			"modelo_anio",

			"color",

			"categoria",

			"asientos",

			"soat_numero",

			"soat_expira",

			"tecnomecanica_expira",

			"seguro_extracontractual_numero",

			"seguro_extracontractual_expira",

			"estado_vehiculo",

			"verificacion_estado",

			"verificacion_notas",

			"foto_principal",

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

			"conductor_id",

			"placa",

			"vin",

			"marca",

			"linea",

			"modelo_anio",

			"color",

			"categoria",

			"asientos",

			"soat_numero",

			"soat_expira",

			"tecnomecanica_expira",

			"seguro_extracontractual_numero",

			"seguro_extracontractual_expira",

			"estado_vehiculo",

			"verificacion_estado",

			"verificacion_notas",

			"foto_principal",

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

			"conductor_id",

			"placa",

			"vin",

			"marca",

			"linea",

			"modelo_anio",

			"color",

			"categoria",

			"asientos",

			"soat_numero",

			"soat_expira",

			"tecnomecanica_expira",

			"seguro_extracontractual_numero",

			"seguro_extracontractual_expira",

			"estado_vehiculo",

			"verificacion_estado",

			"verificacion_notas",

			"foto_principal",

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

			"conductor_id",

			"placa",

			"vin",

			"marca",

			"linea",

			"modelo_anio",

			"color",

			"categoria",

			"asientos",

			"soat_numero",

			"soat_expira",

			"tecnomecanica_expira",

			"seguro_extracontractual_numero",

			"seguro_extracontractual_expira",

			"estado_vehiculo",

			"verificacion_estado",

			"verificacion_notas",

			"foto_principal",

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

			"conductor_id",

			"placa",

			"vin",

			"marca",

			"linea",

			"modelo_anio",

			"color",

			"categoria",

			"asientos",

			"soat_numero",

			"soat_expira",

			"tecnomecanica_expira",

			"seguro_extracontractual_numero",

			"seguro_extracontractual_expira",

			"estado_vehiculo",

			"verificacion_estado",

			"verificacion_notas",

			"foto_principal" 
		];
	}
}
