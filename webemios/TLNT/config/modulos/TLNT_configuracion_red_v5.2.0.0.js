// Acciones cuando se visualizan controles
TLNT.Navegacion.acciones_visualizacion_controles_red = [
    {	selector: '#topologia-red',
		funcion: 	boton_red_actualizar_topologia_red
	}
];


// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_red = [
    // Red
	{	selector: '.boton_red_envia_accion_herramientas_red',
		funcion: 	boton_red_envia_accion_herramientas_red
	},
	{	selector: '.boton_red_envia_accion_herramientas_axon',
		funcion: 	boton_red_envia_accion_herramientas_axon
	},
    {	selector: '.boton_red_mostrar_ventana_borrado_valores_red',
		funcion: 	boton_red_mostrar_ventana_borrado_valores_red
	},
    {	selector: '.boton_red_envia_accion_herramientas_dispositivo',
		funcion: 	boton_red_envia_accion_herramientas_dispositivo
	},
    {	selector: '#boton_red_actualizar_informacion_red',
		funcion: 	boton_red_actualizar_informacion_red
	},
	{	selector: '#boton_red_filtro_alarmas',
		funcion: 	boton_red_filtro_alarmas
	},
    {	selector: '#boton_red_filtro_acciones_usuario',
		funcion: 	boton_red_filtro_acciones_usuario
	},
    {	selector: '#boton_red_exportar_acciones_usuario',
		funcion: 	boton_red_exportar_acciones_usuario
	},
    {	selector: '#boton_red_filtro_comentarios',
		funcion: 	boton_red_filtro_comentarios
	},
    {	selector: '#boton_red_filtro_topologia_red',
		funcion: 	boton_red_filtro_topologia_red
	},
	{	selector: '#boton_red_actualizar_topologia_red',
		funcion: 	boton_red_actualizar_topologia_red
	}
];


// Eventos de las tablas de datos del módulo red
TLNT.Navegacion.botones_tablas_datos_red = [
];


TLNT.Navegacion.botones_detalles_tablas_datos_red = [
    // Herramientas de dispositivo y axón
    {	selector: '.boton_red_envia_accion_herramientas_dispositivo',
		funcion: 	boton_red_envia_accion_herramientas_dispositivo
	},
    {	selector: '.boton_red_envia_accion_herramientas_axon',
		funcion: 	boton_red_envia_accion_herramientas_axon
	}
];


TLNT.Navegacion.botones_ventanas_modales_red = [
    // Comentarios
    {	selector: '.boton_anyadir_modificar_comentario',
		funcion: 	boton_anyadir_modificar_comentario
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_red = function() {
    TLNT.Navegacion.realiza_acciones_visualizacion_controles(TLNT.Navegacion.acciones_visualizacion_controles_red);
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_red);
};


TLNT.Navegacion.establece_eventos_tablas_datos_red = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_red);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_red = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_red);
};


TLNT.Navegacion.establece_eventos_ventanas_modales_red = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_red);

    // Desactivación de eventos anteriores
    $("#descripcion_dispositivo").off();
    $("#id_dispositivo_axon").off();

    // Contador de caracteres de descripción de dispositivo
    $("#descripcion_dispositivo").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Habilita la lista de identificadores de dispositivos de un axón
    var funcion_habilita_lista_ids_dispositivos_axon = function() {
        if ($("select#id_dispositivo_axon option").length <= 1)
        {
            $("#id_dispositivo_axon").prop('disabled', 'disabled');
        }
        else
        {
            $("#id_dispositivo_axon").prop('disabled', false);
        }
    };
    $("#id_dispositivo_axon").show(funcion_habilita_lista_ids_dispositivos_axon);
};
