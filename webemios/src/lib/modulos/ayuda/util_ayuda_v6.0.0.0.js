//
// Creación de textos de ayuda
//


//
// Varios módulos
//


// Ayuda de agrupaciones de días de la semana
function dame_texto_ayuda_agrupaciones_dias_semana() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Las agrupaciones de días de la semana se separan por comas y los días dentro de cada agrupación de días se separan por guiones") + " (" +
        TLNT.Idiomas._("es necesario especificar todos los días de la semana una sola vez") + ")";
    texto_ayuda += "\n(" + TLNT.Idiomas._("ejemplo") + ": " + "1-2-3-4-5, 6-7" + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de fecha
function dame_texto_ayuda_fecha() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("El formato de fecha es el siguiente (vacío si no se quiere especificar ninguna fecha)") + ":" +
        " " + formato_fecha_local + " ";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de fechas
function dame_texto_ayuda_fechas() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("El formato de fechas es el siguiente (separados por comas)") + ":";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Fecha única") + " (" + formato_fecha_local + ")";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Rango de fechas") + " (" + formato_fecha_local + " - " + formato_fecha_local + ")";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Fecha única con hora") + " (" + formato_fecha_local + " hh:mm)";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Rango de fechas con hora") + " (" + formato_fecha_local + " hh:mm - " + formato_fecha_local + " hh:mm)";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Día anual único") + " (" + formato_dia_anyo_local + ")";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Rango de días anuales") + " (" + formato_dia_anyo_local + " - " + formato_dia_anyo_local + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Administración
//


// Ayuda de tabla de usuarios
function dame_texto_ayuda_tabla_usuarios() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la creación de usuarios y asignación de permisos en el manual o en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_USUARIOS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de parámetros de acción inicial de usuario
function dame_texto_ayuda_parametros_accion_inicial_usuario() {
    // Acciones iniciales
    var acciones_iniciales = [
        ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS];
    var parametros_acciones_iniciales = [
        ["número de segundos de intervalo de actualización"]];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Parámetros de acciones iniciales") + ":";
    for (var i = 0; i < acciones_iniciales.length; i++) {
        texto_ayuda += "\n&ensp;- " + dame_descripcion_accion_inicial(acciones_iniciales[i]) + ": ";
        for (var j = 0; j < parametros_acciones_iniciales[i].length; j++) {
            if (j > 0) {
                texto_ayuda += ", ";
            }
            texto_ayuda += TLNT.Idiomas._(parametros_acciones_iniciales[i][j]);
        }
    }

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Personal
//


// Ayuda de tabla de widgets
function dame_texto_ayuda_tabla_widgets() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre los widgets en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_WIDGETS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de plantillas de informes
function dame_texto_ayuda_tabla_plantillas_informes() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la creación de plantillas de informes en el siguiente videotutorial") + ": ";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de plantillas de informes fijas") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_PLANTILLAS_INFORMES_FIJAS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    // texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de plantillas de informes configurables") + " ";
    // texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_PLANTILLAS_INFORMES_CONFIGURABLES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de informes automáticos
function dame_texto_ayuda_tabla_informes_automaticos() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre los informes automáticos en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_INFORMES_AUTOMATICOS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Red
//


// Ayuda de tabla de comentarios
function dame_texto_ayuda_tabla_comentarios() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre los comentarios en sensores en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_COMENTARIOS_SENSORES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Localizaciones
//


// Ayuda de tabla de localizaciones
function dame_texto_ayuda_tabla_localizaciones() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre el módulo de localizaciones en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_MODULO_LOCALIZACIONES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de plantillas de informes
function dame_texto_ayuda_tabla_instalaciones() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la sección de instalaciones en el siguiente videotutorial") + ": ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_MODULO_LOCALIZACIONES_INSTALACIONES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    
    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}

//
// Módulo Sensores
//


// Ayuda de importación de valores de sensores
function dame_texto_ayuda_importacion_valores_sensor() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la importación de valores de un sensor en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_IMPORTACION_VALORES_SENSOR + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de exportación de valores de sensores
function dame_texto_ayuda_valores_clase_exportacion() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los valores de clase contienen información como coste, tramo, sobrepotencia o coseno de phi. Marque esta opción si quiere que estos datos se exporten también");

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de formato de fecha y hora en importación de valores de un sensor
function dame_texto_ayuda_formato_fecha_hora_valores_sensor() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Parámetros de formato de fecha y hora") + ":";
    texto_ayuda += "\n&ensp;- " + "d" + ": " + TLNT.Idiomas._("día");
    texto_ayuda += "\n&ensp;- " + "m" + ": " + TLNT.Idiomas._("mes");
    texto_ayuda += "\n&ensp;- " + "Y" + ": " + TLNT.Idiomas._("año");
    texto_ayuda += "\n&ensp;- " + "H" + ": " + TLNT.Idiomas._("hora");
    texto_ayuda += "\n&ensp;- " + "M" + ": " + TLNT.Idiomas._("minuto");
    texto_ayuda += "\n&ensp;- " + "S" + ": " + TLNT.Idiomas._("segundo");
    texto_ayuda += "\n(" + TLNT.Idiomas._("ejemplo") + ": " + "d-m-Y, H:M:S" + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de formato de identificador de estación meteorológica (idema) específica para un sensor externo de tipo HTTP Emios
function dame_texto_ayuda_idema_sensor_externo_http_emios() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los identificadores de estaciones meteorológicas se pueden consultar en el siguiente enlace") + ":\n" +
        "<a target='_blank' href='" + ENLACE_AYUDA_IDEMAS_AEMET + "'>Aemet ('Datos de observación. Último elaborado')" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tipos de registro ModBus de sensor
