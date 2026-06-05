<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Viajes extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'viajes';
	

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
		'pasajero_id','conductor_id','vehiculo_id','origen_lat','origen_lng','origen_ubicacion','origen_texto','destino_lat','destino_lng','destino_ubicacion','destino_texto','estado','asignado_at','aceptar_hasta','aceptado_at','en_camino_at','llego_at','iniciado_at','terminado_at','cancelado_at','cancelado_por','cancelacion_motivo','metodo_asignacion','radio_busqueda_m','eta_min_estimada','distancia_km_estimada','duracion_min_estimada','distancia_km_real','duracion_min_real','tarifa_id','moneda','tarifa_aplicada','valor_pagado','pago_registrado'
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
				origen_texto LIKE ?  OR 
				destino_texto LIKE ?  OR 
				cancelacion_motivo LIKE ?  OR 
				moneda LIKE ? 
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

			"pasajero_id",

			"conductor_id",

			"vehiculo_id",

			"origen_lat",

			"origen_lng",

			"origen_ubicacion",

			"origen_texto",

			"destino_lat",

			"destino_lng",

			"destino_ubicacion",

			"destino_texto",

			"estado",

			"created_at",

			"asignado_at",

			"aceptar_hasta",

			"aceptado_at",

			"en_camino_at",

			"llego_at",

			"iniciado_at",

			"terminado_at",

			"cancelado_at",

			"cancelado_por",

			"cancelacion_motivo",

			"metodo_asignacion",

			"radio_busqueda_m",

			"eta_min_estimada",

			"distancia_km_estimada",

			"duracion_min_estimada",

			"distancia_km_real",

			"duracion_min_real",

			"tarifa_id",

			"moneda",

			"tarifa_aplicada",

			"valor_pagado",

			"pago_registrado",

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

			"pasajero_id",

			"conductor_id",

			"vehiculo_id",

			"origen_lat",

			"origen_lng",

			"origen_ubicacion",

			"origen_texto",

			"destino_lat",

			"destino_lng",

			"destino_ubicacion",

			"destino_texto",

			"estado",

			"created_at",

			"asignado_at",

			"aceptar_hasta",

			"aceptado_at",

			"en_camino_at",

			"llego_at",

			"iniciado_at",

			"terminado_at",

			"cancelado_at",

			"cancelado_por",

			"cancelacion_motivo",

			"metodo_asignacion",

			"radio_busqueda_m",

			"eta_min_estimada",

			"distancia_km_estimada",

			"duracion_min_estimada",

			"distancia_km_real",

			"duracion_min_real",

			"tarifa_id",

			"moneda",

			"tarifa_aplicada",

			"valor_pagado",

			"pago_registrado",

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

			"pasajero_id",

			"conductor_id",

			"vehiculo_id",

			"origen_lat",

			"origen_lng",

			"origen_ubicacion",

			"origen_texto",

			"destino_lat",

			"destino_lng",

			"destino_ubicacion",

			"destino_texto",

			"estado",

			"created_at",

			"asignado_at",

			"aceptar_hasta",

			"aceptado_at",

			"en_camino_at",

			"llego_at",

			"iniciado_at",

			"terminado_at",

			"cancelado_at",

			"cancelado_por",

			"cancelacion_motivo",

			"metodo_asignacion",

			"radio_busqueda_m",

			"eta_min_estimada",

			"distancia_km_estimada",

			"duracion_min_estimada",

			"distancia_km_real",

			"duracion_min_real",

			"tarifa_id",

			"moneda",

			"tarifa_aplicada",

			"valor_pagado",

			"pago_registrado",

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

			"pasajero_id",

			"conductor_id",

			"vehiculo_id",

			"origen_lat",

			"origen_lng",

			"origen_ubicacion",

			"origen_texto",

			"destino_lat",

			"destino_lng",

			"destino_ubicacion",

			"destino_texto",

			"estado",

			"created_at",

			"asignado_at",

			"aceptar_hasta",

			"aceptado_at",

			"en_camino_at",

			"llego_at",

			"iniciado_at",

			"terminado_at",

			"cancelado_at",

			"cancelado_por",

			"cancelacion_motivo",

			"metodo_asignacion",

			"radio_busqueda_m",

			"eta_min_estimada",

			"distancia_km_estimada",

			"duracion_min_estimada",

			"distancia_km_real",

			"duracion_min_real",

			"tarifa_id",

			"moneda",

			"tarifa_aplicada",

			"valor_pagado",

			"pago_registrado",

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

			"pasajero_id",

			"conductor_id",

			"vehiculo_id",

			"origen_lat",

			"origen_lng",

			"origen_ubicacion",

			"origen_texto",

			"destino_lat",

			"destino_lng",

			"destino_ubicacion",

			"destino_texto",

			"estado",

			"asignado_at",

			"aceptar_hasta",

			"aceptado_at",

			"en_camino_at",

			"llego_at",

			"iniciado_at",

			"terminado_at",

			"cancelado_at",

			"cancelado_por",

			"cancelacion_motivo",

			"metodo_asignacion",

			"radio_busqueda_m",

			"eta_min_estimada",

			"distancia_km_estimada",

			"duracion_min_estimada",

			"distancia_km_real",

			"duracion_min_real",

			"tarifa_id",

			"moneda",

			"tarifa_aplicada",

			"valor_pagado",

			"pago_registrado" 
		];
	}
}
