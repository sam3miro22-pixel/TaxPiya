<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificacionesAddRequest extends FormRequest
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
				"viaje_id" => "nullable",
				"canal" => "required",
				"proveedor" => "nullable",
				"titulo" => "nullable|string",
				"cuerpo" => "nullable|string",
				"data_json" => "nullable",
				"device_token_snapshot" => "nullable|string",
				"estado" => "required",
				"programada_at" => "nullable|date",
				"enviada_at" => "nullable|date",
				"entregada_at" => "nullable|date",
				"abierta_at" => "nullable|date",
				"fallida_at" => "nullable|date",
				"provider_message_id" => "nullable|string",
				"error_code" => "nullable|string",
				"error_message" => "nullable|string",
				"idempotencia" => "nullable|string",
				"prioridad" => "required",
				"origen_evento" => "nullable|string",
            
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
