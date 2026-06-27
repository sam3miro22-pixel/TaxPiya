<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TarifasAddRequest;
use App\Http\Requests\TarifasEditRequest;
use App\Models\Tarifas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TarifasListExport;
use App\Exports\TarifasViewExport;
use Illuminate\Support\Facades\Validator;
use Exception;
class TarifasController extends Controller
{
	
	public function fija(Request $request)
    {
        $categoria = $request->query('categoria', 'taxi');
        $ciudad    = $request->query('ciudad');
        $tarifa = \App\Services\TariffCalculator::findActiveTariff($categoria, $ciudad);

        if (!$tarifa) {
            return response()->json(['ok' => false, 'message' => 'No hay tarifa activa'], 404);
        }

        $oLat = $request->filled('o_lat') ? (float) $request->query('o_lat') : null;
        $oLng = $request->filled('o_lng') ? (float) $request->query('o_lng') : null;
        $dLat = $request->filled('d_lat') ? (float) $request->query('d_lat') : null;
        $dLng = $request->filled('d_lng') ? (float) $request->query('d_lng') : null;

        $fare = \App\Services\TariffCalculator::calculate($tarifa, $oLat, $oLng, $dLat, $dLng);

        return response()->json([
            'ok'        => true,
            'tarifa_id' => (int) $tarifa->id,
            'nombre'    => $tarifa->nombre,
            'ciudad'    => $tarifa->ciudad,
            'categoria' => $tarifa->categoria,
            'moneda'    => $tarifa->moneda,
            'monto'     => $fare['monto'],
            'km'        => $fare['km'],
            'desglose'  => $fare['desglose'],
            'monto_fijo'=> (float) $tarifa->monto_fijo,
            'tarifa_por_km' => (float) ($tarifa->tarifa_por_km ?? 0),
        ]);
    }


	/**
     * List table records
	 * @param  \Illuminate\Http\Request
     * @param string $fieldname //filter records by a table field
     * @param string $fieldvalue //filter value
     * @return \Illuminate\View\View
     */
	function index(Request $request, $fieldname = null , $fieldvalue = null){
		$view = "pages.tarifas.list";

		$query = Tarifas::query();
		$limit = $request->limit ?? 10;
		if($request->search){
			$search = trim($request->search);
			Tarifas::search($query, $search); // search table records
		}
		$orderby = $request->orderby ?? "tarifas.id";
		$ordertype = $request->ordertype ?? "desc";
		$query->orderBy($orderby, $ordertype);
		if($fieldname){
			$query->where($fieldname , $fieldvalue); //filter by a table field
		}
		// if request format is for export example:- product/index?export=pdf
		if($this->getExportFormat()){
			return $this->ExportList($query, $request); // export current query
		}

		$records = $query->paginate($limit, Tarifas::listFields());
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
		Tarifas::insert($modeldata);
		return $this->redirect(url()->previous(), "Datos importados con éxito");
	}
	

	/**
     * Select table record by ID
	 * @param string $rec_id
     * @return \Illuminate\View\View
     */
	function view(Request $request, $rec_id = null){
		$query = Tarifas::query();
		// if request format is for export example:- product/view/344?export=pdf
		if($this->getExportFormat()){
			return $this->ExportView($query, $rec_id, $request);
		}

		$record = $query->findOrFail($rec_id, Tarifas::viewFields());
		return $this->renderView("pages.tarifas.view", ["data" => $record]);
	}
	

	/**
     * Display Master Detail Pages
	 * @param string $rec_id //master record id
     * @return \Illuminate\View\View
     */
	function masterDetail($rec_id = null){
		return View("pages.tarifas.detail-pages", ["masterRecordId" => $rec_id]);
	}
	

	/**
     * Display form page
     * @return \Illuminate\View\View
     */
	function add(){
		return $this->renderView("pages.tarifas.add");
	}
	

	/**
     * Save form record to the table
     * @return \Illuminate\Http\Response
     */
	function store(TarifasAddRequest $request){
		$modeldata = $this->normalizeFormData($request->validated());
		
		//save Tarifas record
		$record = Tarifas::create($modeldata);
		$rec_id = $record->id;
		return $this->redirect("tarifas", "Grabar agregado exitosamente");
	}
	

	/**
     * Update table record with form data
	 * @param string $rec_id //select record by table primary key
     * @return \Illuminate\View\View;
     */
	function edit(TarifasEditRequest $request, $rec_id = null){
		$query = Tarifas::query();
		$record = $query->findOrFail($rec_id, Tarifas::editFields());
		if ($request->isMethod('post')) {
			$modeldata = $this->normalizeFormData($request->validated());
			$record->update($modeldata);
			return $this->redirect("tarifas", "Registro actualizado con éxito");
		}
		return $this->renderView("pages.tarifas.edit", ["data" => $record, "rec_id" => $rec_id]);
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
		$query = Tarifas::query();
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
		$filename = "ListTarifasReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$records = $query->get(Tarifas::exportListFields());
			return view("reports.tarifas-list", ["records" => $records]);
		}
		elseif($format == "pdf"){
			$records = $query->get(Tarifas::exportListFields());
			$pdf = PDF::loadView("reports.tarifas-list", ["records" => $records]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new TarifasListExport($query), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new TarifasListExport($query), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
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
		$filename ="ViewTarifasReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$record = $query->findOrFail($rec_id, Tarifas::exportViewFields());
			return view("reports.tarifas-view", ["record" => $record]);
		}
		elseif($format == "pdf"){
			$record = $query->findOrFail($rec_id, Tarifas::exportViewFields());
			$pdf = PDF::loadView("reports.tarifas-view", ["record" => $record]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new TarifasViewExport($query, $rec_id), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new TarifasViewExport($query, $rec_id), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
}
