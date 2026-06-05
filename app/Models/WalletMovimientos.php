<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class WalletMovimientos extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wallet_movimientos';
	

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
		'conductor_id','viaje_id','admin_user_id','sentido','motivo','monto','moneda','saldo_antes','saldo_despues','descripcion','referencia_externa','idempotencia','anulado','anulado_por','anulado_motivo','anulado_at'
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
				moneda LIKE ?  OR 
				descripcion LIKE ?  OR 
				referencia_externa LIKE ?  OR 
				idempotencia LIKE ?  OR 
				anulado_motivo LIKE ? 
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

			"conductor_id",

			"viaje_id",

			"admin_user_id",

			"sentido",

			"motivo",

			"monto",

			"moneda",

			"saldo_antes",

			"saldo_despues",

			"descripcion",

			"referencia_externa",

			"idempotencia",

			"anulado",

			"anulado_por",

			"anulado_motivo",

			"anulado_at",

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

			"conductor_id",

			"viaje_id",

			"admin_user_id",

			"sentido",

			"motivo",

			"monto",

			"moneda",

			"saldo_antes",

			"saldo_despues",

			"descripcion",

			"referencia_externa",

			"idempotencia",

			"anulado",

			"anulado_por",

			"anulado_motivo",

			"anulado_at",

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

			"conductor_id",

			"viaje_id",

			"admin_user_id",

			"sentido",

			"motivo",

			"monto",

			"moneda",

			"saldo_antes",

			"saldo_despues",

			"descripcion",

			"referencia_externa",

			"idempotencia",

			"anulado",

			"anulado_por",

			"anulado_motivo",

			"anulado_at",

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

			"conductor_id",

			"viaje_id",

			"admin_user_id",

			"sentido",

			"motivo",

			"monto",

			"moneda",

			"saldo_antes",

			"saldo_despues",

			"descripcion",

			"referencia_externa",

			"idempotencia",

			"anulado",

			"anulado_por",

			"anulado_motivo",

			"anulado_at",

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

			"conductor_id",

			"viaje_id",

			"admin_user_id",

			"sentido",

			"motivo",

			"monto",

			"moneda",

			"saldo_antes",

			"saldo_despues",

			"descripcion",

			"referencia_externa",

			"idempotencia",

			"anulado",

			"anulado_por",

			"anulado_motivo",

			"anulado_at" 
		];
	}
}
