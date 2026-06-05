<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ChatMensajes extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'chat_mensajes';
	

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
		'viaje_id','remitente_id','remitente_rol','tipo','mensaje','media_url','media_tipo','reply_to_id','lat','lng','leido_por_pasajero_at','leido_por_conductor_at','moderado','moderado_motivo','ip'
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
				mensaje LIKE ?  OR 
				media_url LIKE ?  OR 
				media_tipo LIKE ?  OR 
				moderado_motivo LIKE ?  OR 
				ip LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"remitente_id",

			"remitente_rol",

			"tipo",

			"mensaje",

			"media_url",

			"media_tipo",

			"reply_to_id",

			"lat",

			"lng",

			"leido_por_pasajero_at",

			"leido_por_conductor_at",

			"moderado",

			"moderado_motivo",

			"ip",

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

			"remitente_id",

			"remitente_rol",

			"tipo",

			"mensaje",

			"media_url",

			"media_tipo",

			"reply_to_id",

			"lat",

			"lng",

			"leido_por_pasajero_at",

			"leido_por_conductor_at",

			"moderado",

			"moderado_motivo",

			"ip",

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

			"remitente_id",

			"remitente_rol",

			"tipo",

			"mensaje",

			"media_url",

			"media_tipo",

			"reply_to_id",

			"lat",

			"lng",

			"leido_por_pasajero_at",

			"leido_por_conductor_at",

			"moderado",

			"moderado_motivo",

			"ip",

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

			"remitente_id",

			"remitente_rol",

			"tipo",

			"mensaje",

			"media_url",

			"media_tipo",

			"reply_to_id",

			"lat",

			"lng",

			"leido_por_pasajero_at",

			"leido_por_conductor_at",

			"moderado",

			"moderado_motivo",

			"ip",

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

			"remitente_id",

			"remitente_rol",

			"tipo",

			"mensaje",

			"media_url",

			"media_tipo",

			"reply_to_id",

			"lat",

			"lng",

			"leido_por_pasajero_at",

			"leido_por_conductor_at",

			"moderado",

			"moderado_motivo",

			"ip" 
		];
	}
}
