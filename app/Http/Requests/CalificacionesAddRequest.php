<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalificacionesAddRequest extends FormRequest
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
            
				"viaje_id" => "required",
				"rater_id" => "required",
				"rater_rol" => "required",
				"ratee_id" => "required",
				"ratee_rol" => "required",
				"puntuacion" => "required|numeric",
				"comentario" => "nullable|string",
				"etiquetas_json" => "nullable",
				"visible" => "required|numeric",
				"moderado" => "required|numeric",
				"moderado_motivo" => "nullable|string",
				"ip" => "nullable|string",
            
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
