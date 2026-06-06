<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
/**
 * Home Page Controller
 * @category  Controller
 */
class HomeController extends Controller{
	/**
     * Index Action
     * @return \Illuminate\View\View
     */
	function index(){
		$user = auth()->user();
		if($user->hasRole('admin')){
			return view("pages.home.admin");
		}
		elseif($user->hasRole('pasajero')){
			return view("pages.home.pasajero");
		}
		elseif($user->hasRole('conductor')){
			return view("pages.home.conductor");
		}
		else{
			return view("pages.home.index");
		}
	}

	function pasajeroPerfil(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Pasajero')) {
			return redirect()->route('home');
		}
		return view('pages.pasajero.perfil', ['user' => $user]);
	}

	function pasajeroViajes(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Pasajero')) {
			return redirect()->route('home');
		}
		$viajes = DB::table('viajes')
			->where('pasajero_id', $user->id)
			->orderByDesc('id')
			->limit(50)
			->get();
		return view('pages.pasajero.viajes', ['viajes' => $viajes]);
	}

	function conductorCuenta(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Conductor')) {
			return redirect()->route('home');
		}
		$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
		$vehiculo = $conductor
			? DB::table('vehiculos')->where('conductor_id', $conductor->id)->first()
			: null;
		return view('pages.conductor.cuenta', compact('user', 'conductor', 'vehiculo'));
	}
	
}