function dame_texto_ayuda_tipos_registro_modbus_sensor() {
    // Tipos de registro
    var tipos_registro_modbus = [
        TIPO_REGISTRO_MODBUS_COILS,
        TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS,
        TIPO_REGISTRO_MODBUS_INPUT_REGISTERS,
        TIPO_REGISTRO_MODBUS_DISCRETE_INPUTS,
        TIPO_REGISTRO_MODBUS_AUTO_BYTES,
        TIPO_REGISTRO_MODBUS_AUTO_BITS];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Tipos de registro") + ":";
    for (var i = 0; i < tipos_registro_modbus.length; i++) {
        texto_ayuda += "\n&ensp;- " + tipos_registro_modbus[i];
    }

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de función de valores en sensores de procesado
function dame_texto_ayuda_funcion_valores_sensor_procesado() {
    var operadores = [
        "()",
        "*",
        "/",
        "+",
        "-",
        "**"];
    var descripciones_operadores = [
        "paréntesis",
        "multiplicación",
        "división",
        "suma",
        "resta",
        "potencia"];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Operadores") + ":";
    for (var i = 0; i < operadores.length; i++) {
        texto_ayuda += "\n&ensp;- " + operadores[i] + ": " + TLNT.Idiomas._(descripciones_operadores[i]);
    }
    texto_ayuda += "\n" + TLNT.Idiomas._("Expresión condicional") + ":";
    texto_ayuda += "\n&ensp;- " + "'equal' if x == y else 'not equal'";
    texto_ayuda += "\n&ensp;(" + TLNT.Idiomas._("ejemplo") + ": " + "(1) if (x1 == 0) else (x2 / x1)" + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de valores de prueba de la función de valores en sensores de procesado
function dame_texto_ayuda_valores_prueba_funcion_valores_sensor_procesado() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los valores de prueba se separan por comas y en el orden de la lista de los hijos");
    texto_ayuda += " (" + TLNT.Idiomas._("si no hay valores de prueba, se evalúa la función con todos los valores a 0 y no se muestra el resultado") + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de valores aleatorios de sensor
function dame_texto_ayuda_valores_aleatorios_sensor() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Parámetros") + ": ";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._('tipo de valor');
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._('valor inicial');
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._('valor mínimo');
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._('valor máximo');

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de eventos
function dame_texto_ayuda_tabla_eventos() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la creación de eventos en los siguientes videotutoriales") + ":";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de alarmas por exceso de potencia") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_ALARMAS_EXCESO_POTENCIA + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de alarmas por consumo fuera de horario") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_ALARMAS_CONSUMO_FUERA_HORARIO + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de alarmas en grupos de múltiples sensores") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_ALARMAS_GRUPOS_MULTIPLES_SENSORES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de parámetros de evento
function dame_texto_ayuda_parametros_evento() {
    // Tipos y parámetros de tipos evento
    var tipos_evento = [
        TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO,
        TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO,
        TIPO_EVENTO_VALOR_MINIMO,
        TIPO_EVENTO_VALOR_MAXIMO,
        TIPO_EVENTO_VALORES_MINIMO_MAXIMO,
        TIPO_EVENTO_INTERVALO_VALORES,
        TIPO_EVENTO_VALOR_EXACTO,
        TIPO_EVENTO_VALOR_DIFERENTE,
        TIPO_EVENTO_VALOR_EXACTO_BITS,
        TIPO_EVENTO_VALOR_DIFERENTE_BITS,
        TIPO_EVENTO_VALOR_REPETIDO,
        TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL,
        TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO,
        TIPO_EVENTO_LINEA_BASE,
        TIPO_EVENTO_PERFIL_HORARIO];
    var parametros_tipos_evento = [
        ["incremento mínimo", "segundos"],
        ["incremento máximo", "segundos"],
        ["valor mínimo", "histéresis"],
        ["valor máximo", "histéresis"],
        ["valor mínimo", "valor máximo", "histéresis"],
        ["valor mínimo", "valor máximo", "histéresis"],
        ["valor exacto"],
        ["valor diferente"],
        ["valor exacto", "bit inicial", "número de bits"],
        ["valor diferente", "bit inicial", "número de bits"],
        ["número máximo de repeticiones de valor"],
        ["hora de inicio", "incremento acumulado máximo"],
        ["número de periodos de tiempo", "incremento acumulado máximo"],
        ["multiplicador de error estándar de línea base"],
        ["diferencia máxima"]];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Parámetros de tipos de evento") + ":";
    for (var i = 0; i < tipos_evento.length; i++) {
        texto_ayuda += "\n&ensp;- " + dame_descripcion_tipo_evento(tipos_evento[i]) + ": ";
        for (var j = 0; j < parametros_tipos_evento[i].length; j++) {
            if (j > 0) {
                texto_ayuda += ", ";
            }
            texto_ayuda += TLNT.Idiomas._(parametros_tipos_evento[i][j]);
        }
    }
    texto_ayuda += "\n" + TLNT.Idiomas._("Si el valor exacto del parámetro se incluye en el evento, se debe añadir un asterisco (*) después del valor");

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de informes de información
function dame_texto_ayuda_informes_informacion() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre estos informes en los siguientes videotutoriales") + ":";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Comentarios en sensores") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_COMENTARIOS_SENSORES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Configurar informes automáticos") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_INFORMES_AUTOMATICOS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de informe de correlación
function dame_texto_ayuda_informe_correlacion() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre cómo hacer estudios de correlación en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CORRELACION + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Actuadores
//


