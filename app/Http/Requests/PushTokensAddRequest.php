<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushTokensAddRequest extends FormRequest
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
            
				"dispositivo_id" => "required",
				"provider" => "required",
				"token" => "required|string",
				"token_hash" => "nullable|string",
				"estado" => "required",
				"scope" => "required",
				"ultimo_uso_at" => "nullable|date",
				"invalidado_at" => "nullable|date",
				"motivo_invalidez" => "nullable|string",
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
