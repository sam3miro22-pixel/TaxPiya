<?php
class Menu{

	/* -------- NAV -------- */
	public static function navbarsideleft(){
    return [
        [
            'path' => 'home',
            'label' => "Inicio",
            'icon' => '<i class="fa fa-tachometer"></i>'
        ],
        [
            'path' => 'users',
            'label' => "Usuarios",
            'icon' => '<i class="fa fa-users"></i>'
        ],
        [
            'path' => 'conductores',
            'label' => "Conductores",
            'icon' => '<i class="fa fa-id-card"></i>'
        ],
        [
            'path' => 'empresas',
            'label' => "Empresas",
            'icon' => '<i class="fa fa-building"></i>'
        ],
        [
            'path' => 'referidos',
            'label' => "Referidos",
            'icon' => '<i class="fa fa-gift"></i>'
        ],
        [
            'path' => 'viajes',
            'label' => "Viajes",
            'icon' => '<i class="fa fa-car"></i>'
        ],
        [
            'path' => 'calificaciones',
            'label' => "Calificaciones",
            'icon' => '<i class="fa fa-star"></i>'
        ],
        [
            'path' => 'tarifas',
            'label' => "Tarifas",
            'icon' => '<i class="fa fa-dollar"></i>'
        ],
        [
            'path' => 'walletsolicitudes',
            'label' => "Aprobar recargas",
            'icon' => '<i class="fa fa-check-circle"></i>'
        ],
        [
            'path' => 'walletmovimientos',
            'label' => "Movimientos wallet",
            'icon' => '<i class="fa fa-exchange"></i>'
        ],
        [
            'path' => 'walletsaldos',
            'label' => "Saldos conductores",
            'icon' => '<i class="fa fa-credit-card"></i>'
        ],
        [
            'path' => 'admin/whatsapp',
            'label' => "WhatsApp",
            'icon' => '<i class="fa-brands fa-whatsapp" style="color:#25D366"></i>'
        ],
    ];
}


	/* -------- ASIGNACIONES -------- */
	public static function estado(){
		return [
			['value'=>'pendiente','label'=>'pendiente'],
			['value'=>'aceptado','label'=>'aceptado'],
			['value'=>'rechazado','label'=>'rechazado'],
			['value'=>'expirado','label'=>'expirado'],
			['value'=>'cancelado_sistema','label'=>'cancelado_sistema'],
			['value'=>'saltado','label'=>'saltado'],
		];
	}
	public static function metodo(){
		return [
			['value'=>'auto','label'=>'auto'],
			['value'=>'manual','label'=>'manual'],
		];
	}

	/* -------- AUDITORÍA EVENTOS -------- */
	public static function actorRol(){
		return [
			['value'=>'admin','label'=>'admin'],
			['value'=>'pasajero','label'=>'pasajero'],
			['value'=>'conductor','label'=>'conductor'],
			['value'=>'sistema','label'=>'sistema'],
		];
	}
	public static function origen(){
		return [
			['value'=>'api','label'=>'api'],
			['value'=>'panel','label'=>'panel'],
			['value'=>'app_pasajero','label'=>'app_pasajero'],
			['value'=>'app_conductor','label'=>'app_conductor'],
			['value'=>'sistema','label'=>'sistema'],
			['value'=>'job','label'=>'job'],
		];
	}
	public static function accion(){
		return [
			['value'=>'create','label'=>'create'],
			['value'=>'update','label'=>'update'],
			['value'=>'delete','label'=>'delete'],
			['value'=>'state_change','label'=>'state_change'],
			['value'=>'assign','label'=>'assign'],
			['value'=>'login','label'=>'login'],
			['value'=>'logout','label'=>'logout'],
			['value'=>'ajuste','label'=>'ajuste'],
			['value'=>'notificacion','label'=>'notificacion'],
			['value'=>'sos','label'=>'sos'],
		];
	}

	/* -------- CALIFICACIONES -------- */
	public static function raterRol(){
		return [
			['value'=>'pasajero','label'=>'pasajero'],
			['value'=>'conductor','label'=>'conductor'],
			['value'=>'admin','label'=>'admin'],
		];
	}
	public static function rateeRol(){
		return [
			['value'=>'pasajero','label'=>'pasajero'],
			['value'=>'conductor','label'=>'conductor'],
		];
	}

