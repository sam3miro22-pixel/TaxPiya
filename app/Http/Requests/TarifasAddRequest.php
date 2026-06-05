<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifasAddRequest extends FormRequest
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
            
				"nombre" => "required|string",
				"descripcion" => "nullable|string",
				"scope" => "required",
				"ciudad" => "nullable|string",
				"categoria" => "required",
				"horario" => "required",
				"origen_ref" => "nullable|string",
				"destino_ref" => "nullable|string",
				"moneda" => "required|string",
				"monto_fijo" => "required|numeric",
				"recargo_nocturno" => "nullable|numeric",
				"recargo_festivo" => "nullable|numeric",
				"recargo_aeropuerto" => "nullable|numeric",
				"incluye_peajes" => "required|numeric",
				"minutos_espera_incluidos" => "nullable|numeric",
				"valor_minuto_espera" => "nullable|numeric",
				"vigente_desde" => "required|date",
				"vigente_hasta" => "nullable|date",
				"activa" => "required|numeric",
				"prioridad" => "required|numeric",
				"version" => "required|numeric",
            
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
