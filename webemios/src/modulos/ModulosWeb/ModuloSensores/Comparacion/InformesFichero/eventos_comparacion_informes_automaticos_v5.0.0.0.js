//
// Funciones de informes automáticos de comparación (de Sensores)
//


// Muestra la ventana para añadir el informe automático de comparación de periodos
function boton_sensores_comparacion_periodos_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_periodos(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Campo y parámetros extra
    var campo_parametros_extra = campo;
    if (parametros_extra_campo != "") {
        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
    }

    // Horario semanal y exclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_COMPARACION_PERIODOS;
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        id_sensor,
        campo_parametros_extra,
        intervalo_valores,
        tipo_mapa_calor,
        cadena_horario_semanal,
        cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de comparación con perfil horario
function boton_sensores_comparacion_perfil_horario_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_perfil_horario(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var cadena_fecha_inicio_perfil_horario_local = parametros_informe["fecha_inicio_perfil_horario"];
    var cadena_fecha_fin_perfil_horario_local = parametros_informe["fecha_fin_perfil_horario"];
    var tipo_perfil_horario = parametros_informe["tipo_perfil_horario"];
    var agrupaciones_dias_semana = parametros_informe["agrupaciones_dias_semana"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Campo y parámetros extra
    var campo_parametros_extra = campo;
    if (parametros_extra_campo != "") {
        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
    }

    // Fechas
    var cadena_fecha_inicio_perfil_horario = convierte_formato_fecha(cadena_fecha_inicio_perfil_horario_local, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
    var cadena_fecha_fin_perfil_horario = convierte_formato_fecha(cadena_fecha_fin_perfil_horario_local, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

    // Agrupaciones de días de la semana
    var cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana(agrupaciones_dias_semana);

    // Horario semanal y exclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO;
    var parametros_tipo = [
        clase_sensor,
        id_sensor,
        campo_parametros_extra,
        intervalo_valores,
        tipo_mapa_calor,
        cadena_fecha_inicio_perfil_horario,
        cadena_fecha_fin_perfil_horario,
        tipo_perfil_horario,
        cadena_agrupaciones_dias_semana,
        cadena_horario_semanal,
        cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de comparación de campos iguales
function boton_sensores_comparacion_campos_iguales_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_campos_iguales(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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
    var tipo = TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES;
    var cadena_ids_sensores = ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        cadena_ids_sensores,
        campo_parametros_extra,
        intervalo_valores,
        tipo_mapa_calor,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de comparación de campos diferentes
function boton_sensores_comparacion_campos_diferentes_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_campos_diferentes(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensores = parametros_informe["clases_sensores"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var unificar_escalas = parametros_informe["unificar_escalas"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Campos y parámetros extra
    var campos_parametros_extra = [];
    for (var i = 0; i < clases_sensores.length; i++) {
        var campo_parametros_extra = campos[i];
        if (parametros_extra_campos[i] != "") {
            campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campos[i];
        }
        campos_parametros_extra.push(campo_parametros_extra);
    }

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES;
    var cadena_ids_sensores = ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        clases_sensores,
        cadena_ids_sensores,
        campos_parametros_extra,
        intervalo_valores,
        unificar_escalas,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de análisis comparativo
function boton_sensores_analisis_comparativo_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_comparativo(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var ids_sensores_agregados = parametros_informe["ids_sensores_agregados"];
    var id_sensor_destacado = parametros_informe["id_sensor_destacado"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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
    var tipo = TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO;
    var cadena_ids_sensores_agregados = ids_sensores_agregados.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        campo_parametros_extra,
        cadena_ids_sensores_agregados,
        id_sensor_destacado,
        intervalo_valores,
        tipo_mapa_calor,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de valores generales
function boton_sensores_valores_generales_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_valores_generales(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensor = parametros_informe["clases_sensor"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var agregacion = parametros_informe["agregacion"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Campos y parámetros extra
    var campos_parametros_extra = [];
    for (var i = 0; i < clases_sensor.length; i++) {
        var campo_parametros_extra = campos[i];
        if (parametros_extra_campos[i] != "") {
            campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campos[i];
        }
        campos_parametros_extra.push(campo_parametros_extra);
    }

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_VALORES_GENERALES;
    var cadena_ids_sensores = ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        clases_sensor,
        campos_parametros_extra,
        cadena_ids_sensores,
        intervalo_valores,
        agregacion,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de incrementos totales
function boton_sensores_incrementos_totales_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_incrementos_totales(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensor = parametros_informe["clases_sensor"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var agregacion = parametros_informe["agregacion"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Campos y parámetros extra
    var campos_parametros_extra = [];
    for (var i = 0; i < clases_sensor.length; i++) {
        var campo_parametros_extra = campos[i];
        if (parametros_extra_campos[i] != "") {
            campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campos[i];
        }
        campos_parametros_extra.push(campo_parametros_extra);
    }

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES;
    var cadena_ids_sensores = ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        clases_sensor,
        campos_parametros_extra,
        cadena_ids_sensores,
        intervalo_valores,
        agregacion,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}