	/* -------- CHAT -------- */
	public static function remitenteRol(){
		return [
			['value'=>'pasajero','label'=>'pasajero'],
			['value'=>'conductor','label'=>'conductor'],
			['value'=>'admin','label'=>'admin'],
			['value'=>'sistema','label'=>'sistema'],
		];
	}
	public static function tipo(){ /* chat tipo */
		return [
			['value'=>'text','label'=>'text'],
			['value'=>'system','label'=>'system'],
			['value'=>'quick','label'=>'quick'],
			['value'=>'image','label'=>'image'],
			['value'=>'file','label'=>'file'],
			['value'=>'location','label'=>'location'],
		];
	}

	/* -------- VERIFICACIONES / UBICACIÓN -------- */
	public static function verificacionEstado(){
		return [
			['value'=>'pendiente','label'=>'pendiente'],
			['value'=>'verificado','label'=>'verificado'],
			['value'=>'rechazado','label'=>'rechazado'],
		];
	}
	public static function locationPermission(){
		return [
			['value'=>'never','label'=>'never'],
			['value'=>'while_in_use','label'=>'while_in_use'],
			['value'=>'always','label'=>'always'],
		];
	}
	public static function origen2(){ /* posiciones origen */
		return [
			['value'=>'fg','label'=>'fg'],
			['value'=>'bg','label'=>'bg'],
			['value'=>'manual','label'=>'manual'],
			['value'=>'system','label'=>'system'],
		];
	}
	public static function provider(){ /* posiciones provider */
		return [
			['value'=>'gps','label'=>'gps'],
			['value'=>'network','label'=>'network'],
			['value'=>'fused','label'=>'fused'],
			['value'=>'unknown','label'=>'unknown'],
		];
	}
	public static function appEstado(){
		return [
			['value'=>'active','label'=>'active'],
			['value'=>'background','label'=>'background'],
		];
	}

	/* -------- DOCUMENTOS CONDUCTOR -------- */
	public static function documentoTipo(){
		return [
			['value'=>'licencia','label'=>'licencia'],
			['value'=>'soat','label'=>'soat'],
			['value'=>'tecnomecanica','label'=>'tecnomecanica'],
			['value'=>'seguro_extracontractual','label'=>'seguro_extracontractual'],
			['value'=>'antecedentes','label'=>'antecedentes'],
			['value'=>'otro','label'=>'otro'],
		];
	}

	/* -------- LLAMADAS -------- */
	public static function llamadaTipo(){
		return [
			['value'=>'native','label'=>'native'],
			['value'=>'proxy','label'=>'proxy'],
		];
	}
	public static function llamadaProvider(){
		return [
			['value'=>'native','label'=>'native'],
			['value'=>'twilio','label'=>'twilio'],
			['value'=>'vonage','label'=>'vonage'],
			['value'=>'infobip','label'=>'infobip'],
			['value'=>'other','label'=>'other'],
		];
	}
	public static function llamadaEstado(){
		return [
			['value'=>'iniciado','label'=>'iniciado'],
			['value'=>'marcando','label'=>'marcando'],
			['value'=>'conectada','label'=>'conectada'],
			['value'=>'finalizada','label'=>'finalizada'],
			['value'=>'rechazada','label'=>'rechazada'],
			['value'=>'ocupado','label'=>'ocupado'],
			['value'=>'no_contesta','label'=>'no_contesta'],
			['value'=>'fallo','label'=>'fallo'],
			['value'=>'cancelada','label'=>'cancelada'],
		];
	}

	/* -------- NOTAS / ENTIDADES -------- */
	public static function entityType(){
		return [
			['value'=>'viaje','label'=>'viaje'],
			['value'=>'conductor','label'=>'conductor'],
			['value'=>'sos','label'=>'sos'],
			['value'=>'wallet','label'=>'wallet'],
			['value'=>'documento','label'=>'documento'],
			['value'=>'usuario','label'=>'usuario'],
			['value'=>'vehiculo','label'=>'vehiculo'],
			['value'=>'tarifa','label'=>'tarifa'],
			['value'=>'otro','label'=>'otro'],
		];
	}
	public static function visibilidad(){
		return [
			['value'=>'operacion','label'=>'operacion'],
			['value'=>'finanzas','label'=>'finanzas'],
			['value'=>'admin','label'=>'admin'],
			['value'=>'soporte','label'=>'soporte'],
			['value'=>'general','label'=>'general'],
		];
	}
	public static function createdByRol(){
		return [
			['value'=>'admin','label'=>'admin'],
			['value'=>'operador','label'=>'operador'],
			['value'=>'auditor','label'=>'auditor'],
			['value'=>'sistema','label'=>'sistema'],
		];
	}

