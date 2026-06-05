<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletMovimientosAddRequest extends FormRequest
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
            
				"conductor_id" => "required",
				"viaje_id" => "nullable",
				"admin_user_id" => "nullable",
				"sentido" => "required",
				"motivo" => "required",
				"monto" => "required|numeric",
				"moneda" => "required|string",
				"saldo_antes" => "nullable|numeric",
				"saldo_despues" => "nullable|numeric",
				"descripcion" => "nullable|string",
				"referencia_externa" => "nullable|string",
				"idempotencia" => "nullable|string",
				"anulado" => "required|numeric",
				"anulado_por" => "nullable",
				"anulado_motivo" => "nullable|string",
				"anulado_at" => "nullable|date",
            
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
