<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMensajesAddRequest extends FormRequest
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
				"remitente_id" => "required",
				"remitente_rol" => "required",
				"tipo" => "required",
				"mensaje" => "nullable",
				"media_url" => "nullable|string",
				"media_tipo" => "nullable|string",
				"reply_to_id" => "nullable",
				"lat" => "nullable|numeric",
				"lng" => "nullable|numeric",
				"leido_por_pasajero_at" => "nullable|date",
				"leido_por_conductor_at" => "nullable|date",
				"moderado" => "required|numeric",
				"moderado_motivo" => "nullable|string",
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
