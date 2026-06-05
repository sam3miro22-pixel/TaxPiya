<?php 

namespace App\Exports;
use App\Models\Viajes;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ViajesViewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	protected $query;

	protected $rec_id;

    public function __construct($query, $rec_id)
    {
        $this->query = $query->select(Viajes::exportViewFields());
        $this->rec_id = $rec_id;
    }


    public function query()
    {
        return $this->query->where("id", $this->rec_id);
    }


	public function headings(): array
    {
        return [
			'Id',
			'Pasajero Id',
			'Conductor Id',
			'Vehiculo Id',
			'Origen Lat',
			'Origen Lng',
			'Origen Ubicacion',
			'Origen Texto',
			'Destino Lat',
			'Destino Lng',
			'Destino Ubicacion',
			'Destino Texto',
			'Estado',
			'Created At',
			'Asignado At',
			'Aceptar Hasta',
			'Aceptado At',
			'En Camino At',
			'Llego At',
			'Iniciado At',
			'Terminado At',
			'Cancelado At',
			'Cancelado Por',
			'Cancelacion Motivo',
			'Metodo Asignacion',
			'Radio Busqueda M',
			'Eta Min Estimada',
			'Distancia Km Estimada',
			'Duracion Min Estimada',
			'Distancia Km Real',
			'Duracion Min Real',
			'Tarifa Id',
			'Moneda',
			'Tarifa Aplicada',
			'Valor Pagado',
			'Pago Registrado',
			'Updated At'
        ];
    }


    public function map($record): array
    {
        return [
			$record->id,
			$record->pasajero_id,
			$record->conductor_id,
			$record->vehiculo_id,
			$record->origen_lat,
			$record->origen_lng,
			$record->origen_ubicacion,
			$record->origen_texto,
			$record->destino_lat,
			$record->destino_lng,
			$record->destino_ubicacion,
			$record->destino_texto,
			$record->estado,
			$record->created_at,
			$record->asignado_at,
			$record->aceptar_hasta,
			$record->aceptado_at,
			$record->en_camino_at,
			$record->llego_at,
			$record->iniciado_at,
			$record->terminado_at,
			$record->cancelado_at,
			$record->cancelado_por,
			$record->cancelacion_motivo,
			$record->metodo_asignacion,
			$record->radio_busqueda_m,
			$record->eta_min_estimada,
			$record->distancia_km_estimada,
			$record->duracion_min_estimada,
			$record->distancia_km_real,
			$record->duracion_min_real,
			$record->tarifa_id,
			$record->moneda,
			$record->tarifa_aplicada,
			$record->valor_pagado,
			$record->pago_registrado,
			$record->updated_at
        ];
    }
}
