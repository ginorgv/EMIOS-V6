/*
 * Funciones de validaciones de parámetros de administración de actuadores
 *
 */


//
// Ubicaciones de interfaces de actuadores
//


// Devuelve la cadena de ubicación de interfaz de un actuador con el tipo y clase de interfaz que se le pasan como parámetros
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador(tipo_actuador, clase_interfaz_actuador) {
    var resultado = {};
    switch (tipo_actuador) {
        case TIPO_ACTUADOR_HARDWARE: {
            switch (clase_interfaz_actuador) {
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_modbus_ip();
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_modbus_serie();
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_PWM: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_pwm();
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_SIMULADO: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_simulado();
                    break;
                }
            }
            break;
        }
        case TIPO_ACTUADOR_SOFTWARE: {
            switch (clase_interfaz_actuador) {
                case CLASE_INTERFAZ_ACTUADOR_EMAIL: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_software_email();
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_software_modbus_ip();
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_SIMULADO: {
                    resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_software_simulado();
                    break;
                }
            }
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador hardware con la clase de interfaz 'Modbus IP' (tipo Hardware)
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_modbus_ip() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#encapsulado_clase_interfaz_modbus_ip_hardware_actuador").val(),
            $("#protocolo_clase_interfaz_modbus_ip_hardware_actuador").val(),
            $("#direccion_ip_clase_interfaz_modbus_ip_hardware_actuador").val(),
            $("#puerto_clase_interfaz_modbus_ip_hardware_actuador").val()].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador hardware con la clase de interfaz 'Modbus serie'
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_modbus_serie() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Velocidad
    if (parametros_correctos == true) {
        var velocidad =  $("#velocidad_clase_interfaz_modbus_serie_hardware_actuador").val();
        if ((parseInt(velocidad) < VALOR_MINIMO_VELOCIDAD_MODBUS_SERIE) ||
            (parseInt(velocidad) > VALOR_MAXIMO_VELOCIDAD_MODBUS_SERIE)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._('La velocidad es incorrecta') +
                " (" + TLNT.Idiomas._('rango de valores') + ": " +
                VALOR_MINIMO_VELOCIDAD_MODBUS_SERIE + " - " + VALOR_MAXIMO_VELOCIDAD_MODBUS_SERIE + ")";
        }
    }

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#encapsulado_clase_interfaz_modbus_serie_hardware_actuador").val(),
            velocidad,
            $("#numero_bits_parada_clase_interfaz_modbus_serie_hardware_actuador").val(),
            $("#paridad_clase_interfaz_modbus_serie_hardware_actuador").val()].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador hardware con la clase de interfaz 'PWM'
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_pwm() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador hardware con la clase de interfaz 'Simulado'
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_hardware_simulado() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador con la clase de interfaz 'E-mail'
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_software_email() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador con la clase de interfaz 'Modbus IP' (tipo Software)
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_software_modbus_ip() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#encapsulado_clase_interfaz_modbus_ip_software_actuador").val(),
            $("#protocolo_clase_interfaz_modbus_ip_software_actuador").val(),
            $("#direccion_ip_clase_interfaz_modbus_ip_software_actuador").val(),
            $("#puerto_clase_interfaz_modbus_ip_software_actuador").val()].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un actuador con la clase de interfaz 'Simulado'
function dame_cadena_ubicacion_interfaz_clase_interfaz_actuador_software_simulado() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


//
// Opciones de interfaces de actuadores
//


