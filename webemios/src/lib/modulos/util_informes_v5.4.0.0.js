//
// Funciones de informes
//


// Comprobación de parámetros extra de campo de clase de sensor correctos
function comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo) {
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            switch (campo) {
                case CAMPO_GRADOS_HORA_CALEFACCION:
                case CAMPO_GRADOS_HORA_REFRIGERACION:
                case CAMPO_GRADOS_DIA_CALEFACCION:
                case CAMPO_GRADOS_DIA_REFRIGERACION: {
                    if (PATRON_NUMERO_REAL.test(parametros_extra_campo) == false) {
                        jAlert(TLNT.Idiomas._('La temperatura de referencia debe ser un número real'));
                        return (false);
                    }
                    break;
                }
            }
            break;
        }
    }
    return (true);
}


// Comprobación de fechas de inicio y fin correctas
function comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin) {
    if ($.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio) > $.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_fin)) {
        jAlert(TLNT.Idiomas._("La fecha de inicio es más reciente que la fecha de fin"));
        return (false);
    }
    else {
        if ((hora_inicio != null) && (hora_fin != null)) {
            if (+$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio) == +$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_fin)) {
                if (hora_inicio > hora_fin) {
                    jAlert(TLNT.Idiomas._("La hora de inicio es más reciente que la hora de fin"));
                    return (false);
                }
            }
        }
    }
    return (true);
}


// Comprobación de fechas de inicio y fin correctas de un usuario interno
function comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, hora_inicio, fecha_fin, hora_fin) {
    var resultado = {
        "res": "OK"
    };
    if ($.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio) > $.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_fin)) {
        resultado = {
            "res": "ERROR",
            "msg": TLNT.Idiomas._("La fecha de inicio es más reciente que la fecha de fin")
        };
    }
    else {
        if ((hora_inicio != null) && (hora_fin != null)) {
            if (+$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio) == +$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_fin)) {
                if (hora_inicio > hora_fin) {
                    resultado = {
                        "res": "ERROR",
                        "msg": TLNT.Idiomas._("La hora de inicio es más reciente que la hora de fin")
                    };
                }
            }
        }
    }
    return (resultado);
}


// Comprobación de fechas y número de días de periodos correctos
function comprueba_fechas_numero_dias_periodos_correctos(fecha_inicio_anterior, fecha_inicio_posterior, numero_dias_periodos) {
    if ($.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio_anterior) >= $.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio_posterior)) {
        jAlert(TLNT.Idiomas._("El inicio del periodo posterior debe ser más reciente que el inicio del periodo anterior"));
        return (false);
    }
    if (PATRON_NUMERO_NATURAL.test(numero_dias_periodos) == false) {
        jAlert(TLNT.Idiomas._("El número de días de duración de los periodos debe ser un valor numérico"));
        return (false);
    }
    if (numero_dias_periodos <= 0) {
		jAlert(TLNT.Idiomas._("El número de días de duración de los periodos debe ser mayor que 0"));
		return (false);
	}
    return (true);
}


// Comprobación de fechas y número de días de periodos correctos de un usuario interno
function comprueba_fechas_numero_dias_periodos_correctos_usuario_interno(fecha_inicio_anterior, fecha_inicio_posterior, numero_dias_periodos) {
    var resultado = {
        "res": "OK"
    };
    if ($.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio_anterior) >= $.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio_posterior)) {
        resultado = {
            "res": "ERROR",
            "msg": TLNT.Idiomas._("El inicio del periodo posterior debe ser más reciente que el inicio del periodo anterior")
        };
    }
    else {
        if (isNaN(numero_dias_periodos) == true) {
            resultado = {
                "res": "ERROR",
                "msg": TLNT.Idiomas._("No hay número de días de duración de los periodos")
            };
        }
        else {
            if (numero_dias_periodos <= 0) {
                resultado = {
                    "res": "ERROR",
                    "msg": TLNT.Idiomas._("El número de días de duración de los periodos debe ser mayor que 0")
                };
            }
        }
    }
    return (resultado);
}


