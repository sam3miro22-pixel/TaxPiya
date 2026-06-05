<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class UsuarioDispositivos extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'usuario_dispositivos';
	

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
		'user_id','device_uuid','plataforma','app_version','os_version','idioma','zona_horaria','fabricante','modelo','notificaciones_activas','activo','is_emulador','root_jailbreak','installed_at','last_seen_at','last_ip'
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
				device_uuid LIKE ?  OR 
				app_version LIKE ?  OR 
				os_version LIKE ?  OR 
				idioma LIKE ?  OR 
				zona_horaria LIKE ?  OR 
				fabricante LIKE ?  OR 
				modelo LIKE ?  OR 
				last_ip LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"device_uuid",

			"plataforma",

			"app_version",

			"os_version",

			"idioma",

			"zona_horaria",

			"fabricante",

			"modelo",

			"notificaciones_activas",

			"activo",

			"is_emulador",

			"root_jailbreak",

			"installed_at",

			"last_seen_at",

			"last_ip",

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

			"device_uuid",

			"plataforma",

			"app_version",

			"os_version",

			"idioma",

			"zona_horaria",

			"fabricante",

			"modelo",

			"notificaciones_activas",

			"activo",

			"is_emulador",

			"root_jailbreak",

			"installed_at",

			"last_seen_at",

			"last_ip",

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

			"device_uuid",

			"plataforma",

			"app_version",

			"os_version",

			"idioma",

			"zona_horaria",

			"fabricante",

			"modelo",

			"notificaciones_activas",

			"activo",

			"is_emulador",

			"root_jailbreak",

			"installed_at",

			"last_seen_at",

			"last_ip",

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

			"device_uuid",

			"plataforma",

			"app_version",

			"os_version",

			"idioma",

			"zona_horaria",

			"fabricante",

			"modelo",

			"notificaciones_activas",

			"activo",

			"is_emulador",

			"root_jailbreak",

			"installed_at",

			"last_seen_at",

			"last_ip",

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

			"device_uuid",

			"plataforma",

			"app_version",

			"os_version",

			"idioma",

			"zona_horaria",

			"fabricante",

			"modelo",

			"notificaciones_activas",

			"activo",

			"is_emulador",

			"root_jailbreak",

			"installed_at",

			"last_seen_at",

			"last_ip" 
		];
	}
}
