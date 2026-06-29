//
// Funciones de informes automáticos de información (de Sensores)
//


// Muestra la ventana para añadir el informe automático de temperatura
function boton_sensores_informacion_temperatura_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_temperatura(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA;
    var parametros_tipo = [
        id_sensor,
        campo_parametros_extra,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de humedad
function boton_sensores_informacion_humedad_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_humedad(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD;
    var parametros_tipo = [
        id_sensor,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de luz interior
function boton_sensores_informacion_luz_interior_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_luz_interior(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR;
    var parametros_tipo = [
        id_sensor,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de viento
function boton_sensores_informacion_viento_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_viento(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_VIENTO;
    var parametros_tipo = [
        id_sensor,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de energía activa
function boton_sensores_informacion_energia_activa_anyadir_informe_automatico() {
    boton_sensores_informacion_energia_anyadir_informe_automatico(TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA);
}


// Muestra la ventana para añadir el informe automático de energía reactiva
function boton_sensores_informacion_energia_reactiva_anyadir_informe_automatico() {
    boton_sensores_informacion_energia_anyadir_informe_automatico(TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA);
}


// Muestra la ventana para añadir el informe automático de energía
function boton_sensores_informacion_energia_anyadir_informe_automatico(tipo_informe_automatico) {
    // Sufijo de controles de tipo de energía
    var tipo_energia = null;
    switch (tipo_informe_automatico) {
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA: {
            tipo_energia = "activa";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA: {
            tipo_energia = "reactiva";
            break;
        }
    }

    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_energia(tipo_energia, true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var parametros_tipo = [
        id_ratio,
        id_sensor,
        campo,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo_informe_automatico, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de cortes de tensión
function boton_sensores_informacion_cortes_tension_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_cortes_tension(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION;
    var parametros_tipo = [
        id_sensor,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de compra de energía
function boton_sensores_informacion_compra_energia_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_compra_energia(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA;
    var parametros_tipo = [
        id_sensor,
        campo,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de gas
function boton_sensores_informacion_gas_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_gas(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_GAS;
    var parametros_tipo = [
        id_ratio,
        id_sensor,
        campo,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de agua
function boton_sensores_informacion_agua_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_agua(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_AGUA;
    var parametros_tipo = [
        id_ratio,
        id_sensor,
        campo,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de información genérica
function boton_sensores_informacion_generica_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_generica(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_INFORMACION_GENERICA;
    var parametros_tipo = [
        id_ratio,
        id_sensor,
        campo,
        intervalo_valores,
        tipo_mapa_calor,
        comentarios,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
