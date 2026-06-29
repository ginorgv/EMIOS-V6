//
// Funciones de informes automáticos de información (de Actuadores)
//


// Muestra la ventana para añadir el informe automático de acciones enviadas
function boton_actuadores_informacion_acciones_enviadas_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_actuadores_informacion_acciones_enviadas(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var clase_actuador = parametros_informe["clase_actuador"];
    var destino_accion = parametros_informe["destino_accion"];
    var id_destino_accion = parametros_informe["id_destino_accion"];
    var origen_acciones = parametros_informe["origen_acciones"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Campo y parámetros extra
    var campo_parametros_extra = campo;
    if (parametros_extra_campo != "") {
        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
    }

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS;
    var parametros_tipo = [
        clase_actuador,
        destino_accion,
        id_destino_accion,
        origen_acciones,
        clase_sensor,
        id_sensor,
        campo_parametros_extra,
        intervalo_valores,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
