<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditoriaEventosEditRequest extends FormRequest
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
            
				"actor_user_id" => "nullable",
				"actor_rol" => "filled",
				"origen" => "filled",
				"accion" => "filled",
				"tabla_objetivo" => "filled|string",
				"registro_pk" => "filled|string",
				"detalles" => "nullable|string",
				"viaje_id" => "nullable",
				"conductor_id" => "nullable",
				"before_json" => "nullable",
				"after_json" => "nullable",
				"ip" => "nullable|string",
				"user_agent" => "nullable|string",
				"request_id" => "nullable|string",
            
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
