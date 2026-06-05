<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class WalletSaldos extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'wallet_saldos';
	

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
		'conductor_id','saldo_actual','saldo_reservado','min_operativo','moneda','last_movimiento_id','last_movimiento_at','bloqueado','motivo_bloqueo'
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
				conductor_id LIKE ?  OR 
				moneda LIKE ?  OR 
				motivo_bloqueo LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%"
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

			"saldo_actual",

			"saldo_reservado",

			"min_operativo",

			"moneda",

			"last_movimiento_id",

			"last_movimiento_at",

			"bloqueado",

			"motivo_bloqueo",

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
			"conductor_id",

			"saldo_actual",

			"saldo_reservado",

			"min_operativo",

			"moneda",

			"last_movimiento_id",

			"last_movimiento_at",

			"bloqueado",

			"motivo_bloqueo",

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
			"conductor_id",

			"saldo_actual",

			"saldo_reservado",

			"min_operativo",

			"moneda",

			"last_movimiento_id",

			"last_movimiento_at",

			"bloqueado",

			"motivo_bloqueo",

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
			"conductor_id",

			"saldo_actual",

			"saldo_reservado",

			"min_operativo",

			"moneda",

			"last_movimiento_id",

			"last_movimiento_at",

			"bloqueado",

			"motivo_bloqueo",

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
			"conductor_id",

			"saldo_actual",

			"saldo_reservado",

			"min_operativo",

			"moneda",

			"last_movimiento_id",

			"last_movimiento_at",

			"bloqueado",

			"motivo_bloqueo" 
		];
	}
}
