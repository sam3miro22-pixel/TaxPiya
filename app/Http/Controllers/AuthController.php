<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Http\Requests\UsersRegisterRequest;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller{

	/**
     * Authenticate and login user
     * @return \Illuminate\Http\Response
     */
	public function login(Request $request)
	{
		// Validación mínima y lectura del contexto (app proviene del blade)
		$request->validate([
			'username' => 'required',
			'password' => 'required',
			'app'      => 'nullable|in:pasajero,conductor,empresa',
		]);

		$username = $request->input('username');
		$password = $request->input('password');
		$remember = $request->boolean('rememberme');
		$app      = $request->input('app'); // 'pasajero' | 'conductor' | null

		$credentials = filter_var($username, FILTER_VALIDATE_EMAIL)
			? ['email' => $username, 'password' => $password]
			: ['telefono' => $username, 'password' => $password];

		if (!Auth::attempt($credentials, $remember)) {
			return back()
				->withErrors('Nombre de usuario o contraseña no correctos')
				->withInput($request->only('username'));
		}

		// Seguridad: regenerar la sesión tras autenticación
		$request->session()->regenerate();

		$user = Auth::user();

		// 🚧 VALIDACIÓN DE ESTADO (1 = activo, 2 = inactivo)
		if ((int) ($user->estado ?? 1) !== 1) {
			Auth::logout();
			$request->session()->invalidate();
			$request->session()->regenerateToken();

			return back()
				->withErrors('Tu cuenta está inactiva. Por favor comunícate con el Equipo de Taxpiya.')
				->withInput($request->only('username'));
		}

		// Gateo por rol solo si la vista indicó app concreta
		if ($app === 'conductor') {
			if (!$user->hasRole('Conductor')) {
				Auth::logout();
				$request->session()->invalidate();
				$request->session()->regenerateToken();

				return back()
					->withErrors('Acceso exclusivo para Conductores.')
					->withInput($request->only('username'));
			}
			$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
			if (!$conductor || (int) ($conductor->estado_operitivo ?? 0) !== 1) {
				Auth::logout();
				$request->session()->invalidate();
				$request->session()->regenerateToken();

				return back()
					->withErrors('Tu cuenta de conductor no está activa. Comunícate con el Equipo de Taxpiya.')
					->withInput($request->only('username'));
			}
		}
		elseif ($app === 'pasajero') {
			if (!$user->hasRole('Pasajero')) {
				Auth::logout();
				$request->session()->invalidate();
				$request->session()->regenerateToken();

				return back()
					->withErrors('Este acceso es solo para Pasajeros.')
					->withInput($request->only('username'));
			}
		}
		elseif ($app === 'empresa') {
			if (!$user->hasRole('Empresa')) {
				Auth::logout();
				$request->session()->invalidate();
				$request->session()->regenerateToken();

				return back()
					->withErrors('Acceso exclusivo para empresas afiliadas.')
					->withInput($request->only('username'));
			}
			$empresa = DB::table('empresas')->where('user_id', $user->id)->first();
			if (!$empresa) {
				Auth::logout();
				$request->session()->invalidate();
				$request->session()->regenerateToken();

				return back()
					->withErrors('Tu cuenta no tiene una empresa vinculada.')
					->withInput($request->only('username'));
			}
		}
		// Si 'app' viene null, no se aplica gateo (útil para panel/admin si lo usas aquí).

		$destination = RouteServiceProvider::homeForUser($user, $app);
		return $this->redirectIntended($destination, 'Inicio de sesión completado');
	}

	/**
     * Logout user from session
     * @return \Illuminate\Http\Response
     */
	public function logout(Request $request)
	{
		$user = Auth::user();
		$goPasajero  = $user && $user->hasRole('Pasajero');
		$goConductor = $user && $user->hasRole('Conductor');
		$goEmpresa   = $user && $user->hasRole('Empresa');

		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		if ($goPasajero) {
			return redirect()->route('pasajero.login');
		}
		if ($goConductor) {
			return redirect()->route('conductor.login');
		}
		if ($goEmpresa) {
			return redirect()->route('empresa.login');
		}
		return redirect()->route('index');
	}

	/**
     * Display user registration form
     * @return \Illuminate\View\View
     */
	function register(){
		return view("pages.index.register");
	}

	/**
     * Save new user record
     * @return \Illuminate\Http\Response
     */
	function register_store(UsersRegisterRequest $request){
		$modeldata = $this->normalizeFormData($request->validated());
		
		if( array_key_exists("fotoperfil", $modeldata) ){
			//move uploaded file from temp directory to destination directory
			$fileInfo = $this->moveUploadedFiles($modeldata['fotoperfil'], "fotoperfil");
			$modeldata['fotoperfil'] = $fileInfo['filepath'];
		}
		$modeldata['password'] = bcrypt($modeldata['password']);
		
		//save Users record
		$user = $record = Users::create($modeldata);
		$user->assignRole("Pasajero"); //set default role for user

		$rec_id = $record->id;
		Auth::login($user, true);
		return $this->redirectIntended("/home", "Inicio de sesión completado");
	}

	/**
     * Logout user from session
     * @return \Illuminate\Http\Response
     */
	function accountcreated(Request $request){
		return view("pages.index.accountcreated");
	}

	/**
     * Logout user from session
     * @return \Illuminate\Http\Response
     */
	function accountblocked(Request $request){
		return view("pages.index.accountblocked");
	}

	/**
     * Logout user from session
     * @return \Illuminate\Http\Response
     */
	function accountpending(Request $request){
		return view("pages.index.accountpending");
	}

	/**
     * Display forgot password page
     * @return \Illuminate\View\View
     */
	public function showForgotPassword() {
		return view("pages.passwordreset.forgotpassword");
	}

	/**
     * Display reset password form
     * @return \Illuminate\View\View
     */
	public function showResetPassword() {
		return view("pages.passwordreset.resetpassword");
	}

	/**
     * Display page when password reset link is sent
     * @return \Illuminate\View\View
     */
	public function passwordResetLinkSent() {
		return view("pages.passwordreset.resetlinksent");
	}

	/**
     * Display page when password reset is completed
     * @return \Illuminate\View\View
     */
	public function passwordResetCompleted() {
		return view("pages.passwordreset.resetcompleted");
	}

	/**
     * send password reset link to user email
     * @return \Illuminate\Http\Response
     */
	public function sendPasswordResetLink(Request $request) {

		$validated = $this->validate($request, [
			'email' => "required|email",
		]);

		try{
			$response = Password::sendResetLink($validated);
			return $response == Password::RESET_LINK_SENT
				? $this->sendResetLinkResponse($response)
				: $this->sendResetFailedResponse($request, $response);
		}
		catch (Exception $ex) {
			return $this->sendResetFailedResponse($request, $ex->getMessage());
		}
	}

	/**
     * Reset user password
     * @return \Illuminate\Http\Response
     */
	public function resetPassword(Request $request) {

		$validated = $this->validate($request, [
			'email' => 'required|email',
			'token' => 'required|string',
			"password" => "required|same:confirm_password",
		]);
		$response = Password::reset($validated, function ($user, $password) {
			$user->password = bcrypt($password);
			$user->save();
		});

		return $response == Password::PASSWORD_RESET
			? $this->sendResetResponse($response)
			: $this->sendResetFailedResponse($request, $response);
	}

	/**
     * Get the response for a successful password reset link sent.
     *
     * @param  string  $response
     * @return \Illuminate\Http\Response
     */
	protected function sendResetLinkResponse($response)
	{
		return redirect()->route('password.resetlinksent')->with('status', trans($response));
	}

	/**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\Response
     */
	protected function sendResetResponse($response)
	{
		return redirect()->route('password.resetcompleted')->with('status', trans($response));
	}

	/**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
	protected function sendResetFailedResponse(Request $request, $response)
	{
		return redirect()->back()
			->withInput($request->only('email'))
			->withErrors(['email' => trans($response)]);
	}

}
