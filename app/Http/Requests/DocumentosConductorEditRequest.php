<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentosConductorEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		
        return [
            
				"conductor_id" => "filled",
				"tipo" => "filled",
				"numero" => "nullable|string",
				"emisor" => "nullable|string",
				"expedido_at" => "nullable|date",
				"expira_at" => "nullable|date",
				"archivo_url" => "nullable|string",
				"archivo_mime" => "nullable|string",
				"archivo_size_kb" => "nullable|numeric",
				"hash_sha256" => "nullable|string",
				"estado_verificacion" => "filled",
				"verificado_por" => "nullable",
				"verificado_at" => "nullable|date",
				"rechazo_motivo" => "nullable|string",
				"notas" => "nullable",
            
        ];
    }

	public function messages()
    {
        return [
			
            //using laravel default validation messages
        ];
    }

    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [
            //eg = 'name' => 'trim|capitalize|escape'
        ];
    }
}