// Ayuda de tipos de registro ModBus de actuador
function dame_texto_ayuda_tipos_registro_modbus_actuador() {
    // Tipos de registro
    var tipos_registro_modbus_escritura = [
        TIPO_REGISTRO_MODBUS_HOLDING_REGISTER,
        TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS,
        TIPO_REGISTRO_MODBUS_COIL,
        TIPO_REGISTRO_MODBUS_COILS];
    var tipos_registro_modbus_lectura = [
        TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS,
        TIPO_REGISTRO_MODBUS_INPUT_REGISTERS,
        TIPO_REGISTRO_MODBUS_AUTO_BYTES];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Tipos de registro de escritura") + ":";
    for (var i = 0; i < tipos_registro_modbus_escritura.length; i++) {
        texto_ayuda += "\n&ensp;- " + tipos_registro_modbus_escritura[i];
    }
    texto_ayuda += "\n" + TLNT.Idiomas._("Tipos de registro de lectura") + ":";
    for (var i = 0; i < tipos_registro_modbus_lectura.length; i++) {
        texto_ayuda += "\n&ensp;- " + tipos_registro_modbus_lectura[i];
    }
    texto_ayuda += "\n" + TLNT.Idiomas._("Si los tipos de registro de escritura y lectura son diferentes, se separan por dos puntos (:)");

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de comodines en mensajes de texto de acciones manuales y de programaciones
function dame_texto_ayuda_comodines_mensaje_texto_acciones_manuales_programaciones() {
    // Comodines de mensajes de texto de acciones de actuadores
    var comodines_mensajes_texto_acciones_actuadores = [
        "sensor_info_id"];
    var descripciones_comodines_mensajes_texto_acciones_actuadores = [
        "información de sensor con el identificador especificado"];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Comodines en mensajes") + ":";
    for (var i = 0; i < comodines_mensajes_texto_acciones_actuadores.length; i++) {
        texto_ayuda += "\n&ensp;- [" + comodines_mensajes_texto_acciones_actuadores[i] + "]: " +
            TLNT.Idiomas._(descripciones_comodines_mensajes_texto_acciones_actuadores[i]);
    };

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de comodines en mensaje de texto de acciones de reglas
function dame_texto_ayuda_comodines_mensaje_texto_acciones_reglas() {
    // Comodines de mensajes de texto de acciones de reglas
    var comodines_mensajes_texto_acciones_reglas = [
        "cause",
        "activated_occurrences_names",
        "activated_occurrences_names_list",
        "activated_occurrences_info",
        "activated_sensors_names",
        "activated_sensors_names_list",
        "activated_sensors_info",
        "deactivation_occurrence_name",
        "deactivation_occurrence_info",
        "deactivation_sensor_name",
        "deactivation_sensor_info"];
    var descripciones_comodines_mensajes_texto_acciones_reglas = [
        "causa de activación o desactivación de la regla",
        "nombres de sucesos activados",
        "nombres de sucesos activados (en formato lista)",
        "información de sucesos activados",
        "nombres de sensores con sucesos activados",
        "nombres de sensores con sucesos activados (en formato lista)",
        "información de sensores con sucesos activados",
        "nombre de suceso de desactivación",
        "información de suceso de desactivación",
        "nombre de sensor de desactivación",
        "información de sensor de desactivación"];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Comodines en mensajes") + ":";
    for (var i = 0; i < comodines_mensajes_texto_acciones_reglas.length; i++) {
        texto_ayuda += "\n&ensp;- [" + comodines_mensajes_texto_acciones_reglas[i] + "]: " +
            TLNT.Idiomas._(descripciones_comodines_mensajes_texto_acciones_reglas[i]);
    }

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de programaciones
function dame_texto_ayuda_tabla_programaciones() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Esta sección permite crear programaciones periódicas para controlar, por ejemplo, sistemas de climatización o riegos con un horario semanal");

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de reglas
function dame_texto_ayuda_tabla_reglas() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la creación de reglas en los siguientes videotutoriales") + ":";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de alarmas por exceso de potencia") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_ALARMAS_EXCESO_POTENCIA + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de alarmas por consumo fuera de horario") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_ALARMAS_CONSUMO_FUERA_HORARIO + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de alarmas en grupos de múltiples sensores") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_ALARMAS_GRUPOS_MULTIPLES_SENSORES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de número de días de caducidad de acciones de reglas
function dame_texto_ayuda_numero_dias_caducidad_acciones_regla() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("El número de días de caducidad de acciones de reglas indican la antiguedad máxima de los valores de los sensores que provocan la activación o desactivación de la regla para que se ejecuten las acciones");
    texto_ayuda += " (" + TLNT.Idiomas._("si la antiguedad de los valores es mayor que el número de días de caducidad de las acciones, no se ejecutarán las acciones de la regla") + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Smartmeter
//


// Ayuda de informes de potencias manuales
function dame_texto_ayuda_potencias_manuales() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre cómo optimizar potencias 3.0 y 3.1 con datos de facturas o cierres en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_OPTIMIZACION_POTENCIAS_MANUAL + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de informe de simulación de batería de condensadores
function dame_texto_ayuda_simulador_bateria_condensadores() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre cómo simular el coste de la reactiva por un fallo en batería de condensadores en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_SIMULADOR_BATERIA_CONDENSADORES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de informe de estudio general
function dame_texto_ayuda_estudio_general() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la creación de estudios energéticos personalizados en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_ESTUDIO_GENERAL + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de fórmula de precio de consumo de tarifas eléctricas 'pass-through'
/*
function dame_texto_ayuda_formula_precio_consumo_pass_through_tarifa_electrica() {
    var operadores = [
        "()",
        "*",
        "/",
        "+",
        "-",
        "**"];
    var descripciones_operadores = [
        "paréntesis",
        "multiplicación",
        "división",
        "suma",
        "resta",
        "potencia"];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Operadores") + ":";
    for (var i = 0; i < operadores.length; i++) {
        texto_ayuda += "\n&ensp;- " + operadores[i] + ": " + TLNT.Idiomas._(descripciones_operadores[i]);
    }
    texto_ayuda += "\n" + TLNT.Idiomas._("Expresión condicional") + ":";
    texto_ayuda += "\n&ensp;- " + "'equal' if x == y else 'not equal'";
    texto_ayuda += "\n&ensp;(" + TLNT.Idiomas._("ejemplo") + ": " + "(1) if (x1 == 0) else (x2 / x1)" + ")";

    // Variables de parámetros de energía eléctrica
    texto_ayuda += "\n" + TLNT.Idiomas._("Parámetros de energía eléctrica") + ":";
    texto_ayuda += "\n&ensp;- " + "'OMIE', 'MD'" + ": " + "Mercado Diario (península)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_GRC', 'MD_GRC'" + ": " + "Mercado Diario (Gran Canaria)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_LAF', 'MD_LAF'" + ": " + "Mercado Diario (Lanzarote - Fuerteventura)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_TEN', 'MD_TEN'" + ": " + "Mercado Diario (Tenerife)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_LAP', 'MD_LAP'" + ": " + "Mercado Diario (La Palma)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_LAG', 'MD_LAG'" + ": " + "Mercado Diario (La Gomera)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_ELH', 'MD_ELH'" + ": " + "Mercado Diario (El Hierro)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_CEU', 'MD_CEU'" + ": " + "Mercado Diario (Ceuta)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_MEL', 'MD_MEL'" + ": " + "Mercado Diario (Melilla)";
    texto_ayuda += "\n&ensp;- " + "'OMIE_BAL', 'MD_BAL'" + ": " + "Mercado Diario (Baleares)";
    texto_ayuda += "\n&ensp;- " + "'RPBF'" + ": " + "Restricciones PBF";
    texto_ayuda += "\n&ensp;- " + "'RTR'" + ": " + "Restricciones TR";
    texto_ayuda += "\n&ensp;- " + "'MI'" + ": " + "Mercado Intradiario";
    texto_ayuda += "\n&ensp;- " + "'RI'" + ": " + "Restricciones Intradiario";
    texto_ayuda += "\n&ensp;- " + "'RPAS'" + ": " + "Reserva Potencia Adicional Subir";
    texto_ayuda += "\n&ensp;- " + "'BS'" + ": " + "Banda Secundaria";
    texto_ayuda += "\n&ensp;- " + "'DM'" + ": " + "Desvíos Medidos";
    texto_ayuda += "\n&ensp;- " + "'SD'" + ": " + "Saldo Desvíos";
    texto_ayuda += "\n&ensp;- " + "'PC'" + ": " + "Pagos por capacidad";
    texto_ayuda += "\n&ensp;- " + "'SPO'" + ": " + "Saldo P.O.14.6";
    texto_ayuda += "\n&ensp;- " + "'FNUPG'" + ": " + "Fallo nominación UPG";
    texto_ayuda += "\n&ensp;- " + "'SI'" + ": " + "Servicio interrumpibilidad";
    texto_ayuda += "\n&ensp;- " + "'CPF'" + ": " + "Control factor de potencia";
    texto_ayuda += "\n&ensp;- " + "'IEB'" + ": " + "Incumplimiento energía balance";
    texto_ayuda += "\n&ensp;- " + "'PMHFS'" + ": " + "Precio medio horario final c.libre suma";
    texto_ayuda += "\n&ensp;- " + "'PMHFCRS'" + ": " + "Precio medio horario final com. ref. suma";
    texto_ayuda += "\n&ensp;- " + "'PMHFCLS'" + ": " + "Precio medio horario final suma";
    texto_ayuda += "\n&ensp;- " + "'COEFICIENTE_PERDIDAS'" + ": " + "Coeficientes de pérdidas";
    texto_ayuda += "\n&ensp;- " + "'PERDIDAS_Y'" + ": " + "Valores de pérdidas (%) (península)";
    texto_ayuda += "\n&ensp;&ensp;- " + "'Y'" + ": " + "Tipo de tarifa eléctrica ('30TD', '61TD', '62TD', '63TD', '64TD')";
    texto_ayuda += "\n&ensp;- " + "'PERDIDAS_X_Y'" + ": " + "Valores de pérdidas (%) (diferente de península)";
    texto_ayuda += "\n&ensp;&ensp;- " + "'Y'" + ": " + "Tipo de tarifa eléctrica ('30TD', '61TD', '62TD', '63TD', '64TD')";
    texto_ayuda += "\n&ensp;&ensp;- " + "'X'" + ": " + "Zona geográfica ('BALEARES', 'CANARIAS', 'CEUTA', 'MELILLA')";
    texto_ayuda += "\n&ensp;&ensp;- " + "'Y'" + ": " + "Tipo de tarifa eléctrica ('20A', '20DHA', '20DHS')";
    // Comodines
    texto_ayuda += "\n&ensp;- " + "'RESTRICCIONES'" + ": " + "RPBF + RI + RTR + RPAS + BS + SD + SPO";
    texto_ayuda += "\n&ensp;- " + "'PROCESOS_OS'" + ": " + "FNUPG + MI";

    // Variables ('extra') de energía eléctrica
    texto_ayuda += "\n" + TLNT.Idiomas._("Variables de energía eléctrica") + ":";
    texto_ayuda += "\n&ensp;- " + "'TRAMO'" + ": " + "Número de tramo";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}
*/

//function dame_texto_ayuda_formula_precio_consumo_cierre_tarifa_electrica() {
function dame_texto_ayuda_formula_precio_consumo_pass_through_tarifa_electrica() {
    var texto_ayuda = 
"<div class='help' role='dialog' aria-labelledby='title' align='left'>" +
"    <style>" +
"        .styled-table {" +
"          width: 100%;" +
"          border-collapse: collapse;" +
"          margin-top: 1rem;" +
"        }" +
"    " +
"        .styled-table th," +
"        .styled-table td {" +
"          border: 1px solid #ccc;" +
"          padding: 8px;" +
"          text-align: left;" +
"        }" +
"    " +
"        .styled-table th {" +
"          background-color: #f2f2f2;" +
"          font-weight: bold;" +
"        }" +
"        .hint {" +
"          font-size: 0.85em;" +
"          color: #666666;" +
"          margin-top: 0.5rem;" +
"          margin-bottom: 0.5rem;" +
"          display: block;" +
"        }" +
"       .param {" +
"            display: inline-block;" +
"           padding: 4px 8px;" +
"           margin: 2px;" +
"           background-color: #e6f2ff;" +
"           color: #5eb851;" +
"           border-radius: 12px;" +
"           font-size: 0.9em;" +
"           font-weight: 500;" +
"       }" +
"	#popup_title{" +
"	    font-weight: bold;" +
"       }" +
"    </style>" +
"<!-- TODO: TEMPORARY DISABLED" +
"    <div class='nav' aria-label='Secciones'>" +
"        <a href='#consideraciones'>Consideraciones</a>" +
"        <a href='#operadores'>Operadores</a>" +
"        <a href='#condicional'>Condicional</a>" +
"        <a href='#parametros'>Parámetros</a>" +
"        <a href='#variables-cierre'>Variables con cierre</a>" +
"    </div>" +
"-->" +
"    <br>" +
"    <section id='consideraciones'>" +
"    <h2>Consideraciones</h2>" +
"    <ul>" +
"        <li>El separador decimal es el punto.</li>" +
"        <li>Los precios se expresan en euros por megavatio hora (€/MWh).</li>" +
"        <li>Se pueden utilizar espacios en blanco y saltos de línea para mejorar la legibilidad de la fórmula.</li>" +
"    </ul>" +
"    </section>" +
"    " +
"    <br>" +
"    <div class='two-col separated'>" +
"        <section id='operadores'>" +
"        <h2>Operadores</h2>" +
"        <h3>Aritméticos</h3>" +
"        <table class='styled-table'>" +
"            <thead>" +
"                <tr>" +
"                    <th>Operador</th>" +
"                    <th>Descripción</th>" +
"                    <th>Ejemplo</th>" +
"                </tr>" +
"            </thead>" +
"            <tbody>" +
"                <tr>" +
"                    <td><code>( )</code></td>" +
"                    <td>Agrupar y forzar precedencia</td>" +
"                    <td><code>2 * (3 + 1) = 8</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>**</code></td>" +
"                    <td>Potencia</td>" +
"                    <td><code>2 ** 3 = 8</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>*</code></td>" +
"                    <td>Multiplicación</td>" +
"                    <td><code>3 * 4 = 12</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>/</code></td>" +
"                    <td>División</td>" +
"                    <td><code>5 / 2 = 2.5</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>+</code></td>" +
"                    <td>Suma</td>" +
"                    <td><code>2 + 3 = 5</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>-</code></td>" +
"                    <td>Resta</td>" +
"                    <td><code>5 - 2 = 3</code></td>" +
"                </tr>" +
"            </tbody>" +
"        </table>" +
"        <div class='hint'>" +
"            <ul>" +
"                <li>Precedencia: <code>( )</code> → <code>**</code> → <code>*</code>/<code>/</code> → <code>+</code>/<code>-</code>.</li>" +
"                <li>Usar paréntesis para controlar el orden; por ejemplo: <code>(a + b) / c</code></li>" +
"                <li>El valor absoluto se puede formar con potencias: <code>|a| = (a**2)**(1/2)</code></li>" +
"            </ul>" +
"        </div>" +
"        <br>" +
"        <h3>Booleanos</h3>" +
"        <table  class='styled-table'>" +
"            <thead>" +
"                <tr>" +
"                    <th>Operador</th>" +
"                    <th>Descripción</th>" +
"                    <th>Ejemplo</th>" +
"                </tr>" +
"            </thead>" +
"            <tbody>" +
"                <tr>" +
"                    <td><code>AND</code></td>" +
"                    <td>Conjunción</td>" +
"                    <td><code>true AND false = false</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>OR</code></td>" +
"                    <td>Disyunción</td>" +
"                    <td><code>true OR false = true</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <!-- TODO: NOT es un operador booleano REVISAR SI ESTA IMPLEMENTADO. Por las pruebas realizadas no está como NOT" +
"" +
"                    <td><code>NOT</code></td>" +
"                    <td>Negación</td>" +
"                    <td><code>NOT true → false</code></td>" +
"                    -->" +
"                </tr>" +
"            </tbody>" +
"        </table>" +
"        <br>" +
"        <h3>Comparación</h3>" +
"        <table class='styled-table'>" +
"            <thead>" +
"                <tr>" +
"                    <th>Operador</th>" +
"                    <th>Descripción</th>" +
"                    <th>Ejemplo</th>" +
"                </tr>" +
"            </thead>" +
"            <tbody>" +
"                <tr>" +
"                    <td><code>==</code></td>" +
"                    <td>Igualdad</td>" +
"                    <td><code>2 == 3 = false</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>!=</code></td>" +
"                    <td>Desigualdad</td>" +
"                    <td><code>2 != 3 = true</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>&gt;</code></td>" +
"                    <td>Mayor que</td>" +
"                    <td><code>2 &gt; 3 = false</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>&gt;=</code></td>" +
"                    <td>Mayor o igual</td>" +
"                    <td><code>2 &gt;= 3 = false</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>&lt;</code></td>" +
"                    <td>Menor que</td>" +
"                    <td><code>2 &lt; 3 = true</code></td>" +
"                </tr>" +
"                <tr>" +
"                    <td><code>&lt;=</code></td>" +
"                    <td>Menor o igual</td>" +
"                    <td><code>2 &lt;= 3 = true</code></td>" +
"                </tr>" +
"            </tbody>" +
"        </table>" +
"        </section>" +
"    </div>" +
"" +
"    <br>" +
"    <br>" +
"    <section id='condicional'>" +
"    <h2>Expresión condicional</h2>" +
"    <p>Permite definir un valor según se cumpla o no una condición.</p>" +
"    <p><strong>Instrucción</strong>: <code>(valor_si_verdadero if condicion else valor_si_falso)</code></p>" +
"    <p><strong>Ejemplo</strong>: <code>((1) if (x1 == 0) else (x2 / x1))</code> </p>" +
"    <div class='hint'>Si x1 es igual a 0, el resultado es 1; en caso contrario, el resultado es x2 dividido entre x1</div>" +
"    <div class='hint'>Todo el contenido de una expresión condicional tiene que ir entre paréntesis como en el ejemplo</div>" +
"    </section>" +
"" +
"    <br>" +
"    <br>" +
"    <section id='parametros'>" +
"    <h2>Parámetros de energía eléctrica</h2>" +
"    <ul class='description-text'>" +
"        <li>Los valores de los indicadores de ESIOS se obtienen a través de su API: <a href='https://api.esios.ree.es/' target='_blank' rel='noopener noreferrer'>API de ESIOS</a>. Se actualizan de estimados a ajustados a mes vencido, el día 16 del mes siguiente.</li>" +
"    </ul>" +
"" +
"    <table class='styled-table'>" +
"        <thead>" +
"            <tr>" +
"                <th>Códigos</th>" +
"                <th>Descripción</th>" +
"                <th>Notas</th>" +
"            </tr>" +
"        </thead>" +
"        <tbody>" +
"            <tr><td><span class='param'>OMIE</span><span class='param'>MD</span></div></td><td>Mercado Diario (península)</td><td><a href='https://www.esios.ree.es/es/analisis/805?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 805</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_GRC</span><span class='param'>MD_GRC</span></div></td><td>Mercado Diario (Gran Canaria)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8795?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8795</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_LAF</span><span class='param'>MD_LAF</span></div></td><td>Mercado Diario (Lanzarote - Fuerteventura)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8796?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8796</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_TEN</span><span class='param'>MD_TEN</span></div></td><td>Mercado Diario (Tenerife)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8797?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8797</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_LAP</span><span class='param'>MD_LAP</span></div></td><td>Mercado Diario (La Palma)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8798?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8798</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_LAG</span><span class='param'>MD_LAG</span></div></td><td>Mercado Diario (La Gomera)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8799?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8799</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_ELH</span><span class='param'>MD_ELH</span></div></td><td>Mercado Diario (El Hierro)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8800?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8800</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_CEU</span><span class='param'>MD_CEU</span></div></td><td>Mercado Diario (Ceuta)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8803?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8803</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_MEL</span><span class='param'>MD_MEL</span></div></td><td>Mercado Diario (Melilla)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8804?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8804</a></td></tr>" +
"            <tr><td><span class='param'>OMIE_BAL</span><span class='param'>MD_BAL</span></div></td><td>Mercado Diario (Baleares)</td><td><a href='https://www.esios.ree.es/es/analisis/1336_8823?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1336_8823</a></td></tr>" +
"" +
"            <tr><td><span class='param'>RPBF</span></div></td><td>Restricciones PBF</td><td><a href='https://www.esios.ree.es/es/analisis/806?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 806</a></td></tr>" +
"            <tr><td><span class='param'>RTR</span></div></td><td>Restricciones TR</td><td><a href='https://www.esios.ree.es/es/analisis/807?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 807</a></td></tr>" +
"            <tr><td><span class='param'>MI</span></div></td><td>Mercado Intradiario</td><td><a href='https://www.esios.ree.es/es/analisis/808?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 808</a></td></tr>" +
"            <tr><td><span class='param'>RI</span></div></td><td>Restricciones Intradiario</td><td><a href='https://www.esios.ree.es/es/analisis/809?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 809</a></td></tr>" +
"            <tr><td><span class='param'>RPAS</span></div></td><td>Reserva Potencia Adicional Subir</td><td><a href='https://www.esios.ree.es/es/analisis/810?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 810</a></td></tr>" +
"            <tr><td><span class='param'>BS</span></div></td><td>Banda Secundaria</td><td><a href='https://www.esios.ree.es/es/analisis/811?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 811</a></td></tr>" +
"            <tr><td><span class='param'>DM</span></div></td><td>Desvíos Medidos</td><td><a href='https://www.esios.ree.es/es/analisis/812?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 812</a></td></tr>" +
"            <tr><td><span class='param'>SD</span></div></td><td>Saldo Desvíos</td><td><a href='https://www.esios.ree.es/es/analisis/813?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 813</a></td></tr>" +
"            <tr><td><span class='param'>PC</span></div></td><td>Pagos por capacidad</td><td><a href='https://www.esios.ree.es/es/analisis/814?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 814</a></td></tr>" +
"            <tr><td><span class='param'>SPO</span></div></td><td>Saldo P.O.14.6</td><td><a href='https://www.esios.ree.es/es/analisis/815?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 815</a></td></tr>" +
"            <tr><td><span class='param'>FNUPG</span></div></td><td>Fallo nominación UPG</td><td><a href='https://www.esios.ree.es/es/analisis/816?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 816</a></td></tr>" +
"            <tr><td><span class='param'>SI</span></div></td><td>Servicio interrumpibilidad</td><td><a href='https://www.esios.ree.es/es/analisis/1277?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1277</a></td></tr>" +
"            <tr><td><span class='param'>CPF</span></div></td><td>Control factor de potencia</td><td><a href='https://www.esios.ree.es/es/analisis/1286?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1286</a></td></tr>" +
"            <tr><td><span class='param'>IEB</span></div></td><td>Incumplimiento energía balance</td><td><a href='https://www.esios.ree.es/es/analisis/1368?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 1368</a></td></tr>" +
"" +
"            <tr><td><span class='param'>PMHFS</span></div></td><td>Precio medio horario final suma</td><td><a href='https://www.esios.ree.es/es/analisis/10211?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 10211</a></td></tr>" +
"            <tr><td><span class='param'>PMHFCRS</span></div></td><td>Precio medio horario final com. ref. suma</td><td><a href='https://www.esios.ree.es/es/analisis/10212?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 10212</a></td></tr>" +
"            <tr><td><span class='param'>PMHFCLS</span></div></td><td>Precio medio horario final c. libre suma</td><td><a href='https://www.esios.ree.es/es/analisis/10214?' target='_blank' rel='noopener noreferrer'>ESIOS indicador 10214</a></td></tr>" +
"" +
"" +
"            <tr>" +
"                <td><span class='param'>COEFICIENTE_PERDIDAS</span></div></td>" +
"                <td>Coeficientes de pérdidas (tanto por 1)</td>" +
"                <td></td>" +
"            </tr>" +
"" +
"            <tr>" +
"                <td><span class='param'>PERDIDAS_Y</span></div></td>" +
"                <td>Valores de pérdidas (%) para península</td>" +
"                <td>Sustituir:<br><code>'Y'</code>∈ {'30TD', '61TD', '62TD', '63TD', '64TD'}</td>" +
"            </tr>" +
"            <tr>" +
"                <td><span class='param'>PERDIDAS_X_Y</span></div></td>" +
"                <td>Valores de pérdidas (%) para no península</td>" +
"                <td>" +
"                    <div>Sustituir:<br>" +
"                    <code>'X'</code> ∈ {'30TD', '61TD', '62TD', '63TD', '64TD'}<br>" +
"                    <code>'Y'</code>∈ {'BALEARES', 'CANARIAS', 'CEUTA', 'MELILLA'}" +
"                    </div>" +
"                </td>" +
"            </tr>" +
"" +
"            <tr>" +
"                <td><span class='param'>SERVICIOS_AJUSTE</span><span class='param'>SSAA</span></div></td>" +
"                <td>Agregado:<br><code>RPBF + RTR + BS + DM + SD + SPO + IEB + CPF</code></td>" +
"                <td></td>" +
"" +
"            </tr>" +
"" +
// Actualización, se borran estos agregados en el despliegue de tarifas de cierre (01/2026), ya no se usan
//"            <tr>" +
//"                <td><span class='param'>RESTRICCIONES</span></div></td>" +
//"                <td>Agregado:<br><code>RPBF + RI + RTR + RPAS + BS + SD + SPO</code></td>" +
//"                <td></td>" +
//"" +
//"            </tr>" +
//"            <tr>" +
//"                <td><span class='param'>PROCESOS_OS</span></div></td>" +
//"                <td>Agregado: <code>FNUPG + MI</code></td>" +
//"                <td></td>" +
//"            </tr>" +
"            <tr><td><span class='param'>TRAMO</span></div></td><td>Número de tramo tarifario</td><td>Toma valores de P1=1 a P6=6 (según periodos de la tarifa)</td></tr>" +
"        </tbody>" +
"    </table>" +
"    </section>" +
"" +
"" +
"    <br>" +
"    <br>" +
"    <section id='variables-cierre'>" +
"    <h2>Variables con cierre mensual</h2>" +
"    <p class='description-text'>" +
"        Estas variables se calculan a mes vencido para las tarifas con cierre mensual." +
"    </p>" +
"    <table class='styled-table'>" +
"        <thead><tr><th>Variable</th><th>Descripción</th></tr></thead>" +
"        <tbody>" +
"            <tr><td><span class='param'>CONSUMO_ACUMULADO_ANUAL</span><span class='param'>CAA</param></td><td>Consumo acumulado desde el inicio del año del mes de estudio</td></tr>" +
"            <tr><td><span class='param'>CONSUMO_ACUMULADO_INICIO</span><span class='param'>CAI</param></td><td>Consumo acumulado desde la fecha 'Fecha inicio de contrato' configurada en la pestaña de la tarifa</td></tr>" +
"            <tr><td><span class='param'>CONSUMO_MES_TOTAL</span><span class='param'>CMT</param></td><td>Total del consumo del mes en estudio.</td></tr>" +
"            <tr><td><span class='param'>CONSUMO_ACUMULADO_MES</span><span class='param'>CAM</param></td><td>Consumo acumulado del mes en estudio</td></tr>" +
"            <tr><td><span class='param'>CONSUMO_MES_POR_TRAMO_TOTAL_X </span><span class='param'>CMTT_X</param></td><td>Total del consumo para cada tramo tarifario 'X' del mes en estudio<br>Sustituir:<br><code>'X'</code>∈ {'1', '2', '3', '4', '5', '6'}</td></tr>" +
"            <tr><td><span class='param'>OMIE_PRECIO_MEDIO_PONDERADO_MES</span><span class='param'>OPMPM</param></td><td>Precio OMIE medio ponderado por el consumo del mes en estudio</td></tr>" +
"            <tr><td><span class='param'>OMIE_PRECIO_MEDIO_PONDERADO_MES_POR_TRAMO_X</span><span class='param'>OPMPMT_X</param></td><td>Precio OMIE medio ponderado por el consumo para cada tramo tarifario 'X' del mes en estudio<br>Sustituir:<br><code>'X'</code>∈ {'1', '2', '3', '4', '5', '6'}</td></tr>" +
"            <tr><td><span class='param'>OMIE_PRECIO_MEDIO_ARITMETICO_MES</span><span class='param'>OPMAM</param></td><td>Precio OMIE medio aritmético del mes en estudio</td></tr>" +
"            <tr><td><span class='param'>OMIE_PRECIO_MEDIO_ARITMETICO_MES_POR_TRAMO_X</span><span class='param'>OPMAMT_X</param></td><td>Precio OMIE medio aritmético para cada tramo tarifario 'X' del mes en estudio<br>Sustituir:<br><code>'X'</code>∈ {'1', '2', '3', '4', '5', '6'}</td></tr>" +
"            <tr><td><span class='param'>SERVICIOS_AJUSTE_PRECIO_ARITMETICO_MES</span><span class='param'>SAPAM</param></td><td>Precio medio de los Servicios de Ajuste para el mes en estudio</td></tr>" +
"        </tbody>" +
"    </table>" +
"    </section>" +
"    <!-- TODO: BORRAR si no se usa" +
"    <div class='tips'>" +
"        ********************************************************************************************************************<br>" +
"        ********************************************************************************************************************<br>" +
"        ********************************************************************************************************************<br>" +
"        PRUEBA ESTILO 'TIPS': Estas variables se calculan a mes vencido para las tarifas con cierre mensual.<br>" +
"        ********************************************************************************************************************<br>" +
"        ********************************************************************************************************************<br>" +
"        ********************************************************************************************************************<br>" +
"    </div> -->" +
"" +
"</div>";

return texto_ayuda;
}



// Ayuda de límites de consumo por tramo de tarifas de agua
function dame_texto_ayuda_limites_consumo_tramos_tarifa_agua() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los límites de consumo por tramos se especifican separados por comas");
    texto_ayuda += "\n(" + TLNT.Idiomas._("el último tramo no tiene límite y no se especifica") + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de precios de consumo por tramo de tarifas de agua
function dame_texto_ayuda_precios_consumo_tramos_tarifa_agua() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los precios de consumo por tramos se especifican separados por comas");

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de límites de consumo por tramo de conceptos adicionales de factura de tarifa
function dame_texto_ayuda_limites_consumo_tramos_concepto_adicional_factura_tarifa() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los límites de consumo por tramos se especifican separados por comas");
    texto_ayuda += "\n(" + TLNT.Idiomas._("el último tramo no tiene límite y no se especifica") + ")";
    texto_ayuda += "\n" + TLNT.Idiomas._("Los precios de consumo por tramos (coste) se especifican separados por comas");

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Módulo Proyectos
//


// Ayuda de tabla de proyectos
function dame_texto_ayuda_tabla_proyectos() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre el módulo de proyectos en el siguiente videotutorial") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_MODULO_PROYECTOS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tabla de líneas base
function dame_texto_ayuda_tabla_lineas_base() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Puede encontrar información sobre la creación de lineas base en los siguientes videotutoriales") + ": ";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de lineas base periódicas") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_LINEAS_BASE_PERIODICAS + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";
    texto_ayuda += "\n&ensp;- " + TLNT.Idiomas._("Creación de lineas base funcionales") + " ";
    texto_ayuda += "<a target='_blank' href='" + ENLACE_VIDEOTUTORIAL_CREACION_LINEAS_BASE_FUNCIONALES + "'><div class='boton_ayuda_videotutorial icon-youtube-play color-rojo'></div>" + "</a>";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de función de valores en líneas base
