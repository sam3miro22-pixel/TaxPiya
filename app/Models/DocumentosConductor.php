<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class DocumentosConductor extends Model 
{
	

	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'documentos_conductor';
	

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
		'conductor_id','tipo','numero','emisor','expedido_at','expira_at','archivo_url','archivo_mime','archivo_size_kb','hash_sha256','estado_verificacion','verificado_por','verificado_at','rechazo_motivo','notas'
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
				numero LIKE ?  OR 
				emisor LIKE ?  OR 
				archivo_url LIKE ?  OR 
				archivo_mime LIKE ?  OR 
				hash_sha256 LIKE ?  OR 
				rechazo_motivo LIKE ?  OR 
				notas LIKE ? 
		)';
		$search_params = [
			"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
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

			"tipo",

			"numero",

			"emisor",

			"expedido_at",

			"expira_at",

			"archivo_url",

			"archivo_mime",

			"archivo_size_kb",

			"hash_sha256",

			"estado_verificacion",

			"verificado_por",

			"verificado_at",

			"rechazo_motivo",

			"notas",

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

			"conductor_id",

			"tipo",

			"numero",

			"emisor",

			"expedido_at",

			"expira_at",

			"archivo_url",

			"archivo_mime",

			"archivo_size_kb",

			"hash_sha256",

			"estado_verificacion",

			"verificado_por",

			"verificado_at",

			"rechazo_motivo",

			"notas",

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

			"conductor_id",

			"tipo",

			"numero",

			"emisor",

			"expedido_at",

			"expira_at",

			"archivo_url",

			"archivo_mime",

			"archivo_size_kb",

			"hash_sha256",

			"estado_verificacion",

			"verificado_por",

			"verificado_at",

			"rechazo_motivo",

			"notas",

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

			"conductor_id",

			"tipo",

			"numero",

			"emisor",

			"expedido_at",

			"expira_at",

			"archivo_url",

			"archivo_mime",

			"archivo_size_kb",

			"hash_sha256",

			"estado_verificacion",

			"verificado_por",

			"verificado_at",

			"rechazo_motivo",

			"notas",

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

			"conductor_id",

			"tipo",

			"numero",

			"emisor",

			"expedido_at",

			"expira_at",

			"archivo_url",

			"archivo_mime",

			"archivo_size_kb",

			"hash_sha256",

			"estado_verificacion",

			"verificado_por",

			"verificado_at",

			"rechazo_motivo",

			"notas" 
		];
	}
}
