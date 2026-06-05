<?php 
namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/**
 * Components data Model
 * Use for getting values from the database for page components
 * Support raw query builder
 * @category Model
 */
class ComponentsData{
	

	/**
     * viaje_id_option_list Model Action
     * @return array
     */
	function viaje_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM viajes";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * conductor_id_option_list Model Action
     * @return array
     */
	function conductor_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM conductores";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * actor_user_id_option_list Model Action
     * @return array
     */
	function actor_user_id_option_list(){
		$sqltext = "SELECT id as value, name as label FROM users";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * reply_to_id_option_list Model Action
     * @return array
     */
	function reply_to_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM chat_mensajes";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * dispositivo_id_option_list Model Action
     * @return array
     */
	function dispositivo_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM usuario_dispositivos";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * role_id_option_list Model Action
     * @return array
     */
	function role_id_option_list(){
		$sqltext = "SELECT role_id as value, role_name as label FROM roles";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * Check if value already exist in Users table
	 * @param string $value
     * @return bool
     */
	function users_telefono_value_exist(Request $request){
		$value = trim($request->value);
		$exist = DB::table('users')->where('telefono', $value)->value('telefono');   
		if($exist){
			return true;
		}
		return false;
	}

	

	/**
     * Check if value already exist in Users table
	 * @param string $value
     * @return bool
     */
	function users_name_value_exist(Request $request){
		$value = trim($request->value);
		$exist = DB::table('users')->where('name', $value)->value('name');   
		if($exist){
			return true;
		}
		return false;
	}

	

	/**
     * Check if value already exist in Users table
	 * @param string $value
     * @return bool
     */
	function users_email_value_exist(Request $request){
		$value = trim($request->value);
		$exist = DB::table('users')->where('email', $value)->value('email');   
		if($exist){
			return true;
		}
		return false;
	}

	

	/**
     * vehiculo_id_option_list Model Action
     * @return array
     */
	function vehiculo_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM vehiculos";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * tarifa_id_option_list Model Action
     * @return array
     */
	function tarifa_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM tarifas";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}

	

	/**
     * last_movimiento_id_option_list Model Action
     * @return array
     */
	function last_movimiento_id_option_list(){
		$sqltext = "SELECT id as value, id as label FROM wallet_movimientos";
		$query_params = [];
		$arr = DB::select($sqltext, $query_params);
		return $arr;
	}
}