function dame_texto_ayuda_funcion_valores_lineas_base() {
    var operadores = [
        "()",
        "*",
        "/",
        "+",
        "-",
        "**",
        "log(x)"];
    var descripciones_operadores = [
        "paréntesis",
        "multiplicación",
        "división",
        "suma",
        "resta",
        "potencia",
        "logaritmo"];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Operadores") + ":";
    for (var i = 0; i < operadores.length; i++) {
        texto_ayuda += "\n&ensp;- " + operadores[i] + ": " + TLNT.Idiomas._(descripciones_operadores[i]);
    }

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de valores de prueba de la función de valores en líneas base
function dame_texto_ayuda_valores_prueba_funcion_valores_lineas_base() {
    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Los valores de prueba se separan por comas y en el orden de la lista de las variables");
    texto_ayuda += " (" + TLNT.Idiomas._("si no hay valores de prueba, se evalúa la función con todos los valores a 0 y no se muestra el resultado") + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Varios módulos
//


// Ayuda de calibración de valores de sensor y actuador
function dame_texto_ayuda_calibracion_sensor_actuador() {
    // Operaciones de calibración
    var operaciones_calibracion = [
        OPERACION_CALIBRACION_MULTIPLICACION,
        OPERACION_CALIBRACION_SUMA,
        OPERACION_CALIBRACION_VALOR_MAXIMO,
        OPERACION_CALIBRACION_VALOR_MINIMO];
    var descripciones_operaciones_calibracion = [
        "multiplicación",
        "suma",
        "valor máximo",
        "valor mínimo"];
    var parametros_operaciones_calibracion = [
        ["factor"],
        ["valor"],
        ["valor mínimo"],
        ["valor máximo"]];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Operaciones de calibración") + ":";
    for (var i = 0; i < operaciones_calibracion.length; i++) {
        texto_ayuda += "\n&ensp;- " + operaciones_calibracion[i] + ": " +
            TLNT.Idiomas._(descripciones_operaciones_calibracion[i]) + " (" + TLNT.Idiomas._("parámetros") + ": ";
        for (var j = 0; j < parametros_operaciones_calibracion[i].length; j++) {
            if (j > 0) {
                texto_ayuda += ", ";
            }
            texto_ayuda += TLNT.Idiomas._(parametros_operaciones_calibracion[i][j]);
        }
        texto_ayuda += ")";
    }
    texto_ayuda += "\n" + TLNT.Idiomas._("Las operaciones de calibración se separan por comas");
    texto_ayuda += "\n(" + TLNT.Idiomas._("ejemplo") + ": " + "mul:10, sum:1" + ")";

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


// Ayuda de tipos de dato ModBus de sensor y actuador
function dame_texto_ayuda_tipos_dato_modbus_sensor_actuador() {
    // Tipos de dato
    var tipos_dato_modbus = [
        TIPO_DATO_MODBUS_8BIT_INT,
        TIPO_DATO_MODBUS_8BIT_UINT,
        TIPO_DATO_MODBUS_16BIT_INT,
        TIPO_DATO_MODBUS_16BIT_UINT,
        TIPO_DATO_MODBUS_32BIT_INT,
        TIPO_DATO_MODBUS_32BIT_UINT,
        TIPO_DATO_MODBUS_32BIT_FLOAT,
        TIPO_DATO_MODBUS_64BIT_INT,
        TIPO_DATO_MODBUS_64BIT_UINT,
        TIPO_DATO_MODBUS_64BIT_FLOAT,
        TIPO_DATO_MODBUS_BITS];

    // Se crea el texto de la ayuda
    var texto_ayuda = TLNT.Idiomas._("Tipos de dato") + ":";
    for (var i = 0; i < tipos_dato_modbus.length; i++) {
        texto_ayuda += "\n&ensp;- " + tipos_dato_modbus[i];
    }

    // Se alinea a la izquierda y se devuelve
    texto_ayuda = dame_texto_ayuda_alineado_izquierda(texto_ayuda);
    return (texto_ayuda);
}


//
// Funciones auxiliares
//


// Alinea el texto de ayuda a la izquierda
function dame_texto_ayuda_alineado_izquierda(texto_ayuda) {
    texto_ayuda = "<div id='texto_ayuda' class='alineado-izda'>" + texto_ayuda + "</div>";
    return (texto_ayuda);
}


// Devuelve la descripción de la acción inicial
function dame_descripcion_accion_inicial(accion_inicial) {
    var descripcion = "";
    switch (accion_inicial) {
        case ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS: {
            descripcion = "Actualización periódica de widgets";
            break;
        }
        default: {
            descripcion = "Desconocido";
            break;
        }
    }

    return (TLNT.Idiomas._(descripcion));
}


// Devuelve la descripción del tipo de evento
function dame_descripcion_tipo_evento(tipo_evento) {
    var descripcion = "";
    switch (tipo_evento) {
        case TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO: {
            descripcion = "Incremento temporal mínimo";
            break;
        }
        case TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO: {
            descripcion = "Incremento temporal máximo";
            break;
        }
        case TIPO_EVENTO_VALOR_MINIMO: {
            descripcion = "Valor mínimo";
            break;
        }
        case TIPO_EVENTO_VALOR_MAXIMO: {
            descripcion = "Valor máximo";
            break;
        }
        case TIPO_EVENTO_VALORES_MINIMO_MAXIMO: {
            descripcion = "Valores mínimo y máximo";
            break;
        }
        case TIPO_EVENTO_INTERVALO_VALORES: {
            descripcion = "Intervalo de valores";
            break;
        }
        case TIPO_EVENTO_VALOR_EXACTO: {
            descripcion = "Valor exacto";
            break;
        }
        case TIPO_EVENTO_VALOR_DIFERENTE: {
            descripcion = "Valor diferente";
            break;
        }
        case TIPO_EVENTO_VALOR_EXACTO_BITS: {
            descripcion = "Valor exacto (bits)";
            break;
        }
        case TIPO_EVENTO_VALOR_DIFERENTE_BITS: {
            descripcion = "Valor diferente (bits)";
            break;
        }
        case TIPO_EVENTO_VALOR_REPETIDO: {
            descripcion = "Valor repetido";
            break;
        }
        case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL: {
            descripcion = "Incremento acumulado máximo (periodo de tiempo actual)";
            break;
        }
        case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO: {
            descripcion = "Incremento acumulado máximo (últimos periodos de tiempo)";
            break;
        }
        case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO: {
            descripcion = "Incremento acumulado máximo (últimos periodos de tiempo)";
            break;
        }
        case TIPO_EVENTO_LINEA_BASE: {
            descripcion = "Línea base";
            break;
        }
        case TIPO_EVENTO_PERFIL_HORARIO: {
            descripcion = "Perfil horario";
            break;
        }
        default: {
            descripcion = "Desconocido";
            break;
        }
    }

    return (TLNT.Idiomas._(descripcion));
}
