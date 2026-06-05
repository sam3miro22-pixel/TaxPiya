<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConductorPosicionActualAddRequest extends FormRequest
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
            
				"conductor_id" => "required",
				"viaje_id" => "nullable",
				"lat" => "required|numeric",
				"lng" => "required|numeric",
				"ubicacion" => "required|numeric",
				"precision_m" => "nullable|numeric",
				"velocidad_kmh" => "nullable|numeric",
				"heading" => "nullable|numeric",
				"origen" => "nullable",
				"provider" => "nullable",
				"bateria" => "nullable|numeric",
				"app_estado" => "nullable",
            
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