	/* -------- NOTIFICACIONES -------- */
	public static function canal(){
		return [
			['value'=>'push','label'=>'push'],
			['value'=>'app','label'=>'app'],
			['value'=>'sms','label'=>'sms'],
			['value'=>'email','label'=>'email'],
		];
	}
	public static function proveedor(){ /* notificaciones proveedor */
		return [
			['value'=>'fcm','label'=>'fcm'],
			['value'=>'apns','label'=>'apns'],
			['value'=>'onesignal','label'=>'onesignal'],
			['value'=>'twilio','label'=>'twilio'],
			['value'=>'sendgrid','label'=>'sendgrid'],
			['value'=>'local','label'=>'local'],
		];
	}
	public static function notificacionEstado(){
		return [
			['value'=>'pendiente','label'=>'pendiente'],
			['value'=>'enviado','label'=>'enviado'],
			['value'=>'entregado','label'=>'entregado'],
			['value'=>'abierto','label'=>'abierto'],
			['value'=>'fallido','label'=>'fallido'],
			['value'=>'cancelado','label'=>'cancelado'],
		];
	}
	public static function prioridad(){
		return [
			['value'=>'alta','label'=>'alta'],
			['value'=>'media','label'=>'media'],
			['value'=>'baja','label'=>'baja'],
		];
	}

	/* -------- PUSH TOKENS -------- */
	public static function pushProvider(){
		return [
			['value'=>'fcm','label'=>'fcm'],
			['value'=>'apns','label'=>'apns'],
			['value'=>'webpush','label'=>'webpush'],
		];
	}
	public static function pushTokenEstado(){
		return [
			['value'=>'valido','label'=>'valido'],
			['value'=>'invalido','label'=>'invalido'],
			['value'=>'revocado','label'=>'revocado'],
			['value'=>'expirado','label'=>'expirado'],
		];
	}
	public static function scope(){
		return [
			['value'=>'prod','label'=>'prod'],
			['value'=>'dev','label'=>'dev'],
			['value'=>'test','label'=>'test'],
		];
	}

	/* -------- SOS -------- */
	public static function categoria(){
		return [
			['value'=>'seguridad','label'=>'seguridad'],
			['value'=>'accidente','label'=>'accidente'],
			['value'=>'salud','label'=>'salud'],
			['value'=>'acoso','label'=>'acoso'],
			['value'=>'vehiculo','label'=>'vehiculo'],
			['value'=>'otro','label'=>'otro'],
		];
	}
	public static function sosEstado(){
		return [
			['value'=>'abierto','label'=>'abierto'],
			['value'=>'en_progreso','label'=>'en_progreso'],
			['value'=>'resuelto','label'=>'resuelto'],
			['value'=>'cerrado','label'=>'cerrado'],
			['value'=>'descartado','label'=>'descartado'],
		];
	}
	public static function contactoInicial(){
		return [
			['value'=>'llamada','label'=>'llamada'],
			['value'=>'chat','label'=>'chat'],
			['value'=>'push','label'=>'push'],
			['value'=>'sms','label'=>'sms'],
			['value'=>'otro','label'=>'otro'],
		];
	}
	public static function contactoResultado(){
		return [
			['value'=>'exitoso','label'=>'exitoso'],
			['value'=>'no_contesta','label'=>'no_contesta'],
			['value'=>'numero_invalido','label'=>'numero_invalido'],
			['value'=>'no_procede','label'=>'no_procede'],
		];
	}

	/* -------- TARIFAS -------- */
	public static function scope2(){
		return [
			['value'=>'global','label'=>'global'],
			['value'=>'ciudad','label'=>'ciudad'],
			['value'=>'zona','label'=>'zona'],
			['value'=>'ruta','label'=>'ruta'],
		];
	}
	public static function categoria2(){
		return [
			['value'=>'taxi','label'=>'taxi'],
			['value'=>'taxi_electrico','label'=>'taxi_electrico'],
			['value'=>'taxi_van','label'=>'taxi_van'],
			['value'=>'taxi_plus','label'=>'taxi_plus'],
			['value'=>'movilidad_reducida','label'=>'movilidad_reducida'],
		];
	}
	public static function horario(){
		return [
			['value'=>'todo_dia','label'=>'todo_dia'],
			['value'=>'diurno','label'=>'diurno'],
			['value'=>'nocturno','label'=>'nocturno'],
			['value'=>'fin_semana','label'=>'fin_semana'],
			['value'=>'festivo','label'=>'festivo'],
		];
	}

