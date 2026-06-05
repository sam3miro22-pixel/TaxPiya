<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViajesAddRequest extends FormRequest
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
            
				"pasajero_id" => "required",
				"conductor_id" => "nullable",
				"vehiculo_id" => "nullable",
				"origen_lat" => "required|numeric",
				"origen_lng" => "required|numeric",
				"origen_ubicacion" => "required|numeric",
				"origen_texto" => "nullable|string",
				"destino_lat" => "nullable|numeric",
				"destino_lng" => "nullable|numeric",
				"destino_ubicacion" => "nullable|numeric",
				"destino_texto" => "nullable|string",
				"estado" => "required",
				"asignado_at" => "nullable|date",
				"aceptar_hasta" => "nullable|date",
				"aceptado_at" => "nullable|date",
				"en_camino_at" => "nullable|date",
				"llego_at" => "nullable|date",
				"iniciado_at" => "nullable|date",
				"terminado_at" => "nullable|date",
				"cancelado_at" => "nullable|date",
				"cancelado_por" => "nullable",
				"cancelacion_motivo" => "nullable|string",
				"metodo_asignacion" => "required",
				"radio_busqueda_m" => "nullable|numeric",
				"eta_min_estimada" => "nullable|numeric",
				"distancia_km_estimada" => "nullable|numeric",
				"duracion_min_estimada" => "nullable|numeric",
				"distancia_km_real" => "nullable|numeric",
				"duracion_min_real" => "nullable|numeric",
				"tarifa_id" => "nullable",
				"moneda" => "required|string",
				"tarifa_aplicada" => "nullable|numeric",
				"valor_pagado" => "nullable|numeric",
				"pago_registrado" => "required|numeric",
            
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