// Devuelve la cadena de opciones de interfaz de un actuador con el tipo y clase de interfaz que se le pasan como parámetros
function dame_cadena_opciones_interfaz_clase_interfaz_actuador(tipo_actuador, clase_interfaz_actuador, numero_valores_clase_actuador) {
    var resultado = {};
    switch (tipo_actuador) {
        case TIPO_ACTUADOR_HARDWARE: {
            switch (clase_interfaz_actuador) {
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE: {
                    resultado = dame_cadena_opciones_interfaz_clase_interfaz_actuador_modbus(numero_valores_clase_actuador);
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_PWM: {
                    resultado = dame_cadena_opciones_interfaz_clase_interfaz_actuador_hardware_pwm(numero_valores_clase_actuador);
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_SIMULADO: {
                    resultado = dame_cadena_opciones_interfaz_clase_interfaz_actuador_hardware_simulado(numero_valores_clase_actuador);
                    break;
                }
            }
            break;
        }
        case TIPO_ACTUADOR_SOFTWARE: {
            switch (clase_interfaz_actuador) {
                case CLASE_INTERFAZ_ACTUADOR_EMAIL: {
                    resultado = dame_cadena_opciones_interfaz_clase_interfaz_actuador_software_email(numero_valores_clase_actuador);
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP: {
                    resultado = dame_cadena_opciones_interfaz_clase_interfaz_actuador_modbus(numero_valores_clase_actuador);
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_SIMULADO: {
                    resultado = dame_cadena_opciones_interfaz_clase_interfaz_actuador_software_simulado(numero_valores_clase_actuador);
                    break;
                }
            }
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un actuador con clase de interfaz Modbus
function dame_cadena_opciones_interfaz_clase_interfaz_actuador_modbus(numero_valores_clase_actuador) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Parámetros de valores
    var id_controles = "clase_interfaz_modbus_actuador";
    var cadena_tipos_registros = replaceAll($("#tipos_registros_" + id_controles).val(), " ", "");
    var cadena_direcciones_dispositivos = replaceAll($("#direcciones_dispositivos_" + id_controles).val(), " ", "");
    var cadena_direcciones_registros = replaceAll($("#direcciones_registros_" + id_controles).val(), " ", "");
    var cadena_numeros_elementos = replaceAll($("#numeros_elementos_" + id_controles).val(), " ", "");
    var cadena_reversos_bytes = replaceAll($("#reversos_bytes_" + id_controles).val(), " ", "");
    var cadena_reversos_registros = replaceAll($("#reversos_registros_" + id_controles).val(), " ", "");
    var cadena_tipos_datos = replaceAll($("#tipos_datos_" + id_controles).val(), " ", "");
    var cadena_numeros_bits_iniciales = replaceAll($("#numeros_bits_iniciales_" + id_controles).val(), " ", "");

    var tipos_registros = cadena_tipos_registros.split(SEPARADOR_PARAMETROS_VALORES);
    var direcciones_dispositivos = cadena_direcciones_dispositivos.split(SEPARADOR_PARAMETROS_VALORES);
    var direcciones_registros = cadena_direcciones_registros.split(SEPARADOR_PARAMETROS_VALORES);
    var numeros_elementos = cadena_numeros_elementos.split(SEPARADOR_PARAMETROS_VALORES);
    var reversos_bytes = cadena_reversos_bytes.split(SEPARADOR_PARAMETROS_VALORES);
    var reversos_registros = cadena_reversos_registros.split(SEPARADOR_PARAMETROS_VALORES);
    var tipos_datos = cadena_tipos_datos.split(SEPARADOR_PARAMETROS_VALORES);
    var numeros_bits_iniciales = cadena_numeros_bits_iniciales.split(SEPARADOR_PARAMETROS_VALORES);

    // Se comprueba el número de valores
    if ((tipos_registros.length != numero_valores_clase_actuador) ||
        (direcciones_dispositivos.length != numero_valores_clase_actuador) ||
        (direcciones_registros.length != numero_valores_clase_actuador) ||
        (numeros_elementos.length != numero_valores_clase_actuador) ||
        (reversos_bytes.length != numero_valores_clase_actuador) ||
        (reversos_registros.length != numero_valores_clase_actuador) ||
        (tipos_datos.length != numero_valores_clase_actuador) ||
        (numeros_bits_iniciales.length != numero_valores_clase_actuador)) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._('El número de valores configurado no coincide con el número de valores del actuador') +
            " (" + TLNT.Idiomas._('número de valores del actuador') + ": " + numero_valores_clase_actuador + ")";
    }

    // Se comprueban los parámetros de cada uno de los valores
    var cadenas_parametros_valores = [];
    for (var i = 0; i < numero_valores_clase_actuador; i++) {
        if (parametros_correctos == true) {
            var tipos_registro_modbus_escritura = [
                TIPO_REGISTRO_MODBUS_HOLDING_REGISTER,
                TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS,
                TIPO_REGISTRO_MODBUS_COIL,
                TIPO_REGISTRO_MODBUS_COILS];
            var tipos_registro_modbus_lectura = [
                TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS,
                TIPO_REGISTRO_MODBUS_INPUT_REGISTERS,
                TIPO_REGISTRO_MODBUS_AUTO_BYTES];
            var tipo_registro = tipos_registros[i];
            var tipos_registro_escritura_lectura = tipo_registro.split(SEPARADOR_TIPOS_REGISTRO_ESCRITURA_LECTURA_MODBUS);
            var tipo_registro_escritura = null;
            var tipo_registro_lectura = null;
            switch (tipos_registro_escritura_lectura.length) {
                case 1: {
                    tipo_registro_escritura = tipos_registro_escritura_lectura[0];
                    break;
                }
                case 2: {
                    tipo_registro_escritura = tipos_registro_escritura_lectura[0];
                    tipo_registro_lectura = tipos_registro_escritura_lectura[1];
                    break;
                }
                default: {
                    parametros_correctos = false;
                    break;
                }
            }
            if (parametros_correctos == false) {
                descripcion_error = TLNT.Idiomas._('Los números de tipos de registros son incorrectos');
                break;
            }
            if (tipos_registro_modbus_escritura.indexOf(tipo_registro_escritura) == -1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los tipos de registros son incorrectos') +
                    " (" + TLNT.Idiomas._('valores disponibles') + ": " +
                    tipos_registro_modbus_escritura.join(", ") + ")";
                break;
            }
            if (tipo_registro_lectura != null) {
                if (tipos_registro_modbus_lectura.indexOf(tipo_registro_lectura) == -1) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._('Los tipos de registros de lectura son incorrectos') +
                        " (" + TLNT.Idiomas._('valores disponibles') + ": " +
                        tipos_registro_modbus_lectura.join(", ") + ")";
                    break;
                }
            }
        }

        // Dirección del dispositivo
        if (parametros_correctos == true) {
            var direccion_dispositivo = direcciones_dispositivos[i];
            if (PATRON_NUMERO_NATURAL.test(direccion_dispositivo) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Las direcciones de los dispositivos deben ser valores numéricos');
                break;
            }
            if ((parseInt(direccion_dispositivo) < VALOR_MINIMO_DIRECCION_DISPOSITIVO_MODBUS) ||
                (parseInt(direccion_dispositivo) > VALOR_MAXIMO_DIRECCION_DISPOSITIVO_MODBUS)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Las direcciones de los dispositivos son incorrectas') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_DIRECCION_DISPOSITIVO_MODBUS + " - " + VALOR_MAXIMO_DIRECCION_DISPOSITIVO_MODBUS + ")";
                break;
            }
        }

        // Dirección del registro (inicial)
        if (parametros_correctos == true) {
            var direccion_registro = direcciones_registros[i];
            if (PATRON_NUMERO_NATURAL.test(direccion_registro) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Las direcciones de los registros deben ser valores numéricos');
                break;
            }
            if ((parseInt(direccion_registro) < VALOR_MINIMO_DIRECCION_REGISTRO_MODBUS) ||
                (parseInt(direccion_registro) > VALOR_MAXIMO_DIRECCION_REGISTRO_MODBUS)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Las direcciones de los registros son incorrectas') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_DIRECCION_REGISTRO_MODBUS + " - " + VALOR_MAXIMO_DIRECCION_REGISTRO_MODBUS + ")";
                break;
            }
        }

        // Número de elementos
        if (parametros_correctos == true) {
            var numero_elementos = numeros_elementos[i];
            if (PATRON_NUMERO_NATURAL.test(numero_elementos) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los números de elementos deben ser valores numéricos');
                break;
            }
            if ((parseInt(numero_elementos) < VALOR_MINIMO_NUMERO_ELEMENTOS_MODBUS) ||
                (parseInt(numero_elementos) > VALOR_MAXIMO_NUMERO_ELEMENTOS_MODBUS)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los números de elementos son incorrectos') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_NUMERO_ELEMENTOS_MODBUS + " - " + VALOR_MAXIMO_NUMERO_ELEMENTOS_MODBUS + ")";
                break;
            }
        }

        // Reverso de bytes
        if (parametros_correctos == true) {
            var reverso_bytes = reversos_bytes[i];
            if (PATRON_NUMEROS_0_1.test(reverso_bytes) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los reversos de bytes deben ser 0 (desactivado) o 1 (activado)');
                break;
            }
        }

        // Reverso de registros
        if (parametros_correctos == true) {
            var reverso_registros = reversos_registros[i];
            if (PATRON_NUMEROS_0_1.test(reverso_registros) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los reversos de registros deben ser 0 (desactivado) o 1 (activado)');
                break;
            }
        }

        // Tipo de dato
        if (parametros_correctos == true) {
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
            var tipo_dato = tipos_datos[i];
            if (tipos_dato_modbus.indexOf(tipo_dato) == -1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los tipos de datos son incorrectos') +
                    " (" + TLNT.Idiomas._('valores disponibles') + ": " +
                    tipos_dato_modbus.join(", ") + ")";
                break;
            }
        }

        // Número de bit inicial
        if (parametros_correctos == true) {
            var numero_bit_inicial = numeros_bits_iniciales[i];
            if (numero_bit_inicial == -1) {
                if (((tipo_registro_escritura == TIPO_REGISTRO_MODBUS_HOLDING_REGISTER) || (tipo_registro_escritura == TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS)) &&
                    (tipo_dato == TIPO_DATO_MODBUS_BITS)) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._('Los números de bits iniciales son incorrectos');
                }
            }
            else {
                if (PATRON_NUMERO_NATURAL.test(numero_bit_inicial) == false) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._('Los números de bits iniciales deben ser valores numéricos');
                    break;
                }
                if (numero_bit_inicial != "") {
                    if ((parseInt(numero_bit_inicial) < VALOR_MINIMO_NUMERO_BIT_INICIAL_MODBUS) ||
                        (parseInt(numero_bit_inicial) > VALOR_MAXIMO_NUMERO_BIT_INICIAL_MODBUS)) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._('Los números de bits iniciales son incorrectos') +
                            " (" + TLNT.Idiomas._('rango de valores') + ": " +
                            VALOR_MINIMO_NUMERO_BIT_INICIAL_MODBUS + " - " + VALOR_MAXIMO_NUMERO_BIT_INICIAL_MODBUS + ")";
                    }
                }
                if ((tipo_registro_escritura == TIPO_REGISTRO_MODBUS_COIL) ||
                    (tipo_registro_escritura == TIPO_REGISTRO_MODBUS_COILS)) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._('La escritura del tipo de registro no utiliza el bit inicial');
                }
                if (tipo_dato != TIPO_DATO_MODBUS_BITS) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._('El tipo de dato no utiliza el bit inicial');
                }
            }
        }

        // Comprobación de tipo de registros y tipo de datos correcto
        if (parametros_correctos == true) {
            switch (tipo_registro_escritura) {
                case TIPO_REGISTRO_MODBUS_COIL:
                case TIPO_REGISTRO_MODBUS_COILS: {
                    parametros_correctos = (tipo_dato == TIPO_DATO_MODBUS_BITS);
                    break;
                }
            }
            if (parametros_correctos == false) {
                descripcion_error = TLNT.Idiomas._('Los tipos de registros y los tipos de datos no coinciden');
                break;
            }
        }

        // Comprobación de número de elementos y tipo de registro
        if (parametros_correctos == true) {
            switch (tipo_registro_escritura) {
                case TIPO_REGISTRO_MODBUS_COIL:
                case TIPO_REGISTRO_MODBUS_HOLDING_REGISTER: {
                    parametros_correctos = (numero_elementos == 1);
                    break;
                }
            }
            if (parametros_correctos == false) {
                descripcion_error = TLNT.Idiomas._('Los números de elementos y los tipos de registro no coinciden');
                break;
            }
        }

        // Comprobación de número de elementos y tipo de dato correcto
        if (parametros_correctos == true) {
            switch (tipo_dato) {
                case TIPO_DATO_MODBUS_8BIT_INT:
                case TIPO_DATO_MODBUS_8BIT_UINT:
                case TIPO_DATO_MODBUS_16BIT_INT:
                case TIPO_DATO_MODBUS_16BIT_UINT: {
                    parametros_correctos = (numero_elementos == 1);
                    break;
                }
                case TIPO_DATO_MODBUS_32BIT_INT:
                case TIPO_DATO_MODBUS_32BIT_UINT:
                case TIPO_DATO_MODBUS_32BIT_FLOAT: {
                    parametros_correctos = (numero_elementos == 2);
                    break;
                }
                case TIPO_DATO_MODBUS_64BIT_INT:
                case TIPO_DATO_MODBUS_64BIT_UINT:
                case TIPO_DATO_MODBUS_64BIT_FLOAT: {
                    parametros_correctos = (numero_elementos == 4);
                    break;
                }
                case TIPO_DATO_MODBUS_BITS: {
                    parametros_correctos = (numero_elementos == 1);
                    break;
                }
            }
            if (parametros_correctos == false) {
                descripcion_error = TLNT.Idiomas._('Los números de elementos y los tipos de datos no coinciden');
                break;
            }
        }

        // Parámetros del valor
        var parametros_valor = [
            tipo_registro,
            direccion_dispositivo,
            direccion_registro,
            numero_elementos,
            reverso_bytes,
            reverso_registros,
            tipo_dato,
            numero_bit_inicial];
        var cadena_parametros_valor = parametros_valor.join(SEPARADOR_PARAMETROS_SIMPLES);
        cadenas_parametros_valores.push(cadena_parametros_valor);
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = cadenas_parametros_valores.join(SEPARADOR_PARAMETROS_VALORES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un actuador hardware con la clase de interfaz 'PWM'
function dame_cadena_opciones_interfaz_clase_interfaz_actuador_hardware_pwm(numero_valores_clase_actuador) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Números de pines
    var cadena_numeros_pines = replaceAll($("#numeros_pines_clase_interfaz_pwm_actuador").val(), " ", "");
    var numeros_pines = cadena_numeros_pines.split(SEPARADOR_PARAMETROS_VALORES);

    // Se comprueba el número de pines
    if (numeros_pines.length != numero_valores_clase_actuador) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._('El número de pines configurado no coincide con el número de valores del actuador') +
            " (" + TLNT.Idiomas._('número de valores del actuador') + ": " + numero_valores_clase_actuador + ")";
    }

    // Números de pines
    if (parametros_correctos == true) {
        for (var i = 0; i < numeros_pines.length; i++) {
            var numero_pin = numeros_pines[i];
            if (PATRON_NUMERO_ENTERO.test(numero_pin) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los números de pines deben ser valores numéricos iguales o mayores que 0');
                break;
            }
            if ((parseInt(numero_pin) < VALOR_MINIMO_NUMERO_PIN_PWM) ||
                (parseInt(numero_pin) > VALOR_MAXIMO_NUMERO_PIN_PWM)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('Los números de pines son incorrectos') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_NUMERO_PIN_PWM + " - " + VALOR_MAXIMO_NUMERO_PIN_PWM + ")";
                break;
            }
        }
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = cadena_numeros_pines;
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un actuador hardware con la clase de interfaz 'Simulado'
function dame_cadena_opciones_interfaz_clase_interfaz_actuador_hardware_simulado(numero_valores_clase_actuador) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un actuador software con la clase de interfaz 'E-mail'
function dame_cadena_opciones_interfaz_clase_interfaz_actuador_software_email(numero_valores_clase_actuador) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Se comprueba el número de valores
    if (numero_valores_clase_actuador != 1) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._('El número de valores del interfaz no coincide con el número de valores del actuador') +
            " (" + TLNT.Idiomas._('número de valores del actuador') + ": " + numero_valores_clase_actuador + ")";
    }

    // Dirección e-mail del remitente
    if (parametros_correctos == true) {
        var direccion_remitente = $("#direccion_remitente_clase_interfaz_email_actuador").val();
        if (comprueba_longitud_cadena(direccion_remitente, NUMERO_MAXIMO_CARACTERES_DIRECCION_EMAIL) == false) {
            $("#direccion_remitente_clase_interfaz_email_actuador").addClass('data-check-failed');
            parametros_correctos = false;
        }
        if (parametros_correctos == true) {
            direccion_remitente = direccion_remitente.trim();
            if (PATRON_DIRECCION_EMAIL.test(direccion_remitente) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('La dirección e-mail del remitente es incorrecta');
            }
        }
    }

    // Direcciones e-mail destino
    if (parametros_correctos == true) {
        var cadena_direcciones_destino = $("#direcciones_destino_clase_interfaz_email_actuador").val();
        if (comprueba_longitud_cadena(cadena_direcciones_destino, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $("#direcciones_destino_clase_interfaz_email_actuador").addClass('data-check-failed');
            parametros_correctos = false;
        }
        if (parametros_correctos == true) {
            var direcciones_destino = cadena_direcciones_destino.split(SEPARADOR_DIRECCIONES_EMAIL);
            for (var i = 0; i < direcciones_destino.length; i++) {
                direcciones_destino[i] = direcciones_destino[i].trim();
                if (PATRON_DIRECCION_EMAIL.test(direcciones_destino[i]) == false) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._('Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma');
                    break;
                }
            }
            cadena_direcciones_destino = direcciones_destino.join(SEPARADOR_DIRECCIONES_EMAIL);
        }
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = [
            direccion_remitente,
            cadena_direcciones_destino].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un actuador software con la clase de interfaz 'Simulado'
function dame_cadena_opciones_interfaz_clase_interfaz_actuador_software_simulado(numero_valores_clase_actuador) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


//
// Funciones auxiliares
//


function dame_numero_valores_clase_actuador(clase_actuador) {
    var numero_valores_clase_actuador = null;
    switch (clase_actuador) {
        case CLASE_ACTUADOR_MENSAJE: {
            numero_valores_clase_actuador = 1;
            break;
        }
        case CLASE_ACTUADOR_INTERRUPTOR: {
            numero_valores_clase_actuador = 1;
            break;
        }
        case CLASE_ACTUADOR_TELEPOSTE: {
            numero_valores_clase_actuador = 6;
            break;
        }
        case CLASE_ACTUADOR_LUZ_GRADUAL_4: {
            numero_valores_clase_actuador = 4;
            break;
        }
        case CLASE_ACTUADOR_GENERICA: {
            numero_valores_clase_actuador = 1;
            break;
        }
        default: {
            break;
        }
    }
    return (numero_valores_clase_actuador);
}


