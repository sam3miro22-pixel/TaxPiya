<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PushTokens extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'push_tokens';
	

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
		'dispositivo_id','provider','token','token_hash','estado','scope','ultimo_uso_at','invalidado_at','motivo_invalidez','idempotencia'
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
				token LIKE ?  OR 
				token_hash LIKE ?  OR 
				motivo_invalidez LIKE ?  OR 
				idempotencia LIKE ? 
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

			"dispositivo_id",

			"provider",

			"token",

			"token_hash",

			"estado",

			"scope",

			"ultimo_uso_at",

			"invalidado_at",

			"motivo_invalidez",

			"idempotencia",

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

			"dispositivo_id",

			"provider",

			"token",

			"token_hash",

			"estado",

			"scope",

			"ultimo_uso_at",

			"invalidado_at",

			"motivo_invalidez",

			"idempotencia",

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

			"dispositivo_id",

			"provider",

			"token",

			"token_hash",

			"estado",

			"scope",

			"ultimo_uso_at",

			"invalidado_at",

			"motivo_invalidez",

			"idempotencia",

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

			"dispositivo_id",

			"provider",

			"token",

			"token_hash",

			"estado",

			"scope",

			"ultimo_uso_at",

			"invalidado_at",

			"motivo_invalidez",

			"idempotencia",

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

			"dispositivo_id",

			"provider",

			"token",

			"token_hash",

			"estado",

			"scope",

			"ultimo_uso_at",

			"invalidado_at",

			"motivo_invalidez",

			"idempotencia" 
		];
	}
}
