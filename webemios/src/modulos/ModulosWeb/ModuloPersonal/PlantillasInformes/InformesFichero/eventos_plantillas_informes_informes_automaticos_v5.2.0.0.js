//
// Funciones de informes automáticos de plantillas de informes
//


// Muestra la ventana para añadir el informe automático del informe de plantilla de informe
function boton_personal_informe_plantilla_informe_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_plantilla_informe = parametros_informe["id_plantilla_informe"];
    var ids_parametros = parametros_informe["ids_parametros"];
    var valores_parametros = parametros_informe["valores_parametros"];
    var ids_elementos_portada = parametros_informe["ids_elementos_portada"];
    var ids_elementos_titulo = parametros_informe["ids_elementos_titulo"];
    var ids_elementos_texto = parametros_informe["ids_elementos_texto"];
    var ids_elementos_imagen = parametros_informe["ids_elementos_imagen"];
    var ficheros_imagenes_elementos_imagen_texts = parametros_informe["ficheros_imagenes_elementos_imagen_texts"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var parametros_tipo_json = parametros_informe["parametros_tipo_json"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME;
    var cadena_ids_parametros = ids_parametros.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_valores_parametros = valores_parametros.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_ids_elementos_portada = ids_elementos_portada.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_ids_elementos_titulo = ids_elementos_titulo.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_ids_elementos_texto = ids_elementos_texto.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_ids_elementos_imagen = ids_elementos_imagen.join(SEPARADOR_PARAMETROS_SIMPLES);
    var imagenes_personalizadas_elementos_imagen = [];
    for (var i = 0; i < ids_elementos_imagen.length; i++) {
        if (ficheros_imagenes_elementos_imagen_texts[i] != "") {
            imagenes_personalizadas_elementos_imagen.push(VALOR_SI);
        }
        else {
            imagenes_personalizadas_elementos_imagen.push(VALOR_NO);
        }
    }
    var cadena_imagenes_personalizadas_elementos_imagen = imagenes_personalizadas_elementos_imagen.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_plantilla_informe,
        cadena_ids_parametros,
        cadena_valores_parametros,
        cadena_ids_elementos_portada,
        cadena_ids_elementos_titulo,
        cadena_ids_elementos_texto,
        cadena_ids_elementos_imagen,
        cadena_imagenes_personalizadas_elementos_imagen,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
