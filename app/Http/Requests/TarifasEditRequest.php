<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifasEditRequest extends FormRequest
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
            
				"nombre" => "filled|string",
				"descripcion" => "nullable|string",
				"scope" => "filled",
				"ciudad" => "nullable|string",
				"categoria" => "filled",
				"horario" => "filled",
				"origen_ref" => "nullable|string",
				"destino_ref" => "nullable|string",
				"moneda" => "filled|string",
				"monto_fijo" => "filled|numeric",
				"recargo_nocturno" => "nullable|numeric",
				"recargo_festivo" => "nullable|numeric",
				"recargo_aeropuerto" => "nullable|numeric",
				"incluye_peajes" => "filled|numeric",
				"minutos_espera_incluidos" => "nullable|numeric",
				"valor_minuto_espera" => "nullable|numeric",
				"vigente_desde" => "filled|date",
				"vigente_hasta" => "nullable|date",
				"activa" => "filled|numeric",
				"prioridad" => "filled|numeric",
				"version" => "filled|numeric",
            
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
