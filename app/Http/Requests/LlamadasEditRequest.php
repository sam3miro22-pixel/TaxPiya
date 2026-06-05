<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LlamadasEditRequest extends FormRequest
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
				"llamador_user_id" => "filled",
				"llamador_rol" => "filled",
				"receptor_user_id" => "nullable",
				"receptor_rol" => "nullable",
				"tipo" => "filled",
				"provider" => "filled",
				"provider_call_id" => "nullable|string",
				"provider_room_id" => "nullable|string",
				"caller_phone_snapshot" => "nullable|string",
				"callee_phone_snapshot" => "nullable|string",
				"proxy_number" => "nullable|string",
				"masked" => "filled|numeric",
				"estado" => "filled",
				"ring_start_at" => "nullable|date",
				"connected_at" => "nullable|date",
				"ended_at" => "nullable|date",
				"duracion_seg" => "nullable|numeric",
				"dispositivo_id" => "nullable",
				"ip" => "nullable|string",
				"idempotencia" => "nullable|string",
            
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
