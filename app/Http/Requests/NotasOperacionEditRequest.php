<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotasOperacionEditRequest extends FormRequest
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
            
				"entity_type" => "filled",
				"entity_id" => "filled|numeric",
				"viaje_id" => "nullable",
				"conductor_id" => "nullable",
				"user_id" => "nullable",
				"titulo" => "nullable|string",
				"nota" => "filled",
				"adjunto_url" => "nullable|string",
				"adjunto_mime" => "nullable|string",
				"visibilidad" => "filled",
				"pinned" => "filled|numeric",
				"created_by" => "filled",
				"created_by_rol" => "filled",
            
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
