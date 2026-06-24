<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;
use App\Http\Requests\UsersAccountEditRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
/**
 * Account Page Controller
 * @category  Controller
 */
class AccountController extends Controller{
	

	/**
     * Select user account data
     * @return \Illuminate\View\View
     */
	function index(){
		$rec_id = Auth::id();
		$query = Users::query();
		$record = $query->find($rec_id, Users::accountviewFields());
		if(!$record){
			return $this->reject("Registro no encontrado", 404);
		}
		return $this->renderView("pages.account.view", ["data" => $record, "rec_id" => $rec_id]);
	}
	

	/**
     * Update user account data
     * @return \Illuminate\View\View;
     */
	function edit(UsersAccountEditRequest $request){
		$rec_id = Auth::id();
		$query = Users::query();
		$user = $query->findOrFail($rec_id, Users::accounteditFields());
		if ($request->isMethod('post')) {
			$modeldata = $this->normalizeFormData($request->validated());
		
		if( array_key_exists("fotoperfil", $modeldata) ){
			//move uploaded file from temp directory to destination directory
			$fileInfo = $this->moveUploadedFiles($modeldata['fotoperfil'], "fotoperfil");
			$modeldata['fotoperfil'] = $fileInfo['filepath'];
		}
			$user->update($modeldata);
			return $this->redirect("account", "Registro actualizado con éxito");
		}
		return $this->renderView("pages.account.edit", ["data" => $user, "rec_id" => $rec_id]);
	}
	

	/**
     * Change user account password
     * @return \Illuminate\Http\Response
     */
	public function changepassword(Request $request)
	{

		$request->validate([
			'oldpassword' => ['required'],
			'newpassword' => ['required'],
			'confirmpassword' => ['same:newpassword'],
		]);

		$userid = auth()->id();
		$user = Users::find($userid);

		$oldPasswordText = $request->oldpassword;
		$oldPasswordHash = $user->password;
		if(!Hash::check($oldPasswordText, $oldPasswordHash)){
			return back()->withErrors(["oldpassword" => "La contraseña actual no es correcta"]);
		}
		$modeldata = ['password' => Hash::make($request->newpassword)];
		$user->update($modeldata);
		return $this->redirect("/account", "Contraseña actualizada correctamente");
	}

}
