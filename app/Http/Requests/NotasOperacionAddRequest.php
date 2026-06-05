<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotasOperacionAddRequest extends FormRequest
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
            
				"entity_type" => "required",
				"entity_id" => "required|numeric",
				"viaje_id" => "nullable",
				"conductor_id" => "nullable",
				"user_id" => "nullable",
				"titulo" => "nullable|string",
				"nota" => "required",
				"adjunto_url" => "nullable|string",
				"adjunto_mime" => "nullable|string",
				"visibilidad" => "required",
				"pinned" => "required|numeric",
				"created_by" => "required",
				"created_by_rol" => "required",
            
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
