//
// Funciones de acciones de usuario
//


// Muestra la ventana de modificar las observaciones de una acción de usuario
function boton_mostrar_ventana_modificar_observaciones_accion_usuario(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var modulo = $("#modulo").attr("name");
    var params = this.id.split('__');
	var id_accion = params[1];

    $.post("./src/lib/modulos/AccionesUsuario/muestra_ventana_modificar_observaciones_accion.php", {
        modulo: modulo,
		id_accion: id_accion
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		// Se muestra la ventana modal
        $('#ventana_modal').modal('show');
		TLNT.Navegacion.carga_ventana_modal(
			resultado.titulo,
			resultado.contenido,
			resultado.pie);

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Modificación de las observaciones de una acción de usuario
function boton_modificar_observaciones_accion_usuario() {
    // Parámetros de la ventana
	var id_accion = $("#parametros_ventana_modificar_observaciones_accion").attr("id_accion");
    var observaciones_anteriores = $("#parametros_ventana_modificar_observaciones_accion").attr("observaciones_anteriores");

    // Observaciones
    var observaciones = $('#observaciones_accion').val();
    if (comprueba_longitud_cadena(observaciones, NUMERO_MAXIMO_CARACTERES_OBSERVACIONES) == false) {
        $('#observaciones_accion').addClass('data-check-failed');
        return;
    }

    // Se modifican las observaciones de la acción
    $.post("./src/lib/modulos/AccionesUsuario/modifica_observaciones_accion.php", {
        modulo: $("#modulo").attr("name"),
        id_accion: id_accion,
        observaciones: observaciones,
        observaciones_anteriores: observaciones_anteriores
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        actualiza_detalles_accion_usuario(id_accion);
        $('#ventana_modal').modal('hide');
    });
}


// Actualiza los detalles de la acción de usuario
function actualiza_detalles_accion_usuario(id_accion) {
    // Id de la fila de la acción
	var id_datos = "datosAccionUsuario__" + id_accion;

    // Se actualiza la información detallada (si está visible)
    var detalles_tabla_visibles = dame_elemento_visible(id_datos + " .detalle-tabla-datos");
    if (detalles_tabla_visibles == true) {
        $.post("./comun/src/lib/modulos/dame_detalles_tabla.php", {
            id_datos: id_datos
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $('#' + id_datos + " .detalle-tabla-datos").html(resultado.html);

            // Establecimiento de eventos
            TLNT.Navegacion.establece_eventos_tablas_datos();
            TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos();

            // Acciones 'extra' a realizar en los detalles de la tabla de datos
            TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos(resultado);
        });
    }
}
