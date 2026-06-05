<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioDispositivosAddRequest extends FormRequest
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
				"device_uuid" => "required|string",
				"plataforma" => "required",
				"app_version" => "nullable|string",
				"os_version" => "nullable|string",
				"idioma" => "nullable|string",
				"zona_horaria" => "nullable|string",
				"fabricante" => "nullable|string",
				"modelo" => "nullable|string",
				"notificaciones_activas" => "required|numeric",
				"activo" => "required|numeric",
				"is_emulador" => "required|numeric",
				"root_jailbreak" => "required|numeric",
				"installed_at" => "nullable|date",
				"last_seen_at" => "nullable|date",
				"last_ip" => "nullable|string",
            
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