	/* -------- DISPOSITIVOS / VEHÍCULOS -------- */
	public static function plataforma(){
		return [
			['value'=>'android','label'=>'android'],
			['value'=>'ios','label'=>'ios'],
			['value'=>'web','label'=>'web'],
		];
	}
	public static function estadoVehiculo(){
		return [
			['value'=>'activo','label'=>'activo'],
			['value'=>'inactivo','label'=>'inactivo'],
			['value'=>'suspendido','label'=>'suspendido'],
		];
	}

	/* -------- VIAJES / LOG -------- */
	public static function fromEstado(){
		return [
			['value'=>'buscando','label'=>'buscando'],
			['value'=>'asignado','label'=>'asignado'],
			['value'=>'en_camino','label'=>'en_camino'],
			['value'=>'llego','label'=>'llego'],
			['value'=>'iniciado','label'=>'iniciado'],
			['value'=>'terminado','label'=>'terminado'],
			['value'=>'cancelado_pasajero','label'=>'cancelado_pasajero'],
			['value'=>'cancelado_conductor','label'=>'cancelado_conductor'],
			['value'=>'no_show','label'=>'no_show'],
			['value'=>'fallo_localizacion','label'=>'fallo_localizacion'],
		];
	}
	public static function actorTipo(){
		return [
			['value'=>'pasajero','label'=>'pasajero'],
			['value'=>'conductor','label'=>'conductor'],
			['value'=>'sistema','label'=>'sistema'],
			['value'=>'admin','label'=>'admin'],
		];
	}
	public static function motivoCodigo(){
		return [
			['value'=>'flujo','label'=>'flujo'],
			['value'=>'aceptado','label'=>'aceptado'],
			['value'=>'arribo','label'=>'arribo'],
			['value'=>'inicio','label'=>'inicio'],
			['value'=>'fin','label'=>'fin'],
			['value'=>'cancelado_pasajero','label'=>'cancelado_pasajero'],
			['value'=>'cancelado_conductor','label'=>'cancelado_conductor'],
			['value'=>'no_show','label'=>'no_show'],
			['value'=>'timeout_aceptar','label'=>'timeout_aceptar'],
			['value'=>'reasignacion','label'=>'reasignacion'],
			['value'=>'fallo_localizacion','label'=>'fallo_localizacion'],
		];
	}
	public static function appOrigen(){
		return [
			['value'=>'api','label'=>'api'],
			['value'=>'panel','label'=>'panel'],
			['value'=>'app_pasajero','label'=>'app_pasajero'],
			['value'=>'app_conductor','label'=>'app_conductor'],
			['value'=>'sistema','label'=>'sistema'],
		];
	}
	public static function canceladoPor(){
		return [
			['value'=>'pasajero','label'=>'pasajero'],
			['value'=>'conductor','label'=>'conductor'],
			['value'=>'sistema','label'=>'sistema'],
		];
	}
	public static function estado2(){
		return [
			['value'=>'1','label'=>'Activo'],
			['value'=>'2','label'=>'Inactivo'],
		];
	}
	/* -------- WALLET -------- */
	public static function sentido(){
		return [
			['value'=>'credito','label'=>'credito'],
			['value'=>'debito','label'=>'debito'],
		];
	}
	public static function motivo(){
		return [
			['value'=>'recarga','label'=>'recarga'],
			['value'=>'ajuste','label'=>'ajuste'],
			['value'=>'debito_asignacion','label'=>'debito_asignacion'],
			['value'=>'debito_aceptacion','label'=>'debito_aceptacion'],
			['value'=>'debito_inicio','label'=>'debito_inicio'],
			['value'=>'debito_termino','label'=>'debito_termino'],
			['value'=>'reversa','label'=>'reversa'],
			['value'=>'bono','label'=>'bono'],
			['value'=>'penalidad','label'=>'penalidad'],
		];
	}

}