// Comprobación de días de año de inicio y fin correctas
function comprueba_dias_anyo_inicio_fin_correctos(dia_anyo_inicio, dia_anyo_fin) {
    // Nota: Las cadenas con el formato de base de datos se pueden comprar para saber cual es anterior (primero mes y luego día)
    var dia_anyo_inicio_periodo_base_datos = convierte_formato_dia_anyo(dia_anyo_inicio, formato_dia_anyo_local_jquery_ui, FORMATO_DIA_ANYO_BASE_DATOS_JQUERY_UI);
    var dia_anyo_fin_periodo_base_datos = convierte_formato_dia_anyo(dia_anyo_fin, formato_dia_anyo_local_jquery_ui, FORMATO_DIA_ANYO_BASE_DATOS_JQUERY_UI);
    if (dia_anyo_inicio_periodo_base_datos > dia_anyo_fin_periodo_base_datos) {
        jAlert(TLNT.Idiomas._("El día de inicio es posterior al día de fin"));
        return (false);
    }
    return (true);
}


// Recupera la escala de colores para el mapa de calor
function dame_escala_colores_mapa_calor(colores_mapa_calor) {
    var escala_colores_mapa_calor = "";
    switch (colores_mapa_calor) {
        case COLORES_AZUL_ROJO: {
            escala_colores_mapa_calor = ESCALA_COLORES_AZUL_ROJO;
            break;
        }
        case COLORES_VERDE_ROJO: {
            escala_colores_mapa_calor = ESCALA_COLORES_VERDE_ROJO;
            break;
        }
        case COLORES_NEGRO_AMARILLO: {
            escala_colores_mapa_calor = ESCALA_COLORES_NEGRO_AMARILLO;
            break;
        }
        case COLORES_ROJO_VERDE: {
            escala_colores_mapa_calor = ESCALA_COLORES_ROJO_VERDE;
            break;
        }
        case COLORES_ROJO_AZUL: {
            escala_colores_mapa_calor = ESCALA_COLORES_ROJO_AZUL;
            break;
        }
        case COLORES_AMARILLO_AZUL: {
            escala_colores_mapa_calor = ESCALA_COLORES_AMARILLO_AZUL;
            break;
        }
        case COLORES_BLANCO_NEGRO: {
            escala_colores_mapa_calor = ESCALA_COLORES_BLANCO_NEGRO;
            break;
        }
    }
    return (escala_colores_mapa_calor);
}


// Devuelve los índices de los días de la semana entre semana
function dame_indices_dias_semana_entre_semana(nombres_dias_semana) {
    var indices_dias_semana_entre_semana = [];
    var nombre_sabado = TLNT.Idiomas._("Sábado");
    var nombre_domingo = TLNT.Idiomas._("Domingo");
    for (var i = 0; i < nombres_dias_semana.length; i++) {
        var nombre_dia_semana = nombres_dias_semana[i];
        if ((nombre_dia_semana != nombre_sabado) && (nombre_dia_semana != nombre_domingo)) {
            indices_dias_semana_entre_semana.push(i);
        }
    }
    return (indices_dias_semana_entre_semana);
}


// Devuelve los índices de los días entre semana de las fechas especificados
function dame_indices_dias_entre_semana_fechas(cadenas_fechas) {
    var indices_dias_entre_semana = [];
    for (var i = 0; i < cadenas_fechas.length; i++) {
        var fecha = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadenas_fechas[i]);
        var dia_semana_fecha = fecha.getDay();
        if ((dia_semana_fecha != 0) && (dia_semana_fecha != 6)) {
            indices_dias_entre_semana.push(i);
        }
    }
    return (indices_dias_entre_semana);
}


