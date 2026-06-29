/*
 * Funciones de localizaciones (de varios módulos)
 *
 */


// Selección de localización actual
function boton_seleccion_localizacion_actual() {
    var id_localizacion = $('#id_localizacion_seleccion_localizacion_actual').val();
    var nombre_localizacion = $('#id_localizacion_seleccion_localizacion_actual option:selected').text();

    // Se recuperan los identificadores de las localizaciones seleccionadas (si es necesario)
    var ids_localizaciones_seleccionadas = [];
    switch (parseInt(id_localizacion)) {
        case ID_LOCALIZACIONES_SELECCIONADAS_AND:
        case ID_LOCALIZACIONES_SELECCIONADAS_OR: {
            $("#ids_localizaciones_seleccion_localizacion_actual option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_localizaciones_seleccionadas.push($(this).val());
                }
            });
            if (ids_localizaciones_seleccionadas.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una localización"));
                return;
            }
            break;
        }
    }

    $.post("./src/lib/modulos/localizaciones/selecciona_localizacion_actual.php", {
		id_localizacion: id_localizacion,
        nombre_localizacion: nombre_localizacion,
        ids_localizaciones_seleccionadas: ids_localizaciones_seleccionadas
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);

        // Se recarga el contenido de la sección actual (si es necesario)
        var recargar_contenido_seccion_actual = null;
        var modulo_actual = TLNT.Navegacion.modulo_actual;
        var seccion_actual = TLNT.Navegacion.seccion_actual;
        switch (modulo_actual) {
            case MODULO_PERSONAL: {
                switch (seccion_actual) {
                    case SECCION_PERSONAL_PLANTILLAS_INFORMES: {
                        recargar_contenido_seccion_actual = true;
                        break;
                    }
                    default: {
                        recargar_contenido_seccion_actual = false;
                        break;
                    }
                }
                break;
            }
            default: {
                recargar_contenido_seccion_actual = true;
                break;
            }
        }
        if (recargar_contenido_seccion_actual == true) {
            TLNT.Navegacion.recarga_contenido_seccion_actual();
        }
	});
}

