<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class SosIncidentes extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'sos_incidentes';
	

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
		'viaje_id','actor_tipo','actor_user_id','conductor_id','categoria','severidad','estado','descripcion','telefono_contacto','lat','lng','ubicacion','operador_id','asignado_at','reconocido_at','atendido_at','resuelto_at','cerrado_at','nivel_escalamiento','sla_minutos','breach_at','contacto_inicial','contacto_resultado','evidencia_url','notas_operacion'
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
				descripcion LIKE ?  OR 
				telefono_contacto LIKE ?  OR 
				evidencia_url LIKE ?  OR 
				notas_operacion LIKE ? 
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

			"viaje_id",

			"actor_tipo",

			"actor_user_id",

			"conductor_id",

			"categoria",

			"severidad",

			"estado",

			"descripcion",

			"telefono_contacto",

			"lat",

			"lng",

			"ubicacion",

			"operador_id",

			"asignado_at",

			"reconocido_at",

			"atendido_at",

			"resuelto_at",

			"cerrado_at",

			"nivel_escalamiento",

			"sla_minutos",

			"breach_at",

			"contacto_inicial",

			"contacto_resultado",

			"evidencia_url",

			"notas_operacion",

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

			"actor_tipo",

			"actor_user_id",

			"conductor_id",

			"categoria",

			"severidad",

			"estado",

			"descripcion",

			"telefono_contacto",

			"lat",

			"lng",

			"ubicacion",

			"operador_id",

			"asignado_at",

			"reconocido_at",

			"atendido_at",

			"resuelto_at",

			"cerrado_at",

			"nivel_escalamiento",

			"sla_minutos",

			"breach_at",

			"contacto_inicial",

			"contacto_resultado",

			"evidencia_url",

			"notas_operacion",

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

			"actor_tipo",

			"actor_user_id",

			"conductor_id",

			"categoria",

			"severidad",

			"estado",

			"descripcion",

			"telefono_contacto",

			"lat",

			"lng",

			"ubicacion",

			"operador_id",

			"asignado_at",

			"reconocido_at",

			"atendido_at",

			"resuelto_at",

			"cerrado_at",

			"nivel_escalamiento",

			"sla_minutos",

			"breach_at",

			"contacto_inicial",

			"contacto_resultado",

			"evidencia_url",

			"notas_operacion",

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

			"actor_tipo",

			"actor_user_id",

			"conductor_id",

			"categoria",

			"severidad",

			"estado",

			"descripcion",

			"telefono_contacto",

			"lat",

			"lng",

			"ubicacion",

			"operador_id",

			"asignado_at",

			"reconocido_at",

			"atendido_at",

			"resuelto_at",

			"cerrado_at",

			"nivel_escalamiento",

			"sla_minutos",

			"breach_at",

			"contacto_inicial",

			"contacto_resultado",

			"evidencia_url",

			"notas_operacion",

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

			"actor_tipo",

			"actor_user_id",

			"conductor_id",

			"categoria",

			"severidad",

			"estado",

			"descripcion",

			"telefono_contacto",

			"lat",

			"lng",

			"ubicacion",

			"operador_id",

			"asignado_at",

			"reconocido_at",

			"atendido_at",

			"resuelto_at",

			"cerrado_at",

			"nivel_escalamiento",

			"sla_minutos",

			"breach_at",

			"contacto_inicial",

			"contacto_resultado",

			"evidencia_url",

			"notas_operacion" 
		];
	}
}
