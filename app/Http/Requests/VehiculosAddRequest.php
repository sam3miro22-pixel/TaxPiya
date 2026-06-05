<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehiculosAddRequest extends FormRequest
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
            
				"conductor_id" => "nullable",
				"placa" => "required|string",
				"vin" => "nullable|string",
				"marca" => "nullable|string",
				"linea" => "nullable|string",
				"modelo_anio" => "nullable|numeric",
				"color" => "nullable|string",
				"categoria" => "required",
				"asientos" => "nullable|numeric",
				"soat_numero" => "nullable|string",
				"soat_expira" => "nullable|date",
				"tecnomecanica_expira" => "nullable|date",
				"seguro_extracontractual_numero" => "nullable|string",
				"seguro_extracontractual_expira" => "nullable|date",
				"estado_vehiculo" => "required",
				"verificacion_estado" => "required",
				"verificacion_notas" => "nullable",
				"foto_principal" => "nullable|string",
            
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
