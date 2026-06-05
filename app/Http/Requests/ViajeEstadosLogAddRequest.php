<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViajeEstadosLogAddRequest extends FormRequest
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
				"from_estado" => "nullable",
				"to_estado" => "required",
				"actor_tipo" => "required",
				"actor_id" => "nullable",
				"motivo_codigo" => "nullable",
				"motivo_texto" => "nullable|string",
				"app_origen" => "nullable",
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
