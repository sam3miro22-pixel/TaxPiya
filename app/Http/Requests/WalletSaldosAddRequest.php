<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletSaldosAddRequest extends FormRequest
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
				"saldo_actual" => "required|numeric",
				"saldo_reservado" => "required|numeric",
				"min_operativo" => "required|numeric",
				"moneda" => "required|string",
				"last_movimiento_id" => "nullable",
				"last_movimiento_at" => "nullable|date",
				"bloqueado" => "required|numeric",
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
