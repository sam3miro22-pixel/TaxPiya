<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LlamadasAddRequest extends FormRequest
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
				"llamador_user_id" => "required",
				"llamador_rol" => "required",
				"receptor_user_id" => "nullable",
				"receptor_rol" => "nullable",
				"tipo" => "required",
				"provider" => "required",
				"provider_call_id" => "nullable|string",
				"provider_room_id" => "nullable|string",
				"caller_phone_snapshot" => "nullable|string",
				"callee_phone_snapshot" => "nullable|string",
				"proxy_number" => "nullable|string",
				"masked" => "required|numeric",
				"estado" => "required",
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
