<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignacionesAddRequest extends FormRequest
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
				"conductor_id" => "required",
				"estado" => "required",
				"expira_at" => "required|date",
				"respondido_at" => "nullable|date",
				"motivo_rechazo" => "nullable|string",
				"distancia_m" => "nullable|numeric",
				"eta_min" => "nullable|numeric",
				"radio_usado_m" => "nullable|numeric",
				"metodo" => "required",
				"intento" => "required|numeric",
				"prioridad" => "required|numeric",
            
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
