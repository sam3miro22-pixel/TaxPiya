<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Http\Requests\UsersRegisterRequest;

use App\Providers\RouteServiceProvider;
use App\Services\Firebase\FirebaseIdentityService;
use App\Services\Firebase\FirestoreUserService;
use App\Services\ReferralService;
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
		$remember = $request->boolean('rememberme', (bool) config('taxpiya.session.remember_default', true));
		$app      = $request->input('app'); // 'pasajero' | 'conductor' | null

		$credentials = filter_var($username, FILTER_VALIDATE_EMAIL)
			? ['email' => $username, 'password' => $password]
			: ['telefono' => $username, 'password' => $password];

		$loggedIn = Auth::attempt($credentials, $remember);

		if (!$loggedIn && filter_var($username, FILTER_VALIDATE_EMAIL)) {
			$loggedIn = $this->attemptFirebasePasswordLogin($username, $password, $remember, $app);
		}

		if (!$loggedIn) {
			return back()
				->withErrors('Nombre de usuario o contraseña no correctos')
				->withInput($request->only('username'));
		}

		// Seguridad: regenerar la sesión tras autenticación
		$request->session()->regenerate();

		$user = Auth::user();

		$gateError = $this->validateLoginGate($user, $app);
		if ($gateError) {
			Auth::logout();
			$request->session()->invalidate();
			$request->session()->regenerateToken();

			return back()
				->withErrors($gateError)
				->withInput($request->only('username'));
		}

		$destination = RouteServiceProvider::homeForUser($user, $app);
		return $this->redirectIntended($destination, 'Inicio de sesión completado');
	}

	private function attemptFirebasePasswordLogin(string $email, string $password, bool $remember, ?string $app): bool
	{
		$firebase = app(FirebaseIdentityService::class);
		if (!$firebase->isConfigured()) {
			return false;
		}

		try {
			$identity = $firebase->signInWithPassword($email, $password);
		} catch (\Throwable $e) {
			return false;
		}

		$accounts = app(\App\Services\UserAccountService::class);
		$user = $accounts->findByFirebaseIdentity($identity['localId'], $email, null);

		if (!$user) {
			$user = Users::create([
				'firebase_uid' => $identity['localId'],
				'name'         => explode('@', $email)[0],
				'email'        => $email,
				'telefono'     => 'fb_' . substr($identity['localId'], 0, 12),
				'password'     => bcrypt(\Illuminate\Support\Str::random(32)),
				'estado'       => 1,
			]);
			$user->assignRole($app === 'conductor' ? 'Conductor' : 'Pasajero');
		} else {
			$accounts->linkFirebaseUid($user, $identity['localId']);
		}

		Auth::login($user, $remember);

		try {
			app(FirestoreUserService::class)->upsertFromUser($user, $app ?: 'pasajero');
		} catch (\Throwable $e) {
			report($e);
		}

		return true;
	}

	private function validateLoginGate($user, ?string $app): ?string
	{
		if ((int) ($user->estado ?? 1) !== 1) {
			return 'Tu cuenta está inactiva. Por favor comunícate con el Equipo de Taxpiya.';
		}

		if ($app === 'conductor') {
			if (!$user->hasRole('Conductor')) {
				return 'Acceso exclusivo para Conductores.';
			}
			$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
			if (!$conductor || (int) ($conductor->estado_operitivo ?? 0) !== 1) {
				return 'Tu cuenta de conductor no está activa. Comunícate con el Equipo de Taxpiya.';
			}

			DB::table('conductores')
				->where('id', (int) $conductor->id)
				->update([
					'disponible' => 0,
					'updated_at' => now()->format('Y-m-d H:i:s'),
				]);
		} elseif ($app === 'pasajero') {
			if (!$user->hasRole('Pasajero')) {
				return 'Este acceso es solo para Pasajeros.';
			}
		} elseif ($app === 'empresa') {
			if (!$user->hasRole('Empresa')) {
				return 'Acceso exclusivo para empresas afiliadas.';
			}
			$empresa = DB::table('empresas')->where('user_id', $user->id)->first();
			if (!$empresa) {
				return 'Tu cuenta no tiene una empresa vinculada.';
			}
		}

		return null;
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

		if ($goConductor && $user) {
			$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
			if ($conductor) {
				DB::table('conductores')
					->where('id', (int) $conductor->id)
					->update([
						'disponible' => 0,
						'updated_at' => now()->format('Y-m-d H:i:s'),
					]);
			}
		}

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
		$referralCode = $request->input('codigo_referido');
		unset($modeldata['codigo_referido']);
		$referrals = app(ReferralService::class);
		if ($referrals->normalizeCode($referralCode)) {
			$check = $referrals->validateCode($referralCode);
			if (!$check['ok']) {
				return back()->withErrors($check['message'] ?? 'Código de referido inválido')->withInput($request->except('password'));
			}
		}
		$plainPassword = $request->input('password');
		
		if ($request->hasFile('fotoperfil_file')) {
			try {
				$modeldata['fotoperfil'] = app(\App\Services\ProfilePhotoService::class)
					->store($request->file('fotoperfil_file'));
			} catch (\Throwable $e) {
				return back()->withErrors($e->getMessage())->withInput($request->except('password'));
			}
		} elseif (array_key_exists('fotoperfil', $modeldata) && !empty($modeldata['fotoperfil'])) {
			$fileInfo = $this->moveUploadedFiles($modeldata['fotoperfil'], 'fotoperfil');
			$modeldata['fotoperfil'] = $fileInfo['filepath'];
		} else {
			unset($modeldata['fotoperfil']);
		}

		$firebase = app(FirebaseIdentityService::class);
		if ($firebase->isConfigured() && !empty($modeldata['email'])) {
			try {
				$identity = $firebase->signUp($modeldata['email'], $plainPassword);
				$modeldata['firebase_uid'] = $identity['localId'];
			} catch (\Throwable $e) {
				return back()
					->withErrors($e->getMessage())
					->withInput($request->except('password'));
			}
		}

		$modeldata['password'] = bcrypt($plainPassword);
		
		//save Users record
		$user = $record = Users::create($modeldata);
		$user->assignRole("Pasajero"); //set default role for user

		$referrals->ensureUserCode($user);
		$referrals->registerReferral($referralCode, (int) $user->id, 'pasajero');

		try {
			app(\App\Services\WalletLedgerService::class)->ensureCuenta('pasajero', (int) $user->id);
		} catch (\Throwable $e) {
			report($e);
		}

		try {
			app(FirestoreUserService::class)->upsertFromUser($user, 'pasajero');
		} catch (\Throwable $e) {
			report($e);
		}

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

	function accountinactive(Request $request){
		return view("pages.index.accountinactive");
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