// Devuelve el ajuste del valor mínimo (valor mínimo y si hay que ajustarlo)
function dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, valor_minimo) {
    // Valor mínimo y ajuste de valor mínimo
    var ajustar_valor_minimo = true;
    switch (campo) {
        case CAMPO_INCREMENTO: {
            if (valor_minimo >= 0) {
                valor_minimo = 0;
            }
            ajustar_valor_minimo = false;
            break;
        }
    }
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            switch (campo) {
                case CAMPO_COSTE:
                case CAMPO_TRAMO: {
                    if (valor_minimo >= 0) {
                        valor_minimo = 0;
                    }
                    ajustar_valor_minimo = false;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            switch (campo) {
                case CAMPO_PENALIZABLE:
                case CAMPO_TRAMO: {
                    if (valor_minimo >= 0) {
                        valor_minimo = 0;
                    }
                    ajustar_valor_minimo = false;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            switch (campo) {
                case CAMPO_CONSUMO_ESTIMADO:
                case CAMPO_CONSUMO_REAL:
                case CAMPO_PENALIZABLE: {
                    if (valor_minimo >= 0) {
                        valor_minimo = 0;
                    }
                    ajustar_valor_minimo = false;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_GAS: {
            switch (campo) {
                case CAMPO_CONSUMO:
                case CAMPO_COSTE: {
                    if (valor_minimo >= 0) {
                        valor_minimo = 0;
                    }
                    ajustar_valor_minimo = false;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_AGUA: {
            switch (campo) {
                case CAMPO_INCREMENTO: {
                    if (valor_minimo >= 0) {
                        valor_minimo = 0;
                    }
                    ajustar_valor_minimo = false;
                    break;
                }
            }
            break;
        }
    }

    var ajuste_valor_minimo = {
        "valor_minimo": valor_minimo,
        "ajustar_valor_minimo": ajustar_valor_minimo};
    return (ajuste_valor_minimo);
}


// Devuelve el ajuste del valor máximo (valor máximo y si hay que ajustarlo)
function dame_ajuste_valor_maximo_grafica_valores_sensor(clase_sensor, campo, valor_maximo) {
    // Valor máximo y ajuste de valor máximo
    var ajustar_valor_maximo = true;
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            switch (campo) {
                case CAMPO_SOBREPOTENCIA: {
                    if (valor_maximo <= 0) {
                        valor_maximo = 0;
                    }
                    ajustar_valor_maximo = false;
                    break;
                }
            }
            break;
        }
    }

    var ajuste_valor_maximo = {
        "valor_maximo": valor_maximo,
        "ajustar_valor_maximo": ajustar_valor_maximo};
    return (ajuste_valor_maximo);
}


// Líneas de referencia de una clase de sensor y campo
function dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo) {
    var lineas_referencia = null;
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            switch (campo) {
                case CAMPO_TEMPERATURA: {
                    lineas_referencia = [];
                    lineas_referencia.push(
                        {
                            valor: 0,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(150, 150, 200, 0.5)",
                            texto_tooltip: 0 + " " + unidad_medida_temperatura
                        }
                    );
                    lineas_referencia.push(
                        {
                            valor: 100,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(200, 150, 150, 0.5)",
                            texto_tooltip: 100 + " " + unidad_medida_temperatura
                        }
                    );
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_HUMEDAD: {
            switch (campo) {
                case CAMPO_HUMEDAD: {
                    lineas_referencia = [];
                    lineas_referencia.push(
                        {
                            valor: 100,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(150, 150, 200, 0.5)",
                            texto_tooltip: 100 + " " + "%"
                        }
                    );
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            switch (campo) {
                case CAMPO_SOBREPOTENCIA: {
                    lineas_referencia = [];
                    lineas_referencia.push(
                        {
                            valor: 0,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(255, 150, 150, 0.5)",
                            texto_tooltip: TLNT.Idiomas._("Sobrepotencia cero")
                        }
                    );
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            switch (campo) {
                case CAMPO_COSENO_PHI: {
                    lineas_referencia = [];
                    lineas_referencia.push(
                        {
                            valor: MINIMO_COSENO_PHI_PENALIZABLE_1,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(255, 150, 150, 0.5)",
                            texto_tooltip: TLNT.Idiomas._("Valor penalizable") + " 1" + " (" + MINIMO_COSENO_PHI_PENALIZABLE_1 + ")"
                        }
                    );
                    lineas_referencia.push(
                        {
                            valor: MINIMO_COSENO_PHI_PENALIZABLE_2,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(255, 150, 150, 0.5)",
                            texto_tooltip: TLNT.Idiomas._("Valor penalizable") + " 2" + " (" + MINIMO_COSENO_PHI_PENALIZABLE_2 + ")"
                        }
                    );
                    break;
                }
                case CAMPO_COSENO_PHI_CAPACITIVA: {
                    lineas_referencia = [];
                    lineas_referencia.push(
                        {
                            valor: MINIMO_COSENO_PHI_PENALIZABLE_CAPACITIVA,
                            tipo: TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                            color: "rgba(255, 150, 150, 0.5)",
                            texto_tooltip: TLNT.Idiomas._("Valor penalizable") + " 1" + " (" + MINIMO_COSENO_PHI_PENALIZABLE_CAPACITIVA + ")"
                        }
                    );
                    break;
                }
            }
            break;
        }
    }
    return (lineas_referencia);
}


// Línea de referencia con la información especificada
function dame_linea_referencia_grafica_valores(valor, tipo, color, texto_tooltip) {
    var linea_referencia = {
        valor: valor,
        tipo: tipo,
        color: color
    };
    if (texto_tooltip != null) {
        linea_referencia[texto_tooltip] = texto_tooltip;
    }
    return (linea_referencia);
}


// Se establece el id de la tabla el id del contenedor más el id de la tabla
// (para los informes de plantillas de informes porque puede haber más de una tabla con el mismo id en diferentes contenedores)
function anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla) {
    var id_tabla = $("#" + id_contenedor_tabla).children(":first").attr('id');
    var id_tabla_modificado = id_contenedor_tabla + "__" + id_tabla;
    $("#" + id_tabla).attr("id", id_tabla_modificado);
    return (id_tabla_modificado);
}


//
// Funciones de formateo de textos de informes
//


// Formatea un texto de informe
function formatea_texto_informe_html(texto) {
    // 1. Sustituye los delimitadores de negrita por etiquetas de inicio y fin de negrita (sin caracteres de escape HTML)
    // 2. Se 'escapan' los caracteres HTML
    // 3. Se sustituyen los saltos de línea por '<br>'
    // 4. Se sustituyen las etiquetas de inicio y fin de negrita por '<b>' y '</b>' respectivamente
    var numero_ocurrencia_negrita = 0;
    while (true) {
        var indice_negrita = texto.indexOf("'''");
        if (indice_negrita == -1) {
            break;
        }
        numero_ocurrencia_negrita += 1;
        if (numero_ocurrencia_negrita % 2 == 1) {
            texto = texto.replace("'''", ETIQUETA_INICIO_NEGRITA_HTML_AUXILIAR);
        }
        else {
            texto = texto.replace("'''", ETIQUETA_FIN_NEGRITA_HTML_AUXILIAR);
        }
    }
    texto = escapeHtml(texto);
    texto = texto.replace(/\n/g, "<br>");
    texto = texto.replace(new RegExp(ETIQUETA_INICIO_NEGRITA_HTML_AUXILIAR, "g"), "<b>");
    texto = texto.replace(new RegExp(ETIQUETA_FIN_NEGRITA_HTML_AUXILIAR, "g"), "</b>");
    return (texto);
}


//
// Funciones auxiliares
//


// Información de dibujado del informe
function establece_informacion_dibujado_informe(modulo, tipo, informacion_extra) {
    modulo_informe_dibujado = modulo;
    tipo_informe_dibujado = tipo;
    informacion_extra_informe_dibujado = informacion_extra;
}


// Información de dibujado del informe
function establece_informacion_dibujado_elemento_plantilla_informe(info_elemento) {
    numero_elemento_plantilla_informe_dibujado = info_elemento["numero_elemento"];
}


// Borra la información de dibujado del informe
function borra_informacion_dibujado_elemento_plantilla_informe() {
    numero_elemento_plantilla_informe_dibujado = null;
}
