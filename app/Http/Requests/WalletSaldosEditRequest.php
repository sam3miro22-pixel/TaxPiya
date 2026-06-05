<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletSaldosEditRequest extends FormRequest
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
            
				"conductor_id" => "filled",
				"saldo_actual" => "filled|numeric",
				"saldo_reservado" => "filled|numeric",
				"min_operativo" => "filled|numeric",
				"moneda" => "filled|string",
				"last_movimiento_id" => "nullable",
				"last_movimiento_at" => "nullable|date",
				"bloqueado" => "filled|numeric",
				"motivo_bloqueo" => "nullable|string",
            
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
