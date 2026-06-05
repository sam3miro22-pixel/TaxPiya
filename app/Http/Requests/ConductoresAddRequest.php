<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConductoresAddRequest extends FormRequest
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
            
				"user_id" => "required",
				"estado_operitivo" => "required|numeric",
				"disponible" => "required|numeric",
				"last_online_at" => "nullable|date",
				"rating_promedio" => "nullable|numeric",
				"total_viajes" => "required|numeric",
				"licencia_numero" => "nullable|string",
				"licencia_categoria" => "nullable|string",
				"licencia_expira" => "nullable|date",
				"soat_numero" => "nullable|string",
				"soat_expira" => "nullable|date",
				"seguro_numero" => "nullable|string",
				"verificacion_estado" => "required",
				"verificacion_nivel" => "nullable|numeric",
				"verificacion_notas" => "nullable",
				"contacto_emergencia_nombre" => "nullable|string",
				"contacto_emergencia_telefono" => "nullable|string",
				"location_permission" => "nullable",
            
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
