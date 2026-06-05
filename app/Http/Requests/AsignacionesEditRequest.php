<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignacionesEditRequest extends FormRequest
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
            
				"viaje_id" => "filled",
				"conductor_id" => "filled",
				"estado" => "filled",
				"expira_at" => "filled|date",
				"respondido_at" => "nullable|date",
				"motivo_rechazo" => "nullable|string",
				"distancia_m" => "nullable|numeric",
				"eta_min" => "nullable|numeric",
				"radio_usado_m" => "nullable|numeric",
				"metodo" => "filled",
				"intento" => "filled|numeric",
				"prioridad" => "filled|numeric",
            
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
