/*
 * Funciones de herramientas de nodos
 *
 */


function boton_mostrar_ventana_asignacion_localizacion_nodos(tipo_nodo) {
    $.post("./src/lib/modulos/Nodos/administracion/muestra_ventana_asignacion_localizacion_nodos.php", {
        tipo_nodo: tipo_nodo
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


function boton_asignar_localizacion_nodos() {
    // Se recupera la localización
    var id_localizacion = $('#id_localizacion_asignacion_localizacion_nodos').val();
    if (id_localizacion == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay localización seleccionada"));
        return;
	}

    // Tipo y clase de nodos
    var tipo_nodo = $("#parametros_ventana_asignacion_localizacion_nodos").attr("tipo_nodo");
    var clase_nodo = $('#clase_nodo_asignacion_localizacion_nodos').val();

    // Se recuperan los identificadores de los nodos y grupos de nodos seleccionados
    var ids_nodos = [];
    var ids_grupos_nodos = [];
    $("#ids_nodos_asignacion_localizacion_nodos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_nodos.push($(this).val());
        }
    });
    $("#ids_grupos_nodos_asignacion_localizacion_nodos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_grupos_nodos.push($(this).val());
        }
    });

    // Se asigna la localización a los nodos especificados
    $.post("./src/lib/modulos/Nodos/administracion/asigna_localizacion_nodos.php", {
        id_localizacion: id_localizacion,
        tipo_nodo: tipo_nodo,
        clase_nodo: clase_nodo,
        ids_nodos: ids_nodos,
        ids_grupos_nodos: ids_grupos_nodos
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        switch (tipo_nodo) {
            case TIPO_NODO_SENSOR: {
                actualiza_tabla_nodos(TIPO_NODO_SENSOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
                break;
            }
            case TIPO_NODO_ACTUADOR: {
                actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);
                break;
            }
        }
    });
}


function boton_mostrar_ventana_asignacion_grupo_nodos(tipo_nodo) {
    $.post("./src/lib/modulos/Nodos/administracion/muestra_ventana_asignacion_grupo_nodos.php", {
        tipo_nodo: tipo_nodo
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


function boton_asignar_grupo_nodos() {
    // Se recupera el grupo
    var id_grupo = $('#id_grupo_asignacion_grupo_nodos').val();
    if (id_grupo == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay grupo seleccionado"));
        return;
	}

    // Parámetros de la ventana
    var tipo_nodo = $("#parametros_ventana_asignacion_grupo_nodos").attr("tipo_nodo");

    // Se recuperan los identificadores de los nodos seleccionados
    var ids_nodos = [];
    $("#ids_nodos_asignacion_grupo_nodos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_nodos.push($(this).val());
        }
    });

    // Se asigna el grupo a los nodos especificados
    $.post("./src/lib/modulos/Nodos/administracion/asigna_grupo_nodos.php", {
        id_grupo: id_grupo,
        tipo_nodo: tipo_nodo,
        ids_nodos: ids_nodos
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        switch (tipo_nodo) {
            case TIPO_NODO_SENSOR: {
                actualiza_tabla_nodos(TIPO_NODO_SENSOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
                break;
            }
            case TIPO_NODO_ACTUADOR: {
                actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);
                break;
            }
        }
    });
}
