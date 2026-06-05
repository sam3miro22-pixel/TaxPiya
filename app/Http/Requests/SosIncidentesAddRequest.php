<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SosIncidentesAddRequest extends FormRequest
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
            
				"viaje_id" => "nullable",
				"actor_tipo" => "required",
				"actor_user_id" => "nullable",
				"conductor_id" => "nullable",
				"categoria" => "nullable",
				"severidad" => "required",
				"estado" => "required",
				"descripcion" => "nullable",
				"telefono_contacto" => "nullable|string",
				"lat" => "nullable|numeric",
				"lng" => "nullable|numeric",
				"ubicacion" => "nullable|numeric",
				"operador_id" => "nullable",
				"asignado_at" => "nullable|date",
				"reconocido_at" => "nullable|date",
				"atendido_at" => "nullable|date",
				"resuelto_at" => "nullable|date",
				"cerrado_at" => "nullable|date",
				"nivel_escalamiento" => "nullable|numeric",
				"sla_minutos" => "nullable|numeric",
				"breach_at" => "nullable|date",
				"contacto_inicial" => "nullable",
				"contacto_resultado" => "nullable",
				"evidencia_url" => "nullable|string",
				"notas_operacion" => "nullable",
            
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
