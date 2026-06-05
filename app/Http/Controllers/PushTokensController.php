<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\PushTokensAddRequest;
use App\Http\Requests\PushTokensEditRequest;
use App\Models\PushTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use \PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PushtokensListExport;
use App\Exports\PushtokensViewExport;
use Illuminate\Support\Facades\Validator;
use Exception;
class PushTokensController extends Controller
{
	 public function register(Request $req)
    {
      
        $uid = auth()->id();
        if (!$uid) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

       
        $data = $req->validate([
            'provider'     => 'nullable|in:fcm,apns,webpush',
            'token'        => 'required|string|max:255',
            'device_uuid'  => 'nullable|string|max:128',
            'plataforma'   => 'nullable|in:android,ios,web',
            'scope'        => 'nullable|in:prod,dev,test',
            'is_emulator'  => 'nullable|boolean',
        ]);

        $provider   = $data['provider']   ?? 'fcm';
        $token      = $data['token'];
        $deviceUuid = $data['device_uuid'] ?: Str::uuid()->toString();
        $platform   = $data['plataforma'] ?? 'android';
        $scope      = config('services.fcm.scope', env('FCM_SCOPE', 'dev'));
        $now        = Carbon::now();

        
        $disp = DB::table('usuario_dispositivos')
            ->where('user_id', $uid)
            ->where('device_uuid', $deviceUuid)
            ->where('plataforma', $platform)
            ->first();

        if (!$disp) {
            $dispId = DB::table('usuario_dispositivos')->insertGetId([
                'user_id'              => $uid,
                'device_uuid'          => $deviceUuid,
                'plataforma'           => $platform,
                'idioma'               => 'es-CO',
                'notificaciones_activas' => 1,
                'activo'               => 1,
                'is_emulador'          => !empty($data['is_emulator']) ? 1 : 0,
                'installed_at'         => $now,
                'last_seen_at'         => $now,
                'last_ip'              => $req->ip(),
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        } else {
            $dispId = $disp->id;
            DB::table('usuario_dispositivos')
                ->where('id', $dispId)
                ->update([
                    'notificaciones_activas' => 1,
                    'last_seen_at'           => $now,
                    'last_ip'                => $req->ip(),
                    'updated_at'             => $now,
                ]);
        }

       
        $hash = hash('sha256', $token);

        $existing = DB::table('push_tokens')->where('token', $token)->first();

        if ($existing) {
            DB::table('push_tokens')->where('id', $existing->id)->update([
                'dispositivo_id' => $dispId,
                'provider'       => $provider,
                'token_hash'     => $hash,
                'estado'         => 'valido',
                'scope'          => $scope,
                'ultimo_uso_at'  => $now,
                'invalidado_at'  => null,
                'motivo_invalidez' => null,
                'updated_at'     => $now,
            ]);
            $pushId = $existing->id;
        } else {
            $pushId = DB::table('push_tokens')->insertGetId([
                'dispositivo_id' => $dispId,
                'provider'       => $provider,
                'token'          => $token,
                'token_hash'     => $hash,
                'estado'         => 'valido',
                'scope'          => $scope,
                'ultimo_uso_at'  => $now,
                'idempotencia'   => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        return response()->json([
            'ok' => true,
            'push_token_id' => $pushId,
            'dispositivo_id' => $dispId,
            'scope' => $scope,
        ]);
    }

	
	function index(Request $request, $fieldname = null , $fieldvalue = null){
		$view = "pages.pushtokens.list";

		$query = PushTokens::query();
		$limit = $request->limit ?? 10;
		if($request->search){
			$search = trim($request->search);
			PushTokens::search($query, $search); 
		}
		$orderby = $request->orderby ?? "push_tokens.id";
		$ordertype = $request->ordertype ?? "desc";
		$query->orderBy($orderby, $ordertype);
		if($fieldname){
			$query->where($fieldname , $fieldvalue); 
		}
		
		if($this->getExportFormat()){
			return $this->ExportList($query, $request);
		}

		$records = $query->paginate($limit, PushTokens::listFields());
		return $this->renderView($view, compact("records"));
	}
	

	/**
     * Import csv file data into a table 
     * @return data
     */
	function importdata(Request $request){
		$importSettings = config("upload.import");
		$maxFileSize = intval($importSettings["max_file_size"]) * 1000; //in kilobyte
		$validator = Validator::make($request->all(), 
			[
				"file" => "file|required|max:$maxFileSize|mimes:csv,txt",
			]
		);
		if ($validator->fails()) {
			return back()->withErrors($validator->errors());
		}
		$csvOptions = array(
			'fields' => '', //leave empty to use the first row as the columns
			'delimiter' => ',', 
			'quote' => '"'
		);
		$filePath = $request->file('file')->getRealPath();
		$modeldata = parse_csv_file($filePath, $csvOptions);
		PushTokens::insert($modeldata);
		return $this->redirect(url()->previous(), "Datos importados con éxito");
	}
	

	/**
     * Select table record by ID
	 * @param string $rec_id
     * @return \Illuminate\View\View
     */
	function view(Request $request, $rec_id = null){
		$query = PushTokens::query();
		// if request format is for export example:- product/view/344?export=pdf
		if($this->getExportFormat()){
			return $this->ExportView($query, $rec_id, $request);
		}

		$record = $query->findOrFail($rec_id, PushTokens::viewFields());
		return $this->renderView("pages.pushtokens.view", ["data" => $record]);
	}
	

	/**
     * Display form page
     * @return \Illuminate\View\View
     */
	function add(){
		return $this->renderView("pages.pushtokens.add");
	}
	

	/**
     * Save form record to the table
     * @return \Illuminate\Http\Response
     */
	function store(PushTokensAddRequest $request){
		$modeldata = $this->normalizeFormData($request->validated());
		
		//save PushTokens record
		$record = PushTokens::create($modeldata);
		$rec_id = $record->id;
		return $this->redirect("pushtokens", "Grabar agregado exitosamente");
	}
	

	/**
     * Update table record with form data
	 * @param string $rec_id //select record by table primary key
     * @return \Illuminate\View\View;
     */
	function edit(PushTokensEditRequest $request, $rec_id = null){
		$query = PushTokens::query();
		$record = $query->findOrFail($rec_id, PushTokens::editFields());
		if ($request->isMethod('post')) {
			$modeldata = $this->normalizeFormData($request->validated());
			$record->update($modeldata);
			return $this->redirect("pushtokens", "Registro actualizado con éxito");
		}
		return $this->renderView("pages.pushtokens.edit", ["data" => $record, "rec_id" => $rec_id]);
	}
	

	/**
     * Delete record from the database
	 * Support multi delete by separating record id by comma.
	 * @param  \Illuminate\Http\Request
	 * @param string $rec_id //can be separated by comma 
     * @return \Illuminate\Http\Response
     */
	function delete(Request $request, $rec_id = null){
		$arr_id = explode(",", $rec_id);
		$query = PushTokens::query();
		$query->whereIn("id", $arr_id);
		$query->delete();
		$redirectUrl = $request->redirect ?? url()->previous();
		return $this->redirect($redirectUrl, "Grabar eliminado con éxito");
	}
	

	/**
     * Export table records to different format
	 * supported format:- PDF, CSV, EXCEL, HTML
	 * @request \Illuminate\Http\Request $request
	 * @param \Illuminate\Database\Eloquent\Model $query
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
	private function ExportList($query, $request){
		ob_end_clean(); // clean any output to allow file download
		$filename = "ListPushTokensReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$records = $query->get(PushTokens::exportListFields());
			return view("reports.pushtokens-list", ["records" => $records]);
		}
		elseif($format == "pdf"){
			$records = $query->get(PushTokens::exportListFields());
			$pdf = PDF::loadView("reports.pushtokens-list", ["records" => $records]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new PushtokensListExport($query), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new PushtokensListExport($query), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
	

	/**
     * Export single record to different format
	 * supported format:- PDF, CSV, EXCEL, HTML
	 * @request \Illuminate\Http\Request $request
	 * @param \Illuminate\Database\Eloquent\Model $record
	 * @param string $rec_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
	private function ExportView($query, $rec_id, $request){
		ob_end_clean();// clean any output to allow file download
		$filename ="ViewPushTokensReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$record = $query->findOrFail($rec_id, PushTokens::exportViewFields());
			return view("reports.pushtokens-view", ["record" => $record]);
		}
		elseif($format == "pdf"){
			$record = $query->findOrFail($rec_id, PushTokens::exportViewFields());
			$pdf = PDF::loadView("reports.pushtokens-view", ["record" => $record]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new PushtokensViewExport($query, $rec_id), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new PushtokensViewExport($query, $rec_id), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
}
