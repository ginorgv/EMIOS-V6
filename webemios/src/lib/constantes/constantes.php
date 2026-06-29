<?php
    // Evita la doble inclusión (por diferencias en la resolución de rutas)
    if (defined('CONSTANTES_PRINCIPAL_YA_INCLUIDO')) { return; }
    define('CONSTANTES_PRINCIPAL_YA_INCLUIDO', true);

    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');


    // Versión de la WEB
    define("VERSION_WEB", "6.0.0.0");

    // Número y fecha de liberación de la WEB
    define("NUMERO_LIBERACION_WEB", "R1");
    define("FECHA_LIBERACION_WEB", "20/09/2021");

    // Título de la WEB
    define("TITULO_WEB", "EMIOS");


    //
    // Formatos de fecha
    //


    // Formatos de fechas locales (jqplot - javascript)
    define("FORMATO_FECHA_LOCAL_DIA_MES_ANYO_JQPLOT_JAVASCRIPT", "%d/%m/%Y");
    define("FORMATO_FECHA_LOCAL_MES_DIA_ANYO_JQPLOT_JAVASCRIPT", "%m/%d/%Y");
    define("FORMATO_FECHA_LOCAL_ANYO_MES_DIA_JQPLOT_JAVASCRIPT", "%Y/%m/%d");
    define("FORMATO_DIA_ANYO_LOCAL_MES_DIA_JQPLOT_JAVASCRIPT", "%m/%d");
    define("FORMATO_DIA_ANYO_LOCAL_DIA_MES_JQPLOT_JAVASCRIPT", "%d/%m");

    // Formatos de fechas locales (jquery-ui - javascript)
    define("FORMATO_FECHA_LOCAL_DIA_MES_ANYO_JQUERY_UI_JAVASCRIPT", "dd/mm/yy");
    define("FORMATO_FECHA_LOCAL_MES_DIA_ANYO_JQUERY_UI_JAVASCRIPT", "mm/dd/yy");
    define("FORMATO_FECHA_LOCAL_ANYO_MES_DIA_JQUERY_UI_JAVASCRIPT", "yy/mm/dd");
    define("FORMATO_DIA_ANYO_LOCAL_MES_DIA_JQUERY_UI_JAVASCRIPT", "mm/dd");
    define("FORMATO_DIA_ANYO_LOCAL_DIA_MES_JQUERY_UI_JAVASCRIPT", "dd/mm");


    //
    // Local
    //


    // Identificadores de separadores de miles
    define("ID_SEPARADOR_MILES_COMA", "COMA");
    define("ID_SEPARADOR_MILES_PUNTO", "PUNTO");
    define("ID_SEPARADOR_MILES_ESPACIO", "ESPACIO");

    // Identificadores de puntos decimales
    define("ID_PUNTO_DECIMAL_PUNTO", "PUNTO");
    define("ID_PUNTO_DECIMAL_COMA", "COMA");

    // Formateado de números por defecto
    define("ID_SEPARADOR_MILES_DEFECTO", ID_SEPARADOR_MILES_PUNTO);
    define("ID_PUNTO_DECIMAL_DEFECTO", ID_PUNTO_DECIMAL_COMA);


    //
    // Unidades, mediciones y países de tarifas
    //


    // Unidades por defecto
    define("MONEDA_DEFECTO", "€");
    define("UNIDAD_MEDIDA_TEMPERATURA_DEFECTO", "ºC");
    define("UNIDAD_MEDIDA_VELOCIDAD_DEFECTO", "km/h");

    // Países (para tarifas)
    define("PAIS_NINGUNO", "NINGUNO");
    define("PAIS_ESPANYA", "ESPANYA");
    define("PAIS_PORTUGAL", "PORTUGAL");

    // Países de tarifas
    define("PAIS_TARIFAS_ELECTRICAS_DEFECTO", PAIS_ESPANYA);
    define("PAIS_TARIFAS_GAS_DEFECTO", PAIS_ESPANYA);
    define("PAIS_TARIFAS_AGUA_DEFECTO", PAIS_ESPANYA);

    // Mediciones
    define("MEDICION_NINGUNA", "ninguna");
    define("MEDICION_ELECTRICIDAD", "electricidad");
    define("MEDICION_GAS", "gas");
    define("MEDICION_AGUA", "agua");

    // Medición por defecto
    define("MEDICION_DEFECTO", MEDICION_ELECTRICIDAD);


    //
    // Temas
    //


    // Tema por defecto (verde)
    define("TEMA_DEFECTO", "DEFECTO");

    // Temas
    define("TEMA_NARANJA", "NARANJA");
    define("TEMA_ROJO", "ROJO");
    define("TEMA_TURQUESA", "TURQUESA");
    define("TEMA_AZUL_CLARO", "AZUL_CLARO");
    define("TEMA_AZUL", "AZUL");
    define("TEMA_MORADO", "MORADO");
    define("TEMA_MAGENTA", "MAGENTA");
    define("TEMA_VERDE", "VERDE");
    define("TEMA_MARRON", "MARRON");
    define("TEMA_GRIS", "GRIS");
    define("TEMA_NEGRO", "NEGRO");

    // Colores de tema por defecto (verde)
    define("COLOR_TEMA_DEFECTO_OSCURO", "#298B21");
    define("COLOR_TEMA_DEFECTO_INTERMEDIO", "#52A652");
    define("COLOR_TEMA_DEFECTO_CLARO", "#5AB55A");
    define("COLOR_TEMA_DEFECTO_FONDO", "#F0F0F0");
    define("COLOR_TEMA_DEFECTO_FONDO_CLARO", "#FFFFFF");

    // Colores de temas
    // - Orden de colores: http://www.todocoleccion.net/coleccionismo/caja-metal-12-pinturas-lapices-colores-plastidecor-conte-nueva~x32005843
    define("COLOR_TEMA_NARANJA_OSCURO", "#ED7925");
    define("COLOR_TEMA_NARANJA_INTERMEDIO", "#BF831B");
    define("COLOR_TEMA_NARANJA_CLARO", "#CF9735");
    define("COLOR_TEMA_NARANJA_FONDO", "#F0F0F0");
    define("COLOR_TEMA_NARANJA_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_ROJO_OSCURO", "#B0251E");
    define("COLOR_TEMA_ROJO_INTERMEDIO", "#A6554A");
    define("COLOR_TEMA_ROJO_CLARO", "#BE675C");
    define("COLOR_TEMA_ROJO_FONDO", "#F0F0F0");
    define("COLOR_TEMA_ROJO_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_TURQUESA_OSCURO", "#21828B");
    define("COLOR_TEMA_TURQUESA_INTERMEDIO", "#5299A6");
    define("COLOR_TEMA_TURQUESA_CLARO", "#5BA7B5");
    define("COLOR_TEMA_TURQUESA_FONDO", "#F0F0F0");
    define("COLOR_TEMA_TURQUESA_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_AZUL_CLARO_OSCURO", "#1B8FD1");
    define("COLOR_TEMA_AZUL_CLARO_INTERMEDIO", "#0077B9");
    define("COLOR_TEMA_AZUL_CLARO_CLARO", "#43ABE9");
    define("COLOR_TEMA_AZUL_CLARO_FONDO", "#F0F0F0");
    define("COLOR_TEMA_AZUL_CLARO_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_AZUL_OSCURO", "#1D365D");
    define("COLOR_TEMA_AZUL_INTERMEDIO", "#3667AF");
    define("COLOR_TEMA_AZUL_CLARO", "#618CCD");
    define("COLOR_TEMA_AZUL_FONDO", "#F0F0F0");
    define("COLOR_TEMA_AZUL_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_MORADO_OSCURO", "#7B2D7F");
    define("COLOR_TEMA_MORADO_INTERMEDIO", "#9D5B9C");
    define("COLOR_TEMA_MORADO_CLARO", "#AB65AB");
    define("COLOR_TEMA_MORADO_FONDO", "#F0F0F0");
    define("COLOR_TEMA_MORADO_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_MAGENTA_OSCURO", "#DD2A90");
    define("COLOR_TEMA_MAGENTA_INTERMEDIO", "#E9279C");
    define("COLOR_TEMA_MAGENTA_CLARO", "#ED4BAD");
    define("COLOR_TEMA_MAGENTA_FONDO", "#F0F0F0");
    define("COLOR_TEMA_MAGENTA_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_VERDE_OSCURO", "#298B21");
    define("COLOR_TEMA_VERDE_INTERMEDIO", "#52A652");
    define("COLOR_TEMA_VERDE_CLARO", "#5AB55A");
    define("COLOR_TEMA_VERDE_FONDO", "#F0F0F0");
    define("COLOR_TEMA_VERDE_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_MARRON_OSCURO", "#A1611D");
    define("COLOR_TEMA_MARRON_INTERMEDIO", "#B28B54");
    define("COLOR_TEMA_MARRON_CLARO", "#BE9760");
    define("COLOR_TEMA_MARRON_FONDO", "#F0F0F0");
    define("COLOR_TEMA_MARRON_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_GRIS_OSCURO", "#777777");
    define("COLOR_TEMA_GRIS_INTERMEDIO", "#9F9F9F");
    define("COLOR_TEMA_GRIS_CLARO", "#AAAAAA");
    define("COLOR_TEMA_GRIS_FONDO", "#F0F0F0");
    define("COLOR_TEMA_GRIS_FONDO_CLARO", "#FFFFFF");

    define("COLOR_TEMA_NEGRO_OSCURO", "#000000");
    define("COLOR_TEMA_NEGRO_INTERMEDIO", "#464646");
    define("COLOR_TEMA_NEGRO_CLARO", "#656565");
    define("COLOR_TEMA_NEGRO_FONDO", "#F0F0F0");
    define("COLOR_TEMA_NEGRO_FONDO_CLARO", "#FFFFFF");

    // Color de contenido por defecto
    define("COLOR_CONTENIDO_DEFECTO", "#000000");

    // Paletas de colores de gráficas
    define("PALETA_COLORES_GRAFICAS_DEFECTO", "DEFECTO");
    define("PALETA_COLORES_GRAFICAS_ORIGINAL", "ORIGINAL");
    define("PALETA_COLORES_GRAFICAS_ALTO_CONTRASTE", "ALTO_CONTRASTE");
    define("PALETA_COLORES_GRAFICAS_EJENER", "COLORES_EJENER");

    // Tamaños de letra
    // Nota: Min 12 (si no no se ven los selects ni sus iconos, ademas de que la letra es diminuta)
    // Nota: Max 22 (va todo bien y la letra ya es muy grande)
    define("TAMANYO_LETRA_MINIMO", "12");
    define("TAMANYO_LETRA_MAXIMO", "22");
    define("TAMANYO_LETRA_DEFECTO", "15");

    // Tamaño de letra de informes fichero
    define("TAMANYO_LETRA_INFORMES_FICHERO", "13");


    //
    // Constantes de valores
    //


    // Valores SI/NO
    define("VALOR_SI", 1);
    define("VALOR_NO", 0);

    // Valores para el envío y la generación de informes
    define("VALOR_FALLO_GENERACION", 2);
    define("VALOR_FALLO_ENVIO", 3);


    //
    // General
    //


    // Versión de las bases de datos
    define("VERSION_BASE_DATOS_RED", "6.0.0.0");
    define("VERSION_BASE_DATOS_DATOS", "6.0.0.0");

    // Versiones defecto de la pasarela
    define("VERSION_FUENTES_DISPOSITIVOS_DEFECTO", "4.0.0.0");
    define("VERSION_FUENTES_WEB_DISPOSITIVOS_DEFECTO", "4.0.0.0");

    // Parámetros de caducidad por defecto
    define("NUMERO_MESES_VALORES_TIEMPO_REAL_DEFECTO", 12);
    define("NUMERO_MESES_VALORES_CUARTOSHORA_DEFECTO", 36);
    define("NUMERO_MESES_VALORES_HORAS_DEFECTO", 36);
    define("NUMERO_MESES_VALORES_DIAS_DEFECTO", 60);
    define("NUMERO_MESES_DEFECTO_VALORES_MESES", 120);
    define("ENVIAR_VALORES_CADUCADOS_TIEMPO_REAL_DEFECTO", VALOR_NO);
    define("ENVIAR_VALORES_CADUCADOS_CUARTOSHORA_DEFECTO", VALOR_NO);
    define("ENVIAR_VALORES_CADUCADOS_HORAS_DEFECTO", VALOR_NO);
    define("DIRECCION_EMAIL_ENVIO_VALORES_CADUCADOS_DEFECTO", "");
    define("NUMERO_MESES_ACCIONES_USUARIO_DEFECTO", 12);
    define("NUMERO_MESES_ACTIVACIONES_DEFECTO", 12);

    // Tipos de bases de datos
    define("TIPO_BASE_DATOS_RED", "Red");
    define("TIPO_BASE_DATOS_DATOS", "Datos");

    // Valores de configuración del fichero de log
    define("NOMBRE_FICHERO_LOG", "web_emios");
    define("TAMANYO_MAXIMO_FICHERO_LOG", "10MB");
    define("NUMERO_FICHEROS_LOG", "5");
    define("NIVEL_LOG", "INFO");

    // Ficheros de configuración
    define("RUTA_FICHERO_INI", "/rsc/config/config.ini");
    define("NOMBRE_FICHERO_INI_SERVIDOR_EMIOS", "servidor_emios.ini");

    // MQTT
    define("PUERTO_SERVIDOR_MQTT", 	1883);

    // Entradas del fichero de configuración
    define("INI_IP_BASE_DATOS_RED", "ip_base_datos_red");
    define("INI_PUERTO_BASE_DATOS_RED", "puerto_base_datos_red");
    define("INI_NOMBRE_BASE_DATOS_RED", "nombre_base_datos_red");
    define("INI_USUARIO_BASE_DATOS_RED", "usuario_base_datos_red");
    define("INI_CONTRASENYA_BASE_DATOS_RED", "contrasenya_base_datos_red");

    define("INI_IP_BASE_DATOS_DATOS", "ip_base_datos_datos");
    define("INI_PUERTO_BASE_DATOS_DATOS", "puerto_base_datos_datos");
    define("INI_NOMBRE_BASE_DATOS_DATOS", "nombre_base_datos_datos");
    define("INI_USUARIO_BASE_DATOS_DATOS", "usuario_base_datos_datos");
    define("INI_CONTRASENYA_BASE_DATOS_DATOS", "contrasenya_base_datos_datos");

    define("INI_IP_SERVIDOR_EMIOS", "ip_servidor_emios");
    define("INI_WEB_EMIOS", "web_emios");

    define("INI_RUTA_PROCESADO_EMIOS", "ruta_procesado_emios");

    // Formatos de fechas
    define("FORMATO_FECHA_BASE_DATOS", "Y-m-d");
    define("FORMATO_FECHA_HORA_BASE_DATOS", "Y-m-d H:i:s");
    define("FORMATO_FECHA_JQPLOT", "Y-m-d");
    define("FORMATO_FECHA_HORA_JQPLOT", "Y-m-d H:i:s");
    define("FORMATO_FECHA_HORA_FICHERO", "Ymd_His");
    define("FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO", "Ymd_Hi");
    define("FORMATO_FECHA_FUNCIONES", "Y-m-d");
    define("FORMATO_FECHA_HORA_FUNCIONES", "Y-m-d H:i:s");
    define("FORMATO_DIA_ANYO_BASE_DATOS", "m-d");
    define("FORMATO_FECHA_HORA_FICHERO_CSV", "d-m-Y, H:i:s");
    define("FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV", "d-m-Y, H:i");
    define("FORMATO_FECHA_DATE_JAVASCRIPT", "Y-m-d");

    // Tipos de mensaje (en ventanas)
    define("TIPO_MENSAJE_INFORMACION", "informacion");
    define("TIPO_MENSAJE_AVISO", "aviso");

    // Perfiles de usuario
    define("PERFIL_USUARIO_TODOS", "todos");
    define("PERFIL_USUARIO_ESTANDAR", "estandar");
    define("PERFIL_USUARIO_ADMINISTRADOR", "admin");
    define("PERFIL_USUARIO_SUPERADMINISTRADOR", "superadmin");

    // Tipos de contraseñas de usuario
    define("TIPO_CONTRASENYA_USUARIO_PERSONAL", "personal");
    define("TIPO_CONTRASENYA_USUARIO_ADMINISTRADOR", "admin");
    define("TIPO_CONTRASENYA_USUARIO_SUPERADMINISTRADOR", "superadmin");


    //
    // Módulos
    //


    // Módulos
    define("MODULO_ADMINISTRACION",	"administracion");
    define("MODULO_MONITORIZACION",	"monitorizacion");
    define("MODULO_PERSONAL", "personal");
    define("MODULO_RED", "red");
    define("MODULO_LOCALIZACIONES", "localizaciones");
    define("MODULO_SENSORES", "sensores");
    define("MODULO_ACTUADORES",	"actuadores");
    define("MODULO_SMARTMETER",	"smartmeter");
    define("MODULO_PROYECTOS", "proyectos");

    // Nombres de los módulos
    define("NOMBRE_MODULO_ADMINISTRACION", "Administración");
    define("NOMBRE_MODULO_MONITORIZACION", "Monitorización");
    define("NOMBRE_MODULO_PERSONAL", "Personal");
    define("NOMBRE_MODULO_RED", "Red");
    define("NOMBRE_MODULO_LOCALIZACIONES", "Localizaciones");
    define("NOMBRE_MODULO_SENSORES", "Sensores");
    define("NOMBRE_MODULO_ACTUADORES", "Actuadores");
    define("NOMBRE_MODULO_SMARTMETER", "SmartMeter");
    define("NOMBRE_MODULO_PROYECTOS", "Proyectos");

    // Número máximo de elementos de módulos por usuario
    define("NUMERO_MAXIMO_LOCALIZACIONES_USUARIO", 250);
    define("NUMERO_MAXIMO_SENSORES_USUARIO", 250);
    define("NUMERO_MAXIMO_GRUPOS_SENSORES_USUARIO", 50);
    define("NUMERO_MAXIMO_ACTUADORES_USUARIO", 250);
    define("NUMERO_MAXIMO_GRUPOS_ACTUADORES_USUARIO", 50);

    // Módulos disponibles
    define("MODULO_DISPONIBLE", "disponible");
    define("MODULO_NO_DISPONIBLE", "no_disponible");


    //
    // Secciones de los módulos
    //


    // Administración
    define("SECCION_ADMINISTRACION_REDES", "redes");
    define("SECCION_ADMINISTRACION_USUARIOS", "usuarios");
    define("SECCION_ADMINISTRACION_PREFERENCIAS", "preferencias");
    define("SECCION_ADMINISTRACION_SELECCION_RED", "seleccionRed");

    // Monitorización
    define("SECCION_MONITORIZACION_PROCESADO", "procesado");
    define("SECCION_MONITORIZACION_ALARMAS", "alarmas");
    define("SECCION_MONITORIZACION_ACCIONES_USUARIO", "accionesUsuario");

    // Personal
    define("SECCION_PERSONAL_PLANTILLAS_INFORMES", "plantillasInformes");
    define("SECCION_PERSONAL_INFORMES_AUTOMATICOS", "informesAutomaticos");
    define("SECCION_PERSONAL_WIDGETS", "widgets");

    // Red
    define("SECCION_RED_PRINCIPAL", "principal");
    define("SECCION_RED_ALARMAS", "alarmas");
    define("SECCION_RED_ACCIONES_USUARIO", "accionesUsuario");
    define("SECCION_RED_COMENTARIOS", "comentarios");
    define("SECCION_RED_TOPOLOGIA", "topologia");
    define("SECCION_RED_MAPA", "geoMapa");

    // Localizaciones
    define("SECCION_LOCALIZACIONES_PRINCIPAL", "principal");
    define("SECCION_LOCALIZACIONES_INSTALACIONES", "instalaciones");
    define("SECCION_LOCALIZACIONES_TOPOLOGIA", "topologia");
    define("SECCION_LOCALIZACIONES_RATIOS", "ratios");
    define("SECCION_LOCALIZACIONES_MAPA", "geoMapa");

    // Sensores
    define("SECCION_SENSORES_PRINCIPAL", "principal");
    define("SECCION_SENSORES_EVENTOS", "eventos");
    define("SECCION_SENSORES_INFORMACION", "informacion");
    define("SECCION_SENSORES_COMPARACION", "comparacion");
    define("SECCION_SENSORES_ESTADISTICA", "estadistica");
    define("SECCION_SENSORES_ANALISIS", "analisis");
    define("SECCION_SENSORES_MAPA", "geoMapa");

    // Actuadores
    define("SECCION_ACTUADORES_PRINCIPAL", "principal");
    define("SECCION_ACTUADORES_PROGRAMACIONES", "programaciones");
    define("SECCION_ACTUADORES_REGLAS", "reglas");
    define("SECCION_ACTUADORES_INFORMACION", "informacion");
    define("SECCION_ACTUADORES_MAPA", "geoMapa");

    // Smartmeter
    define("SECCION_SMARTMETER_CONSUMOS_COSTES", "consumosCostes");
    define("SECCION_SMARTMETER_AUTOCONSUMO", "autoconsumo");
    define("SECCION_SMARTMETER_POTENCIAS", "potencias");
    define("SECCION_SMARTMETER_ENERGIA_REACTIVA", "energiaReactiva");
    define("SECCION_SMARTMETER_COMPRA_ENERGIA", "compraEnergia");
    define("SECCION_SMARTMETER_CAUDALES", "caudales");
    define("SECCION_SMARTMETER_FACTURAS", "facturas");
    define("SECCION_SMARTMETER_INFORMES_PERSONALIZADOS", "informesPersonalizados");
    define("SECCION_SMARTMETER_TARIFAS", "tarifas");

    // Proyectos
    define("SECCION_PROYECTOS_PRINCIPAL", "principal");
    define("SECCION_PROYECTOS_LINEAS_BASE", "lineas_base");
    define("SECCION_PROYECTOS_INFORMACION", "informacion");


    //
    // Separadores
    //


    // Separadores de parámetros
    define("SEPARADOR_PARAMETROS_SUPERCOMPUESTOS", "^"); // Nivel 0
    define("SEPARADOR_PARAMETROS_COMPUESTOS", "&"); // Nivel 1
    define("SEPARADOR_PARAMETROS_SIMPLES", ","); // Nivel 2
    define("SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES", "|"); // Nivel 3
    define("SEPARADOR_PARAMETROS_VALORES", "#");
    define("SEPARADOR_VALORES_SENSOR", "#");
    define("SEPARADOR_VALOR_INCREMENTO_SENSOR", "&");
    define("SEPARADOR_VALORES_ACTUADOR", "#");
    define("SEPARADOR_FECHAS", "_");
    define("SEPARADOR_HORAS", "-");
    define("SEPARADOR_DIAS_SEMANA", "-");

    // Separadores de parámetros específicos
    define("SEPARADOR_OPERACIONES_CALIBRACION", ",");
    define("SEPARADOR_ELEMENTOS_OPERACIONES_CALIBRACION", ":");
    define("SEPARADOR_PARAMETROS_OPERACIONES_CALIBRACION", "&");
    define("SEPARADOR_TIPOS_REGISTRO_ESCRITURA_LECTURA_MODBUS", ":");
    define("SEPARADOR_DIRECCIONES_EMAIL", ";");
    define("SEPARADOR_CAMPO_PARAMETROS_EXTRA", ":");

    // Sustitutos de separadores
    // Nota: Si hay '$' en la cadena hay que utilizar comillas simples (https://techlandia.com/utilizar-signo-dolar-cadena-php-como_223786/)
    define("SUSTITUTO_SEPARADOR", "*");
    define("SUSTITUTO_SEPARADOR_EXTRA", '{$}');

    // Separador de elementos (nombre de tabla e id de elemento) de tablas de detalles (para la exportación de valores)
    define("SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES", "-id-");


    //
    // Herramientas
    //


    // Acciones iniciales
    define("ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS", "actualizacion_periodica_widgets");


    // Constantes de matemáticas

    // Funciones de correlación
    define("FUNCION_CORRELACION_AUTOMATICA", "automatica");
    define("FUNCION_CORRELACION_LINEAL", "lineal");
    define("FUNCION_CORRELACION_POLINOMIO_GRADO_2", "polinomio_grado_2");
    define("FUNCION_CORRELACION_LOGARITMICA", "logaritmica");
    define("FUNCION_CORRELACION_RAIZ_CUADRADA", "raiz_cuadrada");
    define("FUNCION_CORRELACION_MULTIVARIABLE_LINEAL", "multivariable_lineal");

    // Número de decimales para mostrar en los coeficientes de la función de correlacion
    define("NUMERO_DECIMALES_COEFICIENTES_FUNCION_CORRELACION", 8);


    //
    // Longitudes máximas de textos
    //


    // Longitudes máximas de campos de texto
    define("NUMERO_MAXIMO_CARACTERES_NOMBRE", 200);
    define("NUMERO_MAXIMO_CARACTERES_DESCRIPCION", 1000);
    define("NUMERO_MAXIMO_CARACTERES_OBSERVACIONES", 500);
    define("NUMERO_MAXIMO_CARACTERES_PERIODOS_DIA_SEMANA_HORARIO_SEMANAL", 100);
    define("NUMERO_MAXIMO_CARACTERES_PERIODOS_FECHAS", 200);
    define("NUMERO_MAXIMO_CARACTERES_NOTAS", 3000);
    define("NUMERO_MAXIMO_CARACTERES_TEXTO", 3000);
    define("NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL", 500);
    define("NUMERO_MAXIMO_CARACTERES_DIRECCION_EMAIL", 200);
    define("NUMERO_MAXIMO_CARACTERES_CALIBRACION", 100);
    define("NUMERO_MAXIMO_CARACTERES_PARAMETROS_EVENTO", 200);
    define("NUMERO_MAXIMO_CARACTERES_CUPS", 50);
    define("NUMERO_MAXIMO_CARACTERES_PREFIJOS_FICHEROS", 100);
    define("NUMERO_MAXIMO_CARACTERES_NOMBRE_MEDIDA", 50);
    define("NUMERO_MAXIMO_CARACTERES_UNIDAD_MEDIDA", 50);
    define("NUMERO_MAXIMO_CARACTERES_PREFIJO_SUFIJO_TITULO_CABECERA_PESTANYA_WIDGETS", 50);
    define("NUMERO_MAXIMO_CARACTERES_TITULO_FILA_WIDGETS", 1000);
    define("NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE", 200);
    define("NUMERO_MAXIMO_CARACTERES_CONTENIDO_MENSAJE", 1000);
    define("NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_SENSOR_PROCESADO", 3000);
    define("NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_LINEA_BASE_FUNCIONAL", 500);
    define("NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO", 1000);
    define("NUMERO_MAXIMO_CARACTERES_TITULO", 200);
    define("NUMERO_MAXIMO_CARACTERES_TEXTO_ANOTACION_EQUIPO_INSTALACION", 1000);


    //
    // Informes (y gráficas)
    //


    // Intervalos de tiempo real (con líneas o con líneas y puntos)
    define("INTERVALO_VALORES_TIEMPO_REAL_LINEAS", "tiempo_real_lineas");
    define("INTERVALO_VALORES_TIEMPO_REAL_PUNTOS", "tiempo_real_puntos");

    // Agrupaciones de valoresin
    define("AGRUPACION_VALORES_HORA", "hora");
    define("AGRUPACION_VALORES_DIA_SEMANA", "dia_semana");
    define("AGRUPACION_VALORES_FECHA", "fecha");

    // Tipos de líneas de valores
    define("TIPO_LINEAS_VALORES_ESTANDAR", "estandar");
    define("TIPO_LINEAS_VALORES_CUADRADAS", "cuadradas");


    //
    // Módulos
    //


    // Tipos de nodos
	define("TIPO_NODO_RED","Red");
    define("TIPO_NODO_DISPOSITIVO", "Dispositivo");
	define("TIPO_NODO_AXON", "Axon");
	define("TIPO_NODO_SENSOR", "Sensor");
    define("TIPO_NODO_GRUPO_SENSORES", "GrupoSensores");
	define("TIPO_NODO_ACTUADOR", "Actuador");
	define("TIPO_NODO_GRUPO_ACTUADORES", "GrupoActuadores");

    // Intervalos de valores
    define("INTERVALO_VALORES_NINGUNO", "ninguno");
    define("INTERVALO_VALORES_TODOS", "todos");
    define("INTERVALO_VALORES_TIEMPO_REAL", "tiempo_real");
    define("INTERVALO_VALORES_CUARTOHORA", "cuartohora");
    define("INTERVALO_VALORES_HORA", "hora");
    define("INTERVALO_VALORES_DIA", "dia");
    define("INTERVALO_VALORES_SEMANA", "semana");
    define("INTERVALO_VALORES_MES", "mes");

    // Opciones extra para añadir a listas de usuarios
    define("OPCIONES_EXTRA_LISTA_USUARIOS_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_USUARIOS_ACTUAL", "ACTUAL");

    // Opciones extra para añadir a listas de tipos
    define("OPCIONES_EXTRA_LISTA_TIPOS_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_TIPOS_TODOS", "TODOS");
    define("OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO", "TODOS_NINGUNO");

    // Opciones extra para añadir a listas de intervalos de valores
    define("OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_TODOS", "TODOS");

    // Opciones extra para añadir a listas de mediciones
    define("OPCIONES_EXTRA_LISTA_MEDICIONES_TODAS", "TODAS");
    define("OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL", "RED_ACTUAL");
    define("OPCIONES_EXTRA_LISTA_MEDICIONES_CURVA_COSTE_RED_ACTUAL", "CURVA_COSTE_RED_ACTUAL");
    define("OPCIONES_EXTRA_LISTA_MEDICIONES_FACTURAS_RED_ACTUAL", "FACTURAS_RED_ACTUAL");

    // Número de columnas de tablas de nodos
    define("NUMERO_COLUMNAS_TABLA_NODO_RED", 5);
    define("NUMERO_COLUMNAS_TABLA_NODO_SERVIDOR", 3);
    define("NUMERO_COLUMNAS_TABLA_NODO_DISPOSITIVO", 6);
    define("NUMERO_COLUMNAS_TABLA_NODO_AXON", 5);

    // Número de columnas de tablas de nodos (con y sin localización)
    define("NUMERO_COLUMNAS_TABLA_NODO_SENSOR_CON_LOCALIZACION", 6);
    define("NUMERO_COLUMNAS_TABLA_NODO_SENSOR_SIN_LOCALIZACION", 5);
    define("NUMERO_COLUMNAS_TABLA_NODO_GRUPO_SENSORES_CON_LOCALIZACION", 3);
    define("NUMERO_COLUMNAS_TABLA_NODO_GRUPO_SENSORES_SIN_LOCALIZACION", 2);
    define("NUMERO_COLUMNAS_TABLA_NODO_ACTUADOR_CON_LOCALIZACION", 7);
    define("NUMERO_COLUMNAS_TABLA_NODO_ACTUADOR_SIN_LOCALIZACION", 6);
    define("NUMERO_COLUMNAS_TABLA_NODO_GRUPO_ACTUADORES_CON_LOCALIZACION", 5);
    define("NUMERO_COLUMNAS_TABLA_NODO_GRUPO_ACTUADORES_SIN_LOCALIZACION", 4);

    // Anchuras de columnas de tablas de nodos
    define("ANCHURAS_COLUMNAS_TABLA_NODO_RED", serialize(array(25, 35, 10, 10, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_NODO_SENSOR_CON_LOCALIZACION", serialize(array(30, 15, 5, 10, 15, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_NODO_SENSOR_SIN_LOCALIZACION", serialize(array(30, 10, 10, 20, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_NODO_ACTUADOR_CON_LOCALIZACION", serialize(array(25, 10, 5, 15, 15, 10, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_NODO_ACTUADOR_SIN_LOCALIZACION", serialize(array(25, 10, 10, 20, 15, 20)));

    // Números de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_PERIODOS", 4);
    define("NUMERO_COLUMNAS_TABLA_RANGOS_DIAS", 2);
    define("NUMERO_COLUMNAS_TABLA_ALARMAS_SIN_RED", 4);
    define("NUMERO_COLUMNAS_TABLA_ALARMAS_CON_RED", 5);
    define("NUMERO_COLUMNAS_TABLA_ACCIONES_USUARIO_SIN_RED", 4);
    define("NUMERO_COLUMNAS_TABLA_ACCIONES_USUARIO_CON_RED", 5);
    define("NUMERO_COLUMNAS_TABLA_COMENTARIOS_SIN_USUARIO", 4);
    define("NUMERO_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO", 5);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_ALARMAS_SIN_RED", serialize(array(15, 40, 35, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_ALARMAS_CON_RED", serialize(array(15, 15, 35, 25, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_ACCIONES_USUARIO_SIN_RED", serialize(array(15, 20, 20, 45)));
    define("ANCHURAS_COLUMNAS_TABLA_ACCIONES_USUARIO_CON_RED", serialize(array(15, 15, 20, 20, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_SIN_USUARIO", serialize(array(15, 10, 25, 50)));
    define("ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO", serialize(array(15, 15, 10, 20, 40)));

    // Periodos para los intervalos de fechas (para la fecha de inicio con respecto a la fecha final)
    define("PERIODO_DIA", "");
    define("PERIODO_SEMANA", "week");
    define("PERIODO_MES", "month");
    define("PERIODO_ANYO", "year");
    define("PERIODO_DIA_INICIO_HOY", "DIA_INICIO_HOY");
    define("PERIODO_DIA_INICIO_MANYANA", "DIA_INICIO_MANYANA");
    define("PERIODO_DIA_INICIO_SEMANA", "DIA_INICIO_SEMANA");
    define("PERIODO_DIA_INICIO_MES", "DIA_INICIO_MES");
    define("PERIODO_DIA_INICIO_MANYANA_DURACION_SEMANA", "DIA_INICIO_MANYANA_DURACION_SEMANA");

    // Tipos de mapa
    define("TIPO_MAPA_INTERNET", "INTERNET");
    define("TIPO_MAPA_LOCAL", "LOCAL");

    // Constantes de mapa
    define("ZOOM_MAPA_DEFECTO", 0);

    // Orígenes de mapa
    define("ORIGEN_MAPA_RED", "RED");
    define("ORIGEN_MAPA_LOCALIZACION", "LOCALIZACION");
    define("ORIGEN_MAPA_INSTALACION", "INSTALACION");
    define("ORIGEN_MAPA_POSICION", "POSICION");
    define("ORIGEN_MAPA_SECCION", "SECCION");

    // Orígenes de mapa (auxiliares)
    define("ORIGEN_MAPA_RED_LOCALIZACION", "RED_LOCALIZACION");

    // Tipos de elementos de mapa
    define("TIPO_ELEMENTO_MAPA_DISPOSITIVO", "DISPOSITIVO");
    define("TIPO_ELEMENTO_MAPA_LOCALIZACION", "LOCALIZACION");
    define("TIPO_ELEMENTO_MAPA_INSTALACION", "INSTALACION");
    define("TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION", "EQUIPO_INSTALACION");
    define("TIPO_ELEMENTO_MAPA_SENSOR", "SENSOR");
    define("TIPO_ELEMENTO_MAPA_ACTUADOR", "ACTUADOR");

    // Ids de mapas
    define("ID_MAPA_MAPA_SECCION", "MAPA_SECCION");
    define("ID_MAPA_MAPA_INSTALACIONES", "MAPA_INSTALACIONES");
    define("ID_MAPA_IMAGEN_INSTALACION", "IMAGEN_INSTALACION");

    // Número máximo de capas de grupos/localizaciones en el mapa
    define("NUMERO_MAXIMO_CAPAS_MAPA_GRUPOS_LOCALIZACIONES", 10);

    // Operaciones de administracion
    define("OPERACION_ADICION", "ADICION");
    define("OPERACION_MODIFICACION", "MODIFICACION");
    define("OPERACION_BORRADO", "BORRADO");

    // Colores de mapa de calor
    // (http://www.perbang.dk/rgbgradient/)
    define("COLORES_AZUL_ROJO", "AZUL_ROJO");
    define("COLORES_VERDE_ROJO", "VERDE_ROJO");
    define("COLORES_NEGRO_AMARILLO", "NEGRO_AMARILLO");
    define("COLORES_ROJO_VERDE", "ROJO_VERDE");
    define("COLORES_ROJO_AZUL", "ROJO_AZUL");
    define("COLORES_AMARILLO_AZUL", "AMARILLO_AZUL");
    define("COLORES_BLANCO_NEGRO", "BLANCO_NEGRO");

    // Tipos de mapa de calor
    define("TIPO_MAPA_CALOR_NINGUNO", "NINGUNO");
    define("TIPO_MAPA_CALOR_DIARIO", "DIARIO");
    define("TIPO_MAPA_CALOR_SEMANAL", "SEMANAL");

    // Tipos de selección (lista seleccionable)
    define("TIPO_SELECCION_ESTANDAR", "ESTANDAR");
    define("TIPO_SELECCION_CHOSEN", "CHOSEN");

    // Opciones extra para añadir a las listas de nodos
    define("OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS", "SIN_OPCIONES_TODOS_NODOS");
    define("OPCIONES_EXTRA_LISTA_NODOS_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_NODOS_TODOS", "TODOS");
    define("OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO", "TODOS_NINGUNO");

    // Opciones para crear documentos PDF
    define("MODO_PDF_CORE_FONTS", "c");
    define("FORMATO_PDF_A4", "A4");
    define("TAMANYO_FUENTE_DEFECTO_PDF", "0");
    define("FUENTE_DEFECTO_PDF", "");
    define("FORMATO_PDF_A4", "A4");
    define("MARGEN_IZQUIERDA_PDF", 10);
    define("MARGEN_DERECHA_PDF", 10);
    define("MARGEN_ARRIBA_PDF", 30);
    define("MARGEN_ABAJO_PDF", 30);
    define("MARGEN_CABECERA_PDF", 10);
    define("MARGEN_PIE_PDF", 10);

    // Altura de logo de informes PDF
    define("ALTURA_IMAGEN_LOGO_CABECERA_PDF", "53px");

    // Tipos de codificaciones
    define("CODIFICACION_NINGUNA", "NINGUNA");
    define("CODIFICACION_BASE_64", "BASE_64");

    // Valor cero mínimo
    define("VALOR_CERO_MINIMO", 0.00001);

    // Identificadores 'especiales'
    define("ID_NINGUNO", -1);
    define("ID_INVALIDO", -2);
    define("ID_TODOS", -3);
    define("ID_DESACTIVADO", -4);

    // Días de semana
    define("DIA_SEMANA_TODOS", -1);

    // Nombre del logo de Energy Minus
    define("NOMBRE_LOGO_ENERGY_MINUS", "energy_minus");

    // Número máximo de elementos en tablas de históricos
    define("NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS", 1000);

    // Usuarios "internos"
    define("USUARIO_INTERNO_SERVICIOS", "SERVICIOS");
    define("USUARIO_INTERNO_API_HTTP", "API_HTTP");

    // Tipos de informes
    define("TIPO_INFORME_WEB_EMIOS", "WEB_EMIOS");
    define("TIPO_INFORME_FICHERO", "FICHERO");

    // Estados de ejecución (acciones, tareas, etc.)
    define("ESTADO_EJECUCION_EN_EJECUCION", -1);
    define("ESTADO_EJECUCION_ERROR", 0);
    define("ESTADO_EJECUCION_OK", 1);

    // Tipos de operación de administración
    define("TIPO_OPERACION_ADMINISTRACION_MODIFICACION", "MODIFICACION");
    define("TIPO_OPERACION_ADMINISTRACION_DUPLICADO", "DUPLICADO");

    // Tamaños de controles
    define("TAMANYO_CONTROL_PEQUENYO", "PEQUENYO");
    define("TAMANYO_CONTROL_MEDIANO", "MEDIANO");
    define("TAMANYO_CONTROL_GRANDE", "GRANDE");
    define("TAMANYO_CONTROL_MUY_GRANDE", "MUY_GRANDE");

    // Colores de nodos en topología de árbol
    define("TOPOLOGIA_ARBOL_COLOR_NODO_ROJO", "ROJO");
    define("TOPOLOGIA_ARBOL_COLOR_NODO_NARANJA", "NARANJA");
    define("TOPOLOGIA_ARBOL_COLOR_NODO_VERDE", "VERDE");
    define("TOPOLOGIA_ARBOL_COLOR_NODO_AZUL", "AZUL");

    // Errores en evaluación de función con variables
    define("ERROR_FUNCION_VARIABLES_ERROR_DESCONOCIDO", "ERROR_DESCONOCIDO");
    define("ERROR_FUNCION_VARIABLES_FUNCION_VACIA", "FUNCION_VACIA");
    define("ERROR_FUNCION_VARIABLES_VARIABLE_NO_DEFINIDA", "VARIABLE_NO_DEFINIDA");
    define("ERROR_FUNCION_VARIABLES_OPERADORES_INCORRECTOS", "OPERADORES_INCORRECTOS");
    define("ERROR_FUNCION_VARIABLES_FORMATO_INCORRECTO", "FORMATO_INCORRECTO");
    define("ERROR_FUNCION_VARIABLES_NUMERO_VALORES_INCORRECTO", "NUMERO_VALORES_INCORRECTO");
    define("ERROR_FUNCION_VARIABLES_DIVISION_POR_CERO", "DIVISION_POR_CERO");
    define("ERROR_FUNCION_VARIABLES_PARENTESIS", "NUMERO_PARENTESIS");
    define("ERROR_FUNCION_VARIABLES_PRESENCIA_COMAS", "PRESENCIA_COMAS");

    // Colores de las líneas de las gráficas de jqplot
    define("COLOR_LINEA_GRAFICA_ROJO", "rgba(200, 100, 100, 0.25)");
    define("COLOR_LINEA_GRAFICA_VERDE_CLARO", "rgba(100, 255, 100, 0.5)");
    define("COLOR_LINEA_GRAFICA_VERDE_OSCURO", "rgba(100, 200, 100, 0.5)");
    define("COLOR_LINEA_GRAFICA_NARANJA", "rgba(230, 115, 0, 0.5)");
    define("COLOR_LINEA_GRAFICA_AZUL", "rgba(150, 150, 255, 0.5)");
    define("COLOR_LINEA_GRAFICA_AMARILLO", "rgba(255, 200, 50, 0.5)");

    // Número máximo de elementos en la lista de parámetros de informes
    define("NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES", 25);

    // Periodos de tiempo
    define("PERIODO_TIEMPO_FECHA_INICIO", "fecha_inicio");
    define("PERIODO_TIEMPO_HORA", "hora");
    define("PERIODO_TIEMPO_DIA", "dia");
    define("PERIODO_TIEMPO_SEMANA", "semana");
    define("PERIODO_TIEMPO_MES", "mes");
    define("PERIODO_TIEMPO_ANYO", "anyo");

    // Tipos de selección de periodo de tiempo
    define("TIPO_SELECCION_PERIODO_TIEMPO_AUTOMATICO", "automatico");
    define("TIPO_SELECCION_PERIODO_TIEMPO_CONFIGURABLE", "configurable");

    // Tipos de descripción (para las acciones de usuario)
    define("TIPO_DESCRIPCION_HTML", "HTML");
    define("TIPO_DESCRIPCION_TEXTO", "TEXTO");

    // Tipos de comentarios
    define("TIPO_COMENTARIO_ANOTACION_SENSOR", "ANOTACION_SENSOR");
    define("TIPO_COMENTARIO_INTERVENCION_SENSOR", "INTERVENCION_SENSOR");
    define("TIPO_COMENTARIO_ANOTACION_ACTUADOR", "ANOTACION_ACTUADOR");
    define("TIPO_COMENTARIO_INTERVENCION_ACTUADOR", "INTERVENCION_ACTUADOR");
    define("TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES", "ANOTACION_GRUPO_ACTUADORES");
    define("TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES", "INTERVENCION_GRUPO_ACTUADORES");

    // Tipos de visibilidad
    define("VISIBILIDAD_TODAS", "TODAS");
    define("VISIBILIDAD_PUBLICA", "PUBLICA");
    define("VISIBILIDAD_PRIVADA", "PRIVADA");

    // Opciones extra para añadir a la lista de visibilidades de comentarios
    define("OPCIONES_EXTRA_LISTA_VISIBILIDADES_COMENTARIOS_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_VISIBILIDADES_COMENTARIOS_TODAS", "TODAS");

    // Constantes de limitaciones en tablas de comentarios
    define("NUMERO_MAXIMO_CARACTERES_LINEA_TOOLTIP_COMENTARIOS_GRAFICA", 50);

    // Orígenes de comentarios
    define("ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS", "tabla_elemento_plantilla_informe_comentarios");
    define("ORIGEN_COMENTARIOS_TABLA_COMENTARIOS_RED", "tabla_comentarios_red");
    define("ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES", "herramientas_sensores");
    define("ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES", "detalles_tabla_sensores");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION", "grafica_informe_sensores_informacion");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION", "tabla_informe_sensores_informacion");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME", "grafica_informe_sensores_informacion_elemento_plantilla_informe");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME", "tabla_informe_sensores_informacion_elemento_plantilla_informe");
    define("ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES", "herramientas_actuadores");
    define("ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES", "detalles_tabla_actuadores");
    define("ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES", "detalles_tabla_grupo_actuadores");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR", "grafica_informe_actuadores_informacion_actuador");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR", "tabla_informe_actuadores_informacion_actuador");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES", "grafica_informe_actuadores_informacion_grupo_actuadores");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES", "tabla_informe_actuadores_informacion_grupo_actuadores");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR", "grafica_informe_actuadores_informacion_elemento_plantilla_informe_actuador");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR", "tabla_informe_actuadores_informacion_elemento_plantilla_informe_actuador");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES", "grafica_informe_actuadores_informacion_elemento_plantilla_informe_grupo_actuadores");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES", "tabla_informe_actuadores_informacion_elemento_plantilla_informe_grupo_actuadores");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES", "grafica_informe_smartmeter_consumos_costes_generales");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES", "tabla_informe_smartmeter_consumos_costes_generales");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME", "grafica_informe_smartmeter_consumos_costes_generales_elemento_plantilla_informe");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME", "tabla_informe_smartmeter_consumos_costes_generales_elemento_plantilla_informe");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE", "grafica_informe_proyectos_simulador_linea_base");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE", "tabla_informe_proyectos_simulador_linea_base");
    define("ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME", "grafica_informe_proyectos_simulador_linea_base_elemento_plantilla_informe");
    define("ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME", "tabla_informe_proyectos_simulador_linea_base_elemento_plantilla_informe");

    // Ventanas de administración de comentarios
    define("TIPO_VENTANA_ANYADIR_COMENTARIO", "VENTANA_ANYADIR_COMENTARIO");
    define("TIPO_VENTANA_ANYADIR_COMENTARIOS", "VENTANA_ANYADIR_COMENTARIOS");

    // Número máximo de adición de comentarios (al mismo tiempo)
    define("NUMERO_MAXIMO_ADICION_COMENTARIOS", 50);

    // Orígenes de imágenes
    define("ORIGEN_IMAGEN_RED_LOGO", "RED_LOGO");
    define("ORIGEN_IMAGEN_RED_LOGO_PDF", "RED_LOGO_PDF");
    define("ORIGEN_IMAGEN_RED_MAPA", "RED_MAPA");
    define("ORIGEN_IMAGEN_PREFERENCIAS_LOGO", "PREFERENCIAS_LOGO");
    define("ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF", "PREFERENCIAS_LOGO_PDF");
    define("ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF", "PLANTILLA_INFORME_LOGO_PDF");
    define("ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN", "ELEMENTO_PLANTILLA_INFORME_IMAGEN");
    define("ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN", "INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN");
    define("ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO", "PESTANYA_WIDGETS_FONDO");
    define("ORIGEN_IMAGEN_WIDGET_IMAGEN", "WIDGET_IMAGEN");
    define("ORIGEN_IMAGEN_LOCALIZACION_MAPA", "LOCALIZACION_MAPA");
    define("ORIGEN_IMAGEN_INSTALACION_IMAGEN", "INSTALACION_IMAGEN");
    define("ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO", "ANOTACION_EQUIPO_INSTALACION_FOTO");

    // Dimensiones de imágenes
    define("ANCHURA_MAXIMA_IMAGEN_LOGO", 500);
    define("ALTURA_MAXIMA_IMAGEN_LOGO", 53);
    define("ANCHURA_MAXIMA_IMAGEN_MAPA", 3840);
    define("ALTURA_MAXIMA_IMAGEN_MAPA", 2160);
    define("ANCHURA_MAXIMA_IMAGEN_PLANTILLA_INFORME", 3840);
    define("ALTURA_MAXIMA_IMAGEN_PLANTILLA_INFORME", 2160);
    define("ANCHURA_MAXIMA_IMAGEN_FONDO", 3840);
    define("ALTURA_MAXIMA_IMAGEN_FONDO", 2160);
    define("ANCHURA_MAXIMA_IMAGEN_WIDGET", 1920);
    define("ALTURA_MAXIMA_IMAGEN_WIDGET", 1080);
    define("ANCHURA_MAXIMA_IMAGEN_INSTALACION", 3840);
    define("ALTURA_MAXIMA_IMAGEN_INSTALACION", 2160);
    define("ANCHURA_MAXIMA_IMAGEN_FOTO", 3840);
    define("ALTURA_MAXIMA_IMAGEN_FOTO", 2160);

    // Estilos de fuente
    define("ESTILO_FUENTE_NORMAL", "NORMAL");
    define("ESTILO_FUENTE_NEGRITA", "NEGRITA");

    // Colores
    define("COLOR_BLANCO", "#FFFFFF");
    define("COLOR_NEGRO", "#000000");



    //
    // Módulos (widgets)
    //


    // Tipos de widgets
    define("TIPO_WIDGET_IMAGEN", "IMAGEN");
    define("TIPO_WIDGET_VALOR_RATIO", "VALOR_RATIO");
    define("TIPO_WIDGET_VALOR_DIGITAL_SENSOR", "VALOR_DIGITAL_SENSOR");
    define("TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR", "VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR");
    define("TIPO_WIDGET_VALOR_ANALOGICO_SENSOR", "VALOR_ANALOGICO_SENSOR");
    define("TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR", "VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR");
    define("TIPO_WIDGET_GRAFICA_VALORES_SENSOR", "GRAFICA_VALORES_SENSOR");
    define("TIPO_WIDGET_MAPA_CALOR_SENSOR", "MAPA_CALOR_SENSOR");
    define("TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR", "GRAFICA_COMPARACION_PERIODOS_SENSOR");
    define("TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR", "EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR");
    define("TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES", "GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES");
    define("TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES", "GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES");
    define("TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES", "GRAFICA_VALORES_GENERALES_SENSORES");
    define("TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES", "VALOR_AGREGADO_VALORES_GENERALES_SENSORES");
    define("TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES", "GRAFICA_INCREMENTOS_TOTALES_SENSORES");
    define("TIPO_WIDGET_INFORMACION_ACTUADOR", "INFORMACION_ACTUADOR");
    define("TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES", "INFORMACION_GRUPO_ACTUADORES");
    define("TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR", "GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR");
    define("TIPO_WIDGET_COSTE_FACTURA_SENSOR", "COSTE_FACTURA_SENSOR");
    define("TIPO_WIDGET_SIMULADOR_LINEA_BASE", "SIMULADOR_LINEA_BASE");
    define("TIPO_WIDGET_INFORMACION_PROYECTO", "INFORMACION_PROYECTO");

    // Tipos de gráficas en los widgets
    define("TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_BARRAS_VALORES", "BARRAS_VALORES");
    define("TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_TARTA_VALORES", "TARTA_VALORES");

    // Número máximo de columnas de las filas de widgets
    define("NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS", 5);

    // Colores de iconos de widgets
    define("COLOR_ICONO_WIDGET_BLANCO", "BLANCO");
    define("COLOR_ICONO_WIDGET_NEGRO", "NEGRO");

    // Número de columnas de filas de widgets por defecto
    define("NUMERO_COLUMNAS_FILAS_WIDGETS_DEFECTO", 3);

    // Tipos de valores para los límites de colores
    define("TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_NINGUNO", "NINGUNO");
    define("TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_ABSOLUTO", "ABSOLUTO");
    define("TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_PORCENTAJE", "PORCENTAJE");

    // Opciones de valor digital para los widgets de tipo valor analógico de sensor
    define("VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_NINGUNO", "NINGUNO");
    define("VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_SELECCIONADO", "SELECCIONADO");

    // Tipos de gráficos de widgets de valor analógico
    define("TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ", "RELOJ");
    define("TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR", "CIRCULAR");

    // Posiciones de pestañas
    define("POSICION_PESTANYA_NINGUNA", 0);
    define("POSICION_PESTANYA_ULTIMA", 999);


    //
    // Módulos (informes fichero y automáticos)
    //


    // Periodicidad de informes automáticos
    define("PERIODICIDAD_INFORME_AUTOMATICO_DIARIA", "DIARIA");
    define("PERIODICIDAD_INFORME_AUTOMATICO_SEMANAL", "SEMANAL");
    define("PERIODICIDAD_INFORME_AUTOMATICO_MENSUAL", "MENSUAL");
    define("PERIODICIDAD_INFORME_AUTOMATICO_PERSONALIZADA", "PERSONALIZADA");

    // Tipos de scripts a ejecutar por usuarios 'internos' (de Servidor EMIOS)
    define("TIPO_SCRIPT_INTERNO_INFORME_FICHERO", "informe_fichero");


    //
    // Módulo Administración
    //


    // Número mínimo de caracteres de identificador y contraseña de usuario
    define("NUMERO_MINIMO_CARACTERES_ID_USUARIO", 6);
    define("NUMERO_MINIMO_CARACTERES_CONTRASENYA_USUARIO", 8);

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_CLIENTES", 1);
    define("NUMERO_COLUMNAS_TABLA_PREFERENCIAS", 5);
    define("NUMERO_COLUMNAS_TABLA_LICENCIAS", 3);
    define("NUMERO_COLUMNAS_TABLA_USUARIOS", 3);
    define("NUMERO_COLUMNAS_TABLA_INFORMES_AUTOMATICOS", 4);

    // Anchuras de columnas
    define("ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_RED", serialize(array(35, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_PREFERENCIAS", serialize(array(25, 30, 15, 15, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_INFORMES_AUTOMATICOS", serialize(array(40, 25, 20, 15)));

    // Números de columnas de filtros
    define("NUMERO_COLUMNAS_FILTRO_ACTUADORES_TABLA_TAMANYO_LETRA_GRANDE", 4);
    define("NUMERO_COLUMNAS_FILTRO_ACTUADORES_MAPA_TAMANYO_LETRA_GRANDE", 4);

    // Anchuras de columnas de filtros
    define("ANCHURAS_COLUMNAS_FILTRO_REDES_TABLA", serialize(array(18, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_USUARIOS_TABLA", serialize(array(18, -1, -1)));

    // Opciones extra para añadir a listas de perfiles de usuario
    define("OPCIONES_EXTRA_LISTA_PERFILES_USUARIO_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_PERFILES_USUARIO_TODOS", "TODOS");


    //
    // Módulo Monitorización
    //


    // Tipos de operaciones de datos de sensor
    define("TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO", "TAREA_PROCESADO");
    define("TIPO_OPERACION_DATOS_SENSOR_FUNCION_PROCESADO", "FUNCION_PROCESADO");
    define("TIPO_OPERACION_DATOS_SENSOR_FICHERO_CSV", "FICHERO_CSV");

    // Tipos de sensor en las operaciones de datos de sensor
    define("TIPO_SENSOR_OPERACION_DATOS_SENSOR_PRINCIPAL", "PRINCIPAL");
    define("TIPO_SENSOR_OPERACION_DATOS_SENSOR_HIJO", "HIJO");
    define("TIPO_SENSOR_OPERACION_DATOS_SENSOR_ASOCIADO", "ASOCIADO");
    define("TIPO_SENSOR_OPERACION_DATOS_SENSORES_RED", "RED");

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_HISTORICO_PROCESADO", 7);
    define("NUMERO_COLUMNAS_TABLA_TAREAS_PROCESADO", 5);
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_OPERACIONES_DATOS_SENSORES", 3);
    define("NUMERO_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_CON_RED", 6);
    define("NUMERO_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_SIN_RED", 5);
    define("NUMERO_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_CON_RED", 6);
    define("NUMERO_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_SIN_RED", 5);
    define("NUMERO_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_CON_RED", 5);
    define("NUMERO_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_SIN_RED", 4);
    define("NUMERO_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_CON_RED", 4);
    define("NUMERO_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_SIN_RED", 3);
    define("NUMERO_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_CON_RED", 4);
    define("NUMERO_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_SIN_RED", 3);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_HISTORICO_PROCESADO", serialize(array(20, 15, 10, 15, 15, 15, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_CON_RED", serialize(array(10, 25, 15, 15, 20, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_SIN_RED", serialize(array(15, 30, 20, 20, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_CON_RED", serialize(array(15, 25, 15, 15, 10, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_SIN_RED", serialize(array(15, 35, 20, 10, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_CON_RED", serialize(array(25, 20, 25, 15, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_SIN_RED", serialize(array(40, 30, 15, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_CON_RED", serialize(array(25, 25, 30, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_SIN_RED", serialize(array(45, 35, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_CON_RED", serialize(array(25, 25, 30, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_SIN_RED", serialize(array(45, 35, 20)));

    // Número de columnas de parámetros de informes
    define("NUMEROS_COLUMNAS_PARAMETROS_TIEMPOS_EJECUCION_PROCESADO", "2,4");

    // Anchuras de columnas de parámetros de informes
    // Nota: Puede no ser hasta el 100% ya que influye el padding, margins, etc de los controles ...
    define("ANCHURAS_COLUMNAS_PARAMETROS_EJECUCION_MANUAL_PROCESADO", serialize(array(-1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_TIEMPOS_EJECUCION_PROCESADO", serialize(array(-1, -1, -1, -1, -1, -1, -1)));

    // Número de columnas de filtros
    define("NUMERO_COLUMNAS_FILTRO_HISTORICO_PROCESADO", 4);
    define("NUMEROS_COLUMNAS_FILTRO_HISTORICO_IMPORTACIONES_VALORES_SENSORES", "2,4");
    define("NUMERO_COLUMNAS_FILTRO_ACCIONES_USUARIO_MONITORIZACION", 3);

    // Anchuras de columnas de filtros
    define("ANCHURAS_COLUMNAS_FILTRO_HISTORICO_PROCESADO", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_HISTORICO_IMPORTACIONES_VALORES_SENSORES", serialize(array(18, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_ALARMAS_MONITORIZACION", serialize(array(18, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_ACCIONES_USUARIO_MONITORIZACION", serialize(array(18, -1, -1, -1, -1)));

    // Periodos por defecto para las fechas iniciales de los informes y filtros
    define("PERIODO_DEFECTO_MONITORIZACION_TIEMPO_EJECUCION_PROCESADO", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_HISTORICO_IMPORTACIONES_VALORES_SENSORES", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_MONITORIZACION_HISTORICO_PROCESADO", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_MONITORIZACION_ALARMAS", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_MONITORIZACION_ACCIONES_USUARIO", PERIODO_DIA_INICIO_HOY);

    // Tipos de ejecuciones de procesado
    define("TIPO_EJECUCION_PROCESADO_TODOS", "TODOS");
    define("TIPO_EJECUCION_PROCESADO_NORMAL", "NORMAL");
    define("TIPO_EJECUCION_PROCESADO_RECALCULOS", "RECALCULOS");

    // Tipos de procesado
    define("TIPO_PROCESADO_CLASE_SENSOR", "CLASE_SENSOR");
    define("TIPO_PROCESADO_TIPO_SENSOR", "TIPO_SENSOR");

    // Causas de ejecución de procesado
    define("CAUSA_EJECUCION_PROCESADO_AUTOMATICA", "AUTOMATICA");
    define("CAUSA_EJECUCION_PROCESADO_MANUAL", "MANUAL");

    // Tipos de informes
    define("TIPO_INFORME_MONITORIZACION_TIEMPOS_EJECUCION_PROCESADO", "MONITORIZACION_TIEMPOS_EJECUCION_PROCESADO");

    // Opciones extra para añadir a listas de tipos de ejecuciones de procesado
    define("OPCIONES_EXTRA_LISTA_TIPOS_EJECUCIONES_PROCESADO_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_TIPOS_EJECUCIONES_PROCESADO_TODAS", "TODAS");

    // Número máximo días calculo valores de sensores de procesado en ejecución sin recálculos
    define("NUMERO_MAXIMO_DIAS_CALCULO_VALORES_SENSORES_PROCESADO_EJECUCION_SIN_RECALCULOS", 7);

    // Tipos de resultado de ejecución de importaciones de valores de sensores
    // (para el filtro de histórico de importaciones de valores de sensores)
    define("RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_TODOS", "TODOS");
    define("RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK", "OK");
    define("RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK_SIN_VALORES_ERRONEOS", "OK_SIN_VALORES_ERRONEOS");
    define("RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK_CON_VALORES_ERRONEOS", "OK_CON_VALORES_ERRONEOS");
    define("RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_ERROR", "ERROR");


    //
    // Módulo Personal
    //


    // Tipos de plantillas de informes
    define("TIPO_PLANTILLA_INFORME_FIJO", "FIJO");
    define("TIPO_PLANTILLA_INFORME_CONFIGURABLE", "CONFIGURABLE");

    // Tipos de selección de horario semanal y fechas
    define("TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_FIJO", "FIJO");
    define("TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE", "CONFIGURABLE");

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_PLANTILLAS_INFORMES_SIN_USUARIO", 2);
    define("NUMERO_COLUMNAS_TABLA_PLANTILLAS_INFORMES_CON_USUARIO", 3);
    define("NUMERO_COLUMNAS_TABLA_PARAMETROS_PLANTILLAS_INFORMES", 3);
    define("NUMERO_COLUMNAS_TABLA_ELEMENTOS_PLANTILLAS_INFORMES", 3);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_PLANTILLAS_INFORMES_SIN_USUARIO", serialize(array(60, 40)));
    define("ANCHURAS_COLUMNAS_TABLA_PLANTILLAS_INFORMES_CON_USUARIO", serialize(array(50, 20, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_PARAMETROS_PLANTILLAS_INFORMES", serialize(array(40, 30, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_ELEMENTOS_PLANTILLAS_INFORMES", serialize(array(20, 40, 40)));

    // Tipos de elementos de plantillas de informes (generales)
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA", "SALTO_PAGINA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA", "SALTO_LINEA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA", "PORTADA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO", "TITULO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO", "TEXTO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS", "NOTAS");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN", "IMAGEN");

    // Tipos de elementos de plantillas de informes (varios módulos)
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS", "COMENTARIOS");

    // Tipos de elementos de plantillas de informes (Sensores)
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS", "SENSORES_ACTIVACIONES_EVENTOS");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION", "SENSORES_INFORMACION");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO", "SENSORES_ANALISIS_HORARIO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO", "SENSORES_ANALISIS_DIARIO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO", "SENSORES_ANALISIS_COMPORTAMIENTO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS", "SENSORES_COMPARACION_PERIODOS");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO", "SENSORES_COMPARACION_PERFIL_HORARIO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES", "SENSORES_COMPARACION_CAMPOS_IGUALES");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES", "SENSORES_COMPARACION_CAMPOS_DIFERENTES");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO", "SENSORES_ANALISIS_COMPARATIVO");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES", "SENSORES_VALORES_GENERALES");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES", "SENSORES_INCREMENTOS_TOTALES");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA", "SENSORES_HISTOGRAMA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION", "SENSORES_CORRELACION");

    // Tipos de elementos de plantillas de informes (Actuadores)
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS", "ACTUADORES_INFORMACION_ACCIONES_ENVIADAS");

    // Tipos de elementos de plantillas de informes (SmartMeter)
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES", "SMARTMETER_CONSUMOS_COSTES_GENERALES");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES", "SMARTMETER_CONSUMOS_COSTES_TOTALES");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS", "SMARTMETER_COMPARACION_PERIODOS");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS", "SMARTMETER_SIMULADOR_TARIFAS");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD", "SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD", "SMARTMETER_CORTES_TENSION_ELECTRICIDAD");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD", "SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD", "SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS", "SMARTMETER_EXCESOS_CAUDAL_GAS");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA", "SMARTMETER_DESVIOS_COMPRA_ENERGIA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA", "SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA", "SMARTMETER_SIMULADOR_FACTURA");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION", "SMARTMETER_INSTALACION");

    // Tipos de elementos de plantillas de informes (Proyectos)
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE", "PROYECTOS_SIMULADOR_LINEA_BASE");
    define("TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO", "PROYECTOS_INFORMACION_PROYECTO");

    // Tipos de parámetros de plantillas de informes
    define("TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR", "SENSOR");
    define("TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES", "GRUPO_SENSORES");
    define("TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR", "ACTUADOR");
    define("TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES", "GRUPO_ACTUADORES");
    define("TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE", "LINEA_BASE");
    define("TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO", "PROYECTO");

    // Anchuras de controles de parámetros de plantillas de informes
    define("ANCHURA_SUBTITULO_PORTADA_PLANTILLA_INFORME", 50);
    define("ANCHURA_TITULO_PLANTILLA_INFORME", 50);
    define("ANCHURA_PARAMETRO_PLANTILLA_INFORME", 35);

    // Tipos de selección de elementos de plantillas de informes
    define("TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO", "FIJO");
    define("TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE", "CONFIGURABLE");

    // Modos de visibilidad de elementos de plantillas de informes
    define("MODO_VISIBILIDAD_ELEMENTO_CUALQUIER_PARAMETRO", "CUALQUIER_PARAMETRO");
    define("MODO_VISIBILIDAD_ELEMENTO_TODOS_PARAMETROS", "TODOS_PARAMETROS");

    // Periodos por defecto de informes
    define("PERIODO_DEFECTO_PERSONAL_INFORME_PLANTILLA_INFORME", PERIODO_DIA_INICIO_HOY);

    // Anchuras de columnas de parámetros de informes
    define("ANCHURAS_COLUMNAS_PARAMETROS_PLANTILLA_INFORME", serialize(array(35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_PARAMETROS", serialize(array(35)));

    // Tipos de informes del módulo Personal
    define("TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME", "PERSONAL_INFORME_PLANTILLA_INFORME");

    // Número máximo de parámetros y elementos de las plantillas de informes
    define("NUMERO_MAXIMO_PARAMETROS_PLANTILLAS_INFORMES", 10);
    define("NUMERO_MAXIMO_ELEMENTOS_PLANTILLAS_INFORMES", 100);

    // Periodos de tiempo por defecto de plantillas_de informes
    define("PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_DIA", "dia");
    define("PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_SEMANA", "semana");
    define("PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_MES", "mes");
    define("PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_ANYO", "anyo");

    // Número de columnas de parámetros de informes
    define("NUMEROS_COLUMNAS_PARAMETROS_INFORME_PLANTILLA_INFORME", "2,3");

    // Anchuras de columnas de parámetros de informes
    // Nota: Puede no ser hasta el 100% ya que influye el padding, margins, etc de los controles ...
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORME_PLANTILLA_INFORME", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_PLANTILLAS_INFORMES", serialize(array(18, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_INFORMES_AUTOMATICOS", serialize(array(18, -1)));

    // Opciones extra para añadir a las listas de plantillas de informes
    define("OPCIONES_EXTRA_LISTA_PLANTILLAS_INFORMES_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_PLANTILLAS_INFORMES_ACTUAL", "ACTUAL");

    // Alturas máximas
    define("ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN_PORTADA", 700);
    define("ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_DEFECTO", 700);
    define("ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_PAGINA_COMPLETA", 1450);


    //
    // Módulo Red
    //


    // Arquitecturas de dispositivos
    define("ARQUITECTURA_DISPOSITIVO_RPI", "RPI");
    define("ARQUITECTURA_DISPOSITIVO_BABELBOX", "BABELBOX");
    define("ARQUITECTURA_DISPOSITIVO_BYE_RADON", "BYE RADON");

    // Números de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_RED", 4);

    // Anchuras de columnas de filtros
    define("ANCHURAS_COLUMNAS_FILTRO_ALARMAS_RED", serialize(array(18, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_ACCIONES_USUARIO_RED", serialize(array(18, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_COMENTARIOS_RED", serialize(array(18, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_TOPOLOGIA_RED", serialize(array(-1, -1, -1)));

    // Números de columnas de filtros
    define("NUMERO_COLUMNAS_FILTRO_ACCIONES_USUARIO_RED", 3);

    // Periodos por defecto para las fechas iniciales de los filtros
    define("PERIODO_DEFECTO_RED_ALARMAS", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_RED_ACCIONES_USUARIO", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_RED_COMENTARIOS", PERIODO_DIA_INICIO_SEMANA);


    //
    // Módulo Localizaciones
    //


    // Modo de selección de localización actual
    define("MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA", "unica");
    define("MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE", "multiple");

    // Identificadores de selección de localizaciones múltiples
    define("ID_LOCALIZACIONES_SELECCIONADAS_AND", -5);
    define("ID_LOCALIZACIONES_SELECCIONADAS_OR", -6);

    // Número máximo de localizaciones seleccionadas
    define("MAX_LOCALIZACIONES_SELECCIONADAS", 5);

    // Estados de los equipos de las instalaciones
    define("ESTADO_EQUIPO_INSTALACION_OK", "OK");
    define("ESTADO_EQUIPO_INSTALACION_ERROR", "ERROR");
    define("ESTADO_EQUIPO_INSTALACION_PENDIENTE", "PENDIENTE");

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_LOCALIZACIONES", serialize(array(40, 20, 20, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_INSTALACIONES", serialize(array(30, 25, 15, 15, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_EQUIPOS_INSTALACIONES", serialize(array(25, 25, 15, 10, 10, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_ANOTACIONES_EQUIPOS_INSTALACIONES", serialize(array(15, 75, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_RATIOS", serialize(array(35, 20, 10, 35)));

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_LOCALIZACIONES", 4);
    define("NUMERO_COLUMNAS_TABLA_HIJAS_LOCALIZACIONES", 2);
    define("NUMERO_COLUMNAS_TABLA_INSTALACIONES", 5);
    define("NUMERO_COLUMNAS_TABLA_EQUIPOS_INSTALACIONES", 6);
    define("NUMERO_COLUMNAS_TABLA_ANOTACIONES_EQUIPOS_INSTALACIONES", 3);
    define("NUMERO_COLUMNAS_TABLA_RATIOS", 4);

    // Anchuras de columnas de selección de localización
    define("ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_UNICA", serialize(array(35, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_MULTIPLE", serialize(array(-1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_UNICA_RATIO", serialize(array(35, -1, 25)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_MULTIPLE_RATIO", serialize(array(-1, -1, 25)));

    // Anchuras de columnas de filtros y selecciones
    define("ANCHURAS_COLUMNAS_FILTRO_LOCALIZACIONES_TABLA", serialize(array(18, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_INSTALACIONES_TABLA", serialize(array(18, -1, 30, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_RATIOS_TABLA", serialize(array(18, -1)));
    define("ANCHURAS_COLUMNAS_SELECCION_MAPA_INSTALACIONES", serialize(array(35, -1)));
    define("ANCHURAS_COLUMNAS_SELECCION_IMAGEN_INSTALACION", serialize(array(35, 25, -1)));
    define("ANCHURAS_COLUMNAS_SELECCION_TOPOLOGIA_LOCALIZACION", serialize(array(35, -1)));
    define("ANCHURAS_COLUMNAS_SELECCION_TOPOLOGIA_INSTALACION", serialize(array(35, 25, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_LOCALIZACIONES_MAPA", serialize(array(18, -1)));

    // Opciones extra para añadir a la lista de selección de localización actual
    define("OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_NINGUNA", "NINGUNA");
    define("OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_TODAS", "TODAS");
    define("OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_NINGUNA_TODAS", "DESACTIVADAS_NINGUNA_TODAS");
    define("OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_TODAS", "DESACTIVADAS_TODAS");

    // Opciones extra para añadir a listas de localizaciones
    define("OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA", "NINGUNA");
    define("OPCIONES_EXTRA_LISTA_LOCALIZACIONES_TODAS", "TODAS");

    // Opciones extra para añadir a listas de instalaciones
    define("OPCIONES_EXTRA_LISTA_INSTALACIONES_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_INSTALACIONES_NINGUNA", "NINGUNA");
    define("OPCIONES_EXTRA_LISTA_INSTALACIONES_CON_IMAGEN_NINGUNA", "CON_IMAGEN_NINGUNA");

    // Número máximo de sensores y actuadores seleccionados en las listas de selección de sensores y actuadores
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_EQUIPO_INSTALACION", 100);
    define("MAX_ACTUADORES_SELECCIONADOS_LISTA_ACTUADORES_EQUIPO_INSTALACION", 100);

    // Tipos de ratio
    define("TIPO_RATIO_FIJO", "fijo");
    define("TIPO_RATIO_VARIABLE", "variable");


    //
    // Módulos Sensores y Actuadores
    //


    // Clases de nodos (sensores y actuadores)
    define("CLASE_NINGUNA", "NINGUNA");
    define("CLASE_TODAS", "TODAS");

    // Tipos de nodos (sensores y actuadores)
    define("TIPO_NINGUNO", "NINGUNO");
    define("TIPO_TODOS", "TODOS");

    // Origen de los rangos de días
    define("ORIGEN_RANGOS_DIAS_EVENTO", "evento");
    define("ORIGEN_RANGOS_DIAS_REGLA", "regla");

    // Origen de los periodos
    define("ORIGEN_PERIODOS_EVENTO", "evento");
    define("ORIGEN_PERIODOS_REGLA", "regla");

    // Opciones extra para añadir a listas de clases (de sensores y actuadores)
    define("OPCIONES_EXTRA_LISTA_CLASES_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_CLASES_NINGUNA", "NINGUNA");
    define("OPCIONES_EXTRA_LISTA_CLASES_TODAS", "TODAS");
    define("OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS", "NINGUNA_TODAS");
    define("OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA", "TODAS_NINGUNA");

    // Encapsulados ModBus (serie e IP)
    define("ENCAPSULADO_MODBUS_ASCII", "ascii");
    define("ENCAPSULADO_MODBUS_RTU", "rtu");
    define("ENCAPSULADO_MODBUS_TCP", "tcp");

    // Protocolos TCP y UDP
    define("PROTOCOLO_TCP", "tcp");
    define("PROTOCOLO_UDP", "udp");

    // Tipos de puerto serie para los interfaces de sensores
    define("TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_ARDUINO", "arduino");
    define("TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_UART", "uart");
    define("TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_XBEE", "xbee");

    // Paridad para conexiones puerto serie
    define("PARIDAD_PUERTO_SERIE_NINGUNA", "N");
    define("PARIDAD_PUERTO_SERIE_PAR", "E");
    define("PARIDAD_PUERTO_SERIE_IMPAR", "O");

    // Protocolos (de encapsulado de tramas)
    define("PROTOCOLO_EMIOS", "EMIOS");
    define("PROTOCOLO_API_XBEE", "API_XBEE");

    // Valores para sensores con clase de interfaz 'IEC 102 serie'
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_IMPORTADA", "0");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_EXPORTADA", "1");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q1", "2");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q2", "3");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q3", "4");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q4", "5");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_TOTAL", "6");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_TOTAL", "7");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_TOTAL", "8");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_I", "9");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_I", "10");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_I", "11");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_II", "12");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_II", "13");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_II", "14");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_III", "15");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_III", "16");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_III", "17");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_I", "18");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_I", "19");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_II", "20");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_II", "21");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_III", "22");
    define("VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_III", "23");

    // Comentarios de informes de información
    define("COMENTARIOS_NINGUNO", "NINGUNO");
    define("COMENTARIOS_GRAFICA", "GRAFICA");
    define("COMENTARIOS_GRAFICA_TABLA", "GRAFICA_TABLA");

    // Máximos número de nodos seleccionados para asignar localizaciones y grupos
    define("MAX_NODOS_SELECCIONADOS_LISTA_NODOS_ASIGNACION_LOCALIZACION", -1);
    define("MAX_NODOS_SELECCIONADOS_LISTA_NODOS_ASIGNACION_GRUPO", -1);


    //
    // Módulo Sensores
    //


    // Clases de sensores
    define("CLASE_SENSOR_TEMPERATURA", "TEMPERATURA");
    define("CLASE_SENSOR_HUMEDAD", "HUMEDAD");
    define("CLASE_SENSOR_LUZ_INTERIOR", "LUZ_INTERIOR");
    define("CLASE_SENSOR_VIENTO", "VIENTO");
    define("CLASE_SENSOR_ENERGIA_ACTIVA", "ENERGIA_ACTIVA");
    define("CLASE_SENSOR_ENERGIA_REACTIVA", "ENERGIA_REACTIVA");
    define("CLASE_SENSOR_CORTES_TENSION", "CORTES_TENSION");
    define("CLASE_SENSOR_COMPRA_ENERGIA", "COMPRA_ENERGIA");
    define("CLASE_SENSOR_GAS", "GAS");
    define("CLASE_SENSOR_AGUA", "AGUA");
    define("CLASE_SENSOR_GENERICA", "GENERICA");

    // Tipos de clases de sensores
    define("TIPO_CLASE_SENSOR_PUNTUAL", "PUNTUAL");
    define("TIPO_CLASE_SENSOR_INCREMENTAL", "INCREMENTAL");

    // Nombres de las tablas de los datos de cada una de las clases de sensores
    define("TABLA_DATOS_TEMPERATURA", "datos_temperatura");
    define("TABLA_DATOS_HUMEDAD", "datos_humedad");
    define("TABLA_DATOS_LUZ_INTERIOR", "datos_luz_interior");
    define("TABLA_DATOS_VIENTO", "datos_viento");
    define("TABLA_DATOS_ENERGIA_ACTIVA", "datos_energia_activa");
    define("TABLA_DATOS_ENERGIA_REACTIVA", "datos_energia_reactiva");
    define("TABLA_DATOS_CORTES_TENSION", "datos_cortes_tension");
    define("TABLA_DATOS_COMPRA_ENERGIA", "datos_compra_energia");
    define("TABLA_DATOS_GAS", "datos_gas");
    define("TABLA_DATOS_AGUA", "datos_agua");
    define("TABLA_DATOS_GENERICOS", "datos_genericos");

    // Tablas de horas de últimos cálculos de sensores
    define("TABLA_HORAS_ULTIMOS_CALCULOS_VALORES_PERIODOS_SENSORES", "horas_ultimos_calculos_valores_periodos");
    define("TABLA_HORAS_RECALCULOS_VALORES_CLASE_SENSOR", "horas_recalculos_valores_clase");

    // Sufijos de tablas
    define("SUFIJO_TABLA_CUARTOSHORA", "_cuartoshora");
    define("SUFIJO_TABLA_HORAS", "_horas");
    define("SUFIJO_TABLA_DIAS", "_dias");
    define("SUFIJO_TABLA_MESES", "_meses");
    define("SUFIJO_TABLA_INCREMENTOS", "_incrementos");
    define("SUFIJO_TABLA_TIEMPO_REAL", "_tiempo_real");

    // Tipos de sensores
    define("TIPO_SENSOR_REAL", "REAL");
    define("TIPO_SENSOR_VIRTUAL", "VIRTUAL");
    define("TIPO_SENSOR_PROCESADO", "PROCESADO");
    define("TIPO_SENSOR_EXTERNO", "EXTERNO");

    // Tipos de valores de sensores
    define("TIPO_VALORES_SENSOR_PUNTUALES", "PUNTUALES");
    define("TIPO_VALORES_SENSOR_INCREMENTALES", "INCREMENTALES");

    // Tipos de cambio de valores puntuales de sensor
    define("CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL", "GRADUAL");
    define("CAMBIO_VALORES_PUNTUALES_SENSOR_INSTANTANEO", "INSTANTANEO");

    // Tipos de horas de incrementos de valores de sensores
    define("TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO", "FIJO");
    define("TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE", "VARIABLE");

    // Tipos de incrementos de valores de sensores
    define("TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL", "INICIAL");
    define("TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL", "FINAL");

    // Clases de sensores virtuales
    define("CLASE_SENSOR_VIRTUAL_SUMA_VALORES", "SUMA_VALORES");
    define("CLASE_SENSOR_VIRTUAL_MEDIA_VALORES", "MEDIA_VALORES");
    define("CLASE_SENSOR_VIRTUAL_VALOR_MINIMO", "VALOR_MINIMO");
    define("CLASE_SENSOR_VIRTUAL_VALOR_MAXIMO", "VALOR_MAXIMO");

    // Operaciones de hijos de sensores virtuales
    define("OPERACION_HIJO_SENSOR_VIRTUAL_SUMA", "+");
    define("OPERACION_HIJO_SENSOR_VIRTUAL_RESTA", "-");

    // Clases de sensores de procesado
    define("CLASE_SENSOR_PROCESADO_FUNCION_VALORES", "FUNCION_VALORES");
    define("CLASE_SENSOR_PROCESADO_SUMA_VALORES", "SUMA_VALORES");
    define("CLASE_SENSOR_PROCESADO_MEDIA_VALORES", "MEDIA_VALORES");
    define("CLASE_SENSOR_PROCESADO_VALOR_MINIMO", "VALOR_MINIMO");
    define("CLASE_SENSOR_PROCESADO_VALOR_MAXIMO", "VALOR_MAXIMO");

    // Estados de sensor (para el filtrado)
    define("ESTADO_SENSOR_TODOS", "TODOS");
    define("ESTADO_SENSOR_OK", "OK");
    define("ESTADO_SENSOR_ERROR", "ERROR");
    define("ESTADO_SENSOR_TIMEOUT", "TIMEOUT");
    define("ESTADO_SENSOR_ALARMA", "ALARMA");
    define("ESTADO_SENSOR_ERROR_RECUPERACION_VALORES", "ERROR_RECUPERACION_VALORES");
    define("ESTADO_SENSOR_ERROR_CALCULO_VALORES", "ERROR_CALCULO_VALORES");
    define("ESTADO_SENSOR_ERROR_CALCULO_VALORES_CLASE", "ERROR_CALCULO_VALORES_CLASE");
    define("ESTADO_SENSOR_OPERACIONES_DATOS_PENDIENTES", "OPERACIONES_DATOS_PENDIENTES");
    define("ESTADO_SENSOR_IMPORTACIONES_VALORES_PENDIENTES", "IMPORTACIONES_VALORES_PENDIENTES");
    define("ESTADO_SENSOR_RECALCULOS_VALORES_CLASE_PENDIENTES", "RECALCULOS_VALORES_CLASE_PENDIENTES");
    define("ESTADO_SENSOR_ULTIMOS_VALORES_ANTIGUOS_PROCESADO", "ULTIMOS_VALORES_ANTIGUOS_PROCESADO");
    define("ESTADO_SENSOR_SIN_VALORES", "SIN_VALORES");

    // Valor de prueba por defecto para la evaluación de función de sensor de procesado
    define("VALOR_PRUEBA_DEFECTO_FUNCION_SENSOR_PROCESADO", 1);

    // Funciones de hijos de sensores de procesado
    define("FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD", "IDENTIDAD");
    define("FUNCION_HIJO_SENSOR_PROCESADO_MEDIA", "MEDIA");
    define("FUNCION_HIJO_SENSOR_PROCESADO_DESVIACION_ESTANDAR", "DESVIACION_ESTANDAR");
    define("FUNCION_HIJO_SENSOR_PROCESADO_ACUMULADO", "ACUMULADO");
    define("FUNCION_HIJO_SENSOR_PROCESADO_INCREMENTO", "INCREMENTO");
    define("FUNCION_HIJO_SENSOR_PROCESADO_CONSUMO_ENERGIA_BRUTO", "CONSUMO_ENERGIA_BRUTO");

    // Clases de sensores externos
    define("CLASE_SENSOR_EXTERNO_NINGUNA", "NINGUNA");
    define("CLASE_SENSOR_EXTERNO_FICHEROS_CSV", "FICHEROS_CSV");
    define("CLASE_SENSOR_EXTERNO_HTTP_EMIOS", "HTTP_EMIOS");
    define("CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO", "HTTP_XML_POWERSTUDIO");
    define("CLASE_SENSOR_EXTERNO_MODBUS_IP", "MODBUS_IP");
    define("CLASE_SENSOR_EXTERNO_WIBEEE", "WIBEEE");
    define("CLASE_SENSOR_EXTERNO_API", "API");

    define('API_EXTERNA_SENSORES_USUARIO','emios');
    define('API_EXTERNA_SENSORES_PASS','energy.min2021');
    define('API_EXTERNA_SENSORES_DIRECCION','3.251.47.212:5000');
    define('API_EXTERNA_SGCLIMA_DIRECCION','3.251.47.212:5001');
    //define('API_EXTERNA_SGCLIMA_DIRECCION','192.168.108.24:5000');

    // Formatos de ficheros de valores
    define("FORMATO_FICHERO_VALORES_PERSONALIZADO", "personalizado");
    define("FORMATO_FICHERO_VALORES_WEB_EMIOS", "web_emios");
    define("FORMATO_FICHERO_VALORES_LECTOR_CONTADORES_EMIOS", "lector_contadores_emios");
    define("FORMATO_FICHERO_VALORES_DATALOGGER_SATEL", "datalogger_satel");
    define("FORMATO_FICHERO_VALORES_DATADIS", "datadis");

    // Causas de envio de valores de los sensores
    define("CAUSA_ENVIO_VALORES_SENSOR_VALOR_INICIAL", "VALOR_INICIAL");
    define("CAUSA_ENVIO_VALORES_SENSOR_FREC_ENVIO", "FREC_ENVIO");
    define("CAUSA_ENVIO_VALORES_SENSOR_PETICION", "PETICION");
    define("CAUSA_ENVIO_VALORES_SENSOR_EVENTOS_INSTANTANEOS", "EVENTOS_INSTANTANEOS");
    define("CAUSA_ENVIO_VALORES_SENSOR_EVENTOS_ACTIVADOS", "EVENTOS_ACTIVADOS");
    define("CAUSA_ENVIO_VALORES_SENSOR_EVENTOS_DESACTIVADOS", "EVENTOS_DESACTIVADOS");
    define("CAUSA_ENVIO_VALORES_SENSOR_MANUAL", "MANUAL");

    // Estados de importaciones pendientes
    define("ESTADO_IMPORTACION_PENDIENTE_EN_ESPERA", "EN_ESPERA");
    define("ESTADO_IMPORTACION_PENDIENTE_PREPARADO", "PREPARADO");
    define("ESTADO_IMPORTACION_PENDIENTE_EN_EJECUCION", "EN_EJECUCION");
    define("ESTADO_IMPORTACION_PENDIENTE_REALIZADA", "REALIZADA");
    define("ESTADO_IMPORTACION_PENDIENTE_ESPERANDO_REINTENTO", "ESPERANDO_REINTENTO");
    define("ESTADO_IMPORTACION_PENDIENTE_DESCONOCIDO", "DESCONOCIDO");

    // Errores de importaciones pendientes
    define("ERROR_IMPORTACION_PENDIENTE_NINGUNO", "NINGUNO");
    define("ERROR_IMPORTACION_PENDIENTE_DATOS_SENSOR_BLOQUEADOS", "DATOS_SENSOR_BLOQUEADOS");
    define("ERROR_IMPORTACION_PENDIENTE_DESCONOCIDO", "DESCONOCIDO");

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_SENSORES", 9);
    define("NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_VIRTUALES", 2);
    define("NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_CON_VARIABLES", 6);
    define("NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_SIN_VARIABLES", 5);
    define("NUMERO_COLUMNAS_TABLA_EVENTOS", 7);
    define("NUMERO_COLUMNAS_TABLA_HISTORICO_EVENTOS", 5);
    define("NUMERO_COLUMNAS_TABLA_PERCENTILES_VALORES_ANALISIS_HORARIO", 4);
    define("NUMERO_COLUMNAS_TABLA_MAXIMOS_MINIMOS_MEDIAS_MEDIDAS_ANALISIS_DIARIO", 4);
    define("NUMERO_COLUMNAS_TABLA_VALORES_DIA_PUNTUALES_ANALISIS_DIARIO", 4);
    define("NUMERO_COLUMNAS_TABLA_VALORES_DIA_INCREMENTALES_ANALISIS_DIARIO", 5);
    define("NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_PUNTUAL", 4);
    define("NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_INCREMENTAL", 5);
    define("NUMERO_COLUMNAS_TABLA_INCREMENTOS_INCREMENTOS_TOTALES", 3);
    define("NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_PUNTUAL", 4);
    define("NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_INCREMENTAL", 5);
    define("NUMERO_COLUMNAS_TABLA_VALORES_PARETO_ANALISIS_COMPARATIVO", 3);
    define("NUMERO_COLUMNAS_TABLA_PERCENTILES_HISTOGRAMA", 4);
    define("NUMERO_COLUMNAS_TABLA_MEDIDAS_ESTADISTICAS_HISTOGRAMA", 2);
    define("NUMERO_COLUMNAS_TABLA_FUNCION_CORRELACION", 3);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_CON_VARIABLES", serialize(array(30, 10, 20, 15, 10, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_SIN_VARIABLES", serialize(array(35, 10, 20, 20, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO", serialize(array(30, 10, 20, 15, 10, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_PUNTUAL", serialize(array(35, 25, 25, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_INCREMENTAL", serialize(array(30, 22, 22, 13, 13)));
    define("ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_PUNTUAL", serialize(array(35, 25, 25, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_INCREMENTAL", serialize(array(30, 22, 22, 13, 13)));
    define("ANCHURAS_COLUMNAS_TABLA_VALORES_PARETO_ANALISIS_COMPARATIVO", serialize(array(20, 60, 20)));

    // Número máximo de sensores seleccionados en las listas de selección de sensores
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_HIJOS_COMPRA_ENERGIA", 50);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ANALISIS_COMPORTAMIENTO", 50);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_COMPARACION_CAMPOS_IGUALES", 4);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_VALORES_GENERALES", 100);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_INCREMENTOS_TOTALES", 100);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ANALISIS_COMPARATIVO", 100);

    // Número máximo de sensores para el dibujado de gráficas
    define("NUMERO_MAXIMO_SENSORES_GRAFICAS_VALORES_GENERALES", 10);
    define("NUMERO_MAXIMO_SENSORES_GRAFICAS_INCREMENTOS_TOTALES", 10);

    // Número máximo de eventos seleccionados en las listas de selección de eventos
    define("MAX_EVENTOS_SELECCIONADOS_LISTA_EVENTOS_ACTIVACIONES_EVENTOS", 5);

    // Duración de periodos por defecto en el módulo Sensores
    define("DIAS_DURACION_DEFECTO_SENSORES_PERIODO", 7);

    // Periodos por defecto para las fechas iniciales de los informes
    define("PERIODO_DEFECTO_SENSORES_HISTORICO_EVENTOS", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_SENSORES_ACTIVACIONES_EVENTOS", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_TEMPERATURA", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_HUMEDAD", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_LUZ_INTERIOR", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_VIENTO", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_ENERGIA", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_CORTES_TENSION", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_COMPRA_ENERGIA", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_GAS", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INFORMACION_GENERICA", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_ANALISIS_HORARIO", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_ANALISIS_DIARIO", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_ANALISIS_COMPORTAMIENTO", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SENSORES_COMPARACION_PERIODOS", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_COMPARACION_PERFIL_HORARIO", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_COMPARACION_CAMPOS_IGUALES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_COMPARACION_CAMPOS_DIFERENTES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_ANALISIS_COMPARATIVO", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_VALORES_GENERALES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_INCREMENTOS_TOTALES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_HISTOGRAMA", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SENSORES_CORRELACION", PERIODO_DIA_INICIO_SEMANA);

    // Números de columnas de filtros
    define("NUMEROS_COLUMNAS_FILTRO_HISTORICO_EVENTOS", "2,4");

    // Anchuras de columnas de filtros
    define("ANCHURAS_COLUMNAS_FILTRO_SENSORES_TABLA", serialize(array(18, -1, -1, -1, 15, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_GRUPOS_SENSORES_TABLA", serialize(array(18, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_SENSORES_MAPA", serialize(array(18, -1, -1, -1, 15, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_EVENTOS_TABLA", serialize(array(18, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_HISTORICO_EVENTOS", serialize(array(18, -1, -1, -1, -1, -1)));

    // Número de columnas de parámetros de informes
    define("NUMERO_COLUMNAS_PARAMETROS_ACTIVACIONES_EVENTOS", 3);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_TEMPERATURA", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_HUMEDAD", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_LUZ_INTERIOR", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_VIENTO", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_ENERGIA", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_CORTES_TENSION", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_COMPRA_ENERGIA", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_GAS", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_AGUA", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_GENERICA", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_ANALISIS_HORARIO", 3);
    define("NUMERO_COLUMNAS_PARAMETROS_ANALISIS_DIARIO", 3);
    define("NUMEROS_COLUMNAS_PARAMETROS_ANALISIS_COMPORTAMIENTO", "2,3");
    define("NUMERO_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_VALORES", 5);
    define("NUMEROS_COLUMNAS_PARAMETROS_COMPARACION_PERFIL_HORARIO", "4,4");
    define("NUMERO_COLUMNAS_PARAMETROS_COMPARACION_VALORES_IGUALES", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_COMPARACION_VALORES_DIFERENTES", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_VALORES_GENERALES", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_INCREMENTOS_TOTALES", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_ANALISIS_COMPARATIVO", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_HISTOGRAMA", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_CORRELACION", 4);

    // Anchuras de columnas de parámetros de informes
    // Nota: Puede no ser hasta el 100% ya que influye el padding, margins, etc de los controles ...
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR", serialize(array(35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_SENSOR_HIJO", serialize(array(35, 35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CLASE", serialize(array(-1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO", serialize(array(-1, 35, -1, 10)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO", serialize(array(35, -1, 10)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO", serialize(array(-1, -1, 10)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSORES", serialize(array(100)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_FILTRO_EVENTOS_ACTIVACIONES_EVENTOS", serialize(array(-1, -1, 35, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ACTIVACIONES_EVENTOS", serialize(array(-1, -1, 18, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_TEMPERATURA", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_HUMEDAD", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_LUZ_INTERIOR", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_VIENTO", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_ENERGIA", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_CORTES_TENSION", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_COMPRA_ENERGIA", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_GAS", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_AGUA", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_GENERICA", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_HORARIO", serialize(array(-1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_DIARIO", serialize(array(-1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_COMPORTAMIENTO", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_VALORES", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_PERFIL_HORARIO", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_VALORES_IGUALES", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_VALORES_DIFERENTES", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_VALORES_GENERALES", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INCREMENTOS_TOTALES", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_COMPARATIVO", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_HISTOGRAMA", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CORRELACION", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_EVENTOS", serialize(array(20, 10, 15, 22, 15, 8, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_HISTORICO_EVENTOS", serialize(array(15, 25, 25, 25, 10)));

    // Máximo viento considerado calma
    define("MAXIMA_VELOCIDAD_VIENTO_CALMA", 1);

    // Número máximo de sensores secundarios en la comparación de campos iguales
    define("NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES", 4);

    // Orígenes de eventos
    define("ORIGEN_EVENTO_TODOS", "todos");
    define("ORIGEN_EVENTO_SENSOR", "sensor");
    define("ORIGEN_EVENTO_GRUPO_SENSORES", "grupo_sensores");

    // Periodos de tiempo de eventos
    define("PERIODO_TIEMPO_EVENTO_HORA", "hora");
    define("PERIODO_TIEMPO_EVENTO_DIA", "dia");
    define("PERIODO_TIEMPO_EVENTO_SEMANA", "semana");
    define("PERIODO_TIEMPO_EVENTO_MES", "mes");

    // Tipos de evento
    define("TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO", "INCREMENTO_TEMPORAL_MINIMO");
    define("TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO", "INCREMENTO_TEMPORAL_MAXIMO");
    define("TIPO_EVENTO_VALOR_MINIMO", "VALOR_MINIMO");
    define("TIPO_EVENTO_VALOR_MAXIMO", "VALOR_MAXIMO");
    define("TIPO_EVENTO_VALORES_MINIMO_MAXIMO", "VALORES_MINIMO_MAXIMO");
    define("TIPO_EVENTO_INTERVALO_VALORES", "INTERVALO_VALORES");
    define("TIPO_EVENTO_VALOR_EXACTO", "VALOR_EXACTO");
    define("TIPO_EVENTO_VALOR_DIFERENTE", "VALOR_DIFERENTE");
    define("TIPO_EVENTO_VALOR_EXACTO_BITS", "VALOR_EXACTO_BITS");
    define("TIPO_EVENTO_VALOR_DIFERENTE_BITS", "VALOR_DIFERENTE_BITS");
    define("TIPO_EVENTO_VALOR_REPETIDO", "VALOR_REPETIDO");

    // Tipos de evento de incrementos acumulados máximos
    define("TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL", "INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL");
    define("TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO", "INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO");

    // Tipos de evento de línea base y de perfil horario
    define("TIPO_EVENTO_LINEA_BASE", "LINEA_BASE");
    define("TIPO_EVENTO_PERFIL_HORARIO", "PERFIL_HORARIO");

    // Alarmas de eventos
    define("ALARMA_EVENTO_TODOS", "TODOS");
    define("ALARMA_EVENTO_SI", "SI");
    define("ALARMA_EVENTO_NO", "NO");

    // Estados de eventos
    define("ESTADO_EVENTO_TODOS", "TODOS");
    define("ESTADO_EVENTO_ACTIVADO", "ACTIVADO");
    define("ESTADO_EVENTO_DESACTIVADO", "DESACTIVADO");

    // Tipos de fecha de histórico de eventos
    define("TIPO_FECHA_HISTORICO_EVENTOS_EVENTO", "EVENTO");
    define("TIPO_FECHA_HISTORICO_EVENTOS_VALORES", "VALORES");

    // Clases de interfaces de sensores
    define("CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM", "HTTP_ABBODINCEM");
    define("CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE", "ASINCRONO_SERIE");
    define("CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS", "VALORES_ALEATORIOS");
    define("CLASE_INTERFAZ_SENSOR_VALORES_FIJOS", "VALORES_FIJOS");
    define("CLASE_INTERFAZ_SENSOR_MODBUS_SERIE", "MODBUS_SERIE");
    define("CLASE_INTERFAZ_SENSOR_MODBUS_IP", "MODBUS_IP");
    define("CLASE_INTERFAZ_SENSOR_IEC102_SERIE", "IEC102_SERIE");

    // Campos de sensores
    define("CAMPO_TEMPERATURA", "temperatura");
    define("CAMPO_GRADOS_HORA_CALEFACCION", "grados_hora_calefaccion");
    define("CAMPO_GRADOS_HORA_REFRIGERACION", "grados_hora_refrigeracion");
    define("CAMPO_GRADOS_DIA_CALEFACCION", "grados_dia_calefaccion");
    define("CAMPO_GRADOS_DIA_REFRIGERACION", "grados_dia_refrigeracion");
    define("CAMPO_HUMEDAD", "humedad");
    define("CAMPO_ILUMINACION", "iluminacion");
    define("CAMPO_LUZ_ARTIFICIAL", "luz_artificial");
    define("CAMPO_VELOCIDAD", "velocidad");
    define("CAMPO_DIRECCION", "direccion");
    define("CAMPO_DIA_NOCHE", "dia_noche");
    define("CAMPO_ABSOLUTO", "absoluto");
    define("CAMPO_INCREMENTO", "incremento");
    define("CAMPO_INCREMENTO_POTENCIA", "incremento_potencia");
    define("CAMPO_TRAMO", "tramo");
    define("CAMPO_COSTE", "coste");
    define("CAMPO_SOBREPOTENCIA", "sobrepotencia");
    define("CAMPO_COSENO_PHI", "coseno_phi");
    define("CAMPO_PENALIZABLE", "penalizable");
    define("CAMPO_CORTES", "cortes");
    define("CAMPO_CONSUMO_ESTIMADO", "consumo_estimado");
    define("CAMPO_CONSUMO_REAL", "consumo_real");
    define("CAMPO_DESVIO_CONSUMO", "desvio_consumo");
    define("CAMPO_COSTE_DESVIO", "coste_desvio");
    define("CAMPO_CONSUMO", "consumo");
    define("CAMPO_VALOR", "valor");
    define("CAMPO_HORAS", "horas");

    // Campos (con agregación para los sensores genéricos)
    define("CAMPO_VALOR_MEDIA", "valor_media");
    define("CAMPO_VALOR_SUMA", "valor_suma");
    define("CAMPO_INCREMENTO_MEDIA", "incremento_media");
    define("CAMPO_INCREMENTO_SUMA", "incremento_suma");

    // Campo ninguno y todos
    define("CAMPO_NINGUNO", "ninguno");
    define("CAMPO_TODOS", "todos");

    // Número de sensores en informes de sensores
    define("NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES", 5);
    define("NUMERO_SENSORES_INDEPENDIENTES_CORRELACION", 4);

    // Número de clases de sensor en informes de sensores
    define("NUMERO_CLASES_SENSOR_VALORES_GENERALES", 3);
    define("NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES", 3);

    // Agregaciones
    define("AGREGACION_NINGUNA", "ninguna");
    define("AGREGACION_SUMA", "suma");
    define("AGREGACION_MEDIA", "media");
    define("AGREGACION_SUMA_CLASES", "suma_clases");
    define("AGREGACION_MEDIA_CLASES", "media_clases");

    // Tipos de agregación
    define("TIPOS_AGREGACION_TODOS", "todos");
    define("TIPOS_AGREGACION_SIN_CLASES", "sin_clases");
    define("TIPOS_AGREGACION_CON_CLASES", "con_clases");

    // Detalles
    define("DETALLE_MINIMO", "minimo");
    define("DETALLE_MEDIO", "medio");
    define("DETALLE_MAXIMO", "maximo");

    // Número de valores del histograma (dependiendo del detalle)
    define("NUMERO_MAXIMO_VALORES_HISTOGRAMA_DETALLE_MINIMO", 10);
    define("NUMERO_MAXIMO_VALORES_HISTOGRAMA_DETALLE_MEDIO", 25);
    define("NUMERO_MAXIMO_VALORES_HISTOGRAMA_DETALLE_MAXIMO", 100);

    // Número de valores a mostrar en las curvas de estadística
    define("NUMERO_VALORES_CURVA_CORRELACION", 150);

    // Tipos de informes del módulo Sensores
    define("TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA", "SENSORES_TEMPERATURA");
    define("TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD", "SENSORES_HUMEDAD");
    define("TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR", "SENSORES_LUZ_INTERIOR");
    define("TIPO_INFORME_SENSORES_INFORMACION_VIENTO", "SENSORES_VIENTO");
    define("TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA", "SENSORES_ENERGIA_ACTIVA");
    define("TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA", "SENSORES_ENERGIA_REACTIVA");
    define("TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION", "SENSORES_CORTES_TENSION");
    define("TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA", "SENSORES_COMPRA_ENERGIA");
    define("TIPO_INFORME_SENSORES_INFORMACION_GAS", "SENSORES_GAS");
    define("TIPO_INFORME_SENSORES_INFORMACION_AGUA", "SENSORES_AGUA");
    define("TIPO_INFORME_SENSORES_INFORMACION_GENERICA", "SENSORES_GENERICA");
    define("TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS", "SENSORES_ACTIVACIONES_EVENTOS");
    define("TIPO_INFORME_SENSORES_ANALISIS_HORARIO", "SENSORES_ANALISIS_HORARIO");
    define("TIPO_INFORME_SENSORES_ANALISIS_DIARIO", "SENSORES_ANALISIS_DIARIO");
    define("TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO", "SENSORES_ANALISIS_COMPORTAMIENTO");
    define("TIPO_INFORME_SENSORES_COMPARACION_PERIODOS", "SENSORES_COMPARACION_PERIODOS");
    define("TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO", "SENSORES_COMPARACION_PERFIL_HORARIO");
    define("TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES", "SENSORES_COMPARACION_CAMPOS_IGUALES");
    define("TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES", "SENSORES_COMPARACION_CAMPOS_DIFERENTES");
    define("TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO", "SENSORES_ANALISIS_COMPARATIVO");
    define("TIPO_INFORME_SENSORES_VALORES_GENERALES", "SENSORES_VALORES_GENERALES");
    define("TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES", "SENSORES_INCREMENTOS_TOTALES");
    define("TIPO_INFORME_SENSORES_HISTOGRAMA", "SENSORES_HISTOGRAMA");
    define("TIPO_INFORME_SENSORES_CORRELACION", "SENSORES_CORRELACION");

    // Opciones extra para añadir a listas de granularidades
    define("OPCIONES_EXTRA_LISTA_GRANULARIDADES_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_GRANULARIDADES_NINGUNA", "NINGUNA");
    define("OPCIONES_EXTRA_LISTA_GRANULARIDADES_TODAS", "TODAS");

    // Número de decimales a mostrar en el error estándar de correlación
    define("NUMERO_DECIMALES_ERROR_ESTANDAR_CORRELACION", 5);
    define("NUMERO_DECIMALES_COEFICIENTE_VARIACION_CORRELACION", 5);
    define("NUMERO_DECIMALES_COEFICIENTE_CORRELACION_CORRELACION", 5);

    // Granularidades de valores y eventos
    define("GRANULARIDAD_NINGUNA", "NINGUNA");
    define("GRANULARIDAD_TODAS", "TODAS");
    define("GRANULARIDAD_TIEMPO_REAL", "TIEMPO_REAL");
    define("GRANULARIDAD_CUARTOHORARIA", "CUARTOHORARIA");
    define("GRANULARIDAD_HORARIA", "HORARIA");
    define("GRANULARIDAD_DIARIA", "DIARIA");
    define("GRANULARIDAD_MENSUAL", "MENSUAL");

    // Modos de localización
    define("MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS", "COORDENADAS_GEOGRAFICAS");
    define("MODO_LOCALIZACION_LOCALIDAD", "LOCALIDAD");
    define("MODO_LOCALIZACION_IDEMA", "IDEMA");

    // Errores en valores de ficheros CSV:
    // - Errores en lectura de valores
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_DELIMITADOR_COLUMNAS_INCORRECTO", "DELIMITADOR_COLUMNAS_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_PUNTO_DECIMAL_INCORRECTO", "PUNTO_DECIMAL_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_FECHA_INCORRECTO", "NUMERO_COLUMNA_FECHA_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_HORA_INCORRECTO", "NUMERO_COLUMNA_HORA_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_FORMATO_FECHA_HORA_INCORRECTO", "FORMATO_FECHA_HORA_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_ANYO_FECHA_FUERA_LIMITES", "ANYO_FECHA_FUERA_LIMITES");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_HORARIO_VERANO_INCORRECTO", "NUMERO_COLUMNA_HORARIO_VERANO_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_HORARIO_VERANO_INCORRECTO", "HORARIO_VERANO_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_VALORES_INCORRECTO", "NUMERO_VALORES_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_VALOR_INCORRECTO", "NUMERO_COLUMNA_VALOR_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_VALOR_INCORRECTO", "VALOR_INCORRECTO");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_CODIFICACION_INCORRECTA", "CODIFICACION_INCORRECTA");
    define("ERROR_LECTURA_VALORES_FICHERO_CSV_VALORES_INCOMPLETOS", "VALORES_INCOMPLETOS");
    // - Errores al procesar los valores
    define("ERROR_PROCESADO_VALORES_FICHERO_CSV_INTERVALO_TIEMPO_INCREMENTOS_VALORES_INCORRECTO", "INTERVALO_TIEMPO_INCREMENTOS_VALORES_INCORRECTO");
    define("ERROR_PROCESADO_VALORES_FICHERO_CSV_SIN_FILAS_VALORES", "SIN_FILAS_VALORES");
    define("ERROR_PROCESADO_VALORES_FICHERO_CSV_HAY_FILAS_VALORES_ERRONEOS", "HAY_FILAS_VALORES_ERRONEOS");

    // Errores en recuperación de valores HTTP Emios
    define("ERROR_RECUPERACION_VALORES_HTTP_EMIOS_CODIGO_RESPUESTA_INCORRECTO", "CODIGO_RESPUESTA_INCORRECTO");
    define("ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_APERTURA_SOCKET", "ERROR_APERTURA_SOCKET");
    define("ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_PETICION_HTTP", "ERROR_PETICION_HTTP");
    define("ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_PETICION_HTTP_DEMASIADAS_PETICIONES_API_AEMET", "DEMASIADAS_PETICIONES_API_AEMET");
    define("ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_PROCESADO_RESPUESTA", "ERROR_PROCESADO_RESPUESTA");
    define("ERROR_RECUPERACION_VALORES_HTTP_EMIOS_TIPO_INFORMACION_NO_DISPONIBLE", "TIPO_INFORMACION_NO_DISPONIBLE");

    // Errores en recuperación de valores 'HTTP XML PowerStudio'
    define("ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_APERTURA_SOCKET", "ERROR_APERTURA_SOCKET");
    define("ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_PETICION_HTTP", "ERROR_PETICION_HTTP");
    define("ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_CONEXION_CREDENCIALES_INCORRECTAS", "ERROR_CONEXION_CREDENCIALES_INCORRECTAS");
    define("ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_PROCESADO_RESPUESTA", "ERROR_PROCESADO_RESPUESTA");

    // Errores en recuperación de valores Modbus IP
    define("ERROR_RECUPERACION_VALORES_MODBUS_IP_ERROR_APERTURA_SOCKET", "ERROR_APERTURA_SOCKET");
    define("ERROR_RECUPERACION_VALORES_MODBUS_IP_SIN_VALORES_LEIDOS", "SIN_VALORES_LEIDOS");
    define("ERROR_RECUPERACION_VALORES_MODBUS_IP_ERROR_LECTURA_VALORES", "ERROR_LECTURA_VALORES");


    // Errores en recuperación de valores de APIs
    define("ERROR_RECUPERACION_VALORES_API_ERROR_APERTURA_SOCKET", "ERROR_APERTURA_SOCKET");
    define("ERROR_RECUPERACION_VALORES_API_ERROR_PETICION_HTTP", "ERROR_PETICION_HTTP");
    define("ERROR_RECUPERACION_VALORES_API_ERROR_CONEXION_CREDENCIALES_INCORRECTAS", "ERROR_CONEXION_CREDENCIALES_INCORRECTAS");
    define("ERROR_RECUPERACION_VALORES_API_ERROR_LECTURA_VALORES", "ERROR_LECTURA_VALORES");



    // Errores en cálculo de valores de sensores de procesado
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_PROCESADO_VALORES", "SENSOR_HIJO_SIN_PROCESADO_VALORES");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_GRANULARIDAD_CUARTOHORARIA", "SENSOR_HIJO_SIN_GRANULARIDAD_CUARTOHORARIA");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_RECALCULOS_PENDIENTES", "SENSOR_HIJO_RECALCULOS_PENDIENTES");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_POSTERIORES_HORA_VALOR_SENSOR_PROCESADO", "SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_POSTERIORES_HORA_VALOR_SENSOR_PROCESADO");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_HORA_CALCULO_VALORES", "SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_HORA_CALCULO_VALORES");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_VALORES_NO_CALCULADOS", "SENSOR_HIJO_VALORES_NO_CALCULADOS");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_CALCULO_FUNCION_PROCESADO_NO_POSIBLE", "SENSOR_HIJO_CALCULO_FUNCION_PROCESADO_NO_POSIBLE");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_ERROR_CALCULO_FUNCION_PROCESADO", "SENSOR_HIJO_ERROR_CALCULO_FUNCION_PROCESADO");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO", "SENSOR_HIJO_SIN_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO", "SENSOR_HIJO_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO", "SENSOR_HIJO_SIN_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO");
    define("ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO", "SENSOR_HIJO_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO");
    define("ERROR_CALCULO_VALORES_PROCESADO_ERROR_FUNCION_VALORES", "ERROR_FUNCION_VALORES");

    // Errores en cálculo de valores de clase de sensor
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_DESCONOCIDO", "DESCONOCIDO");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_HUECOS_VALORES_TIEMPO_REAL", "HUECOS_VALORES_TIEMPO_REAL");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_HUECOS_INCREMENTOS_VALORES_TIEMPO_REAL", "HUECOS_INCREMENTOS_VALORES_TIEMPO_REAL");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_FIJO", "ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_FIJO");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_PRECIO_MEDIO_PERIODO_CALCULO_COSTES_PASS_POOL", "SIN_PRECIO_MEDIO_PERIODO_CALCULO_COSTES_PASS_POOL");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_FECHA_NO_ENCONTRADA_PERIODOS_CALCULO_COSTES_PASS_POOL", "FECHA_NO_ENCONTRADA_PERIODOS_CALCULO_COSTES_PASS_POOL");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_PERIODOS_CALCULO_COSTES_PASS_POOL", "SIN_PERIODOS_CALCULO_COSTES_PASS_POOL");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_POOL", "ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_POOL");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_PASS_THROUGH", "SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_PASS_THROUGH");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_THROUGH", "ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_THROUGH");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_CIERRE", "SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_CIERRE");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_CIERRE", "ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_CIERRE");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_FILA_SENSOR_ENERGIA_ACTIVA_ASOCIADO", "SIN_FILA_SENSOR_ENERGIA_ACTIVA_ASOCIADO");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_FILA_SENSOR_ASOCIADO", "SIN_FILA_SENSOR_ASOCIADO");
    define("ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALOR_DESVIOS_ENERGIA_ELECTRICA", "SIN_VALOR_DESVIOS_ENERGIA_ELECTRICA");

    // Separador de parámetros de cadenas de error
    define("SEPARADOR_PARAMETROS_CADENA_ULTIMO_ERROR", ",");
    define("SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO", ",");
    define("SEPARADOR_PARAMETROS_CADENA_ERROR_VALORES_CLASE", ",");

    // Cabeceras en fichero CSV
    define("FICHERO_CSV_CON_CABECERAS", "con_cabeceras");
    define("FICHERO_CSV_SIN_CABECERAS", "sin_cabeceras");

    // Formatos de cadenas de valores de sensor
    define("FORMATO_CADENA_VALORES_SENSOR_REDUCIDO", "REDUCIDO");
    define("FORMATO_CADENA_VALORES_SENSOR_COMPLETO", "COMPLETO");

    // Opciones extra para añadir a las listas de orígenes de eventos
    define("OPCIONES_EXTRA_LISTA_ORIGENES_EVENTO_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_ORIGENES_EVENTO_TODOS", "TODOS");

    // Opciones extra para añadir a las listas de identificadores de orígenes de eventos
    define("OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_TODOS", "TODOS");

    // Opciones extra para añadir a las listas de granularidades de eventos
    define("OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_TODAS", "TODAS");

    // Número máximo de eventos en el informe de activaciones de eventos
    define("NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS", 5);

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_ACTIVACIONES_EVENTO", 5);
    define("NUMERO_COLUMNAS_TABLA_EVOLUCION_VALORES", 3);
    define("NUMERO_COLUMNAS_TABLA_DIFERENCIAS_VALORES", 4);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_ACTIVACIONES_EVENTO", serialize(array(10, 15, 15, 35, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_DIFERENCIAS_VALORES", serialize(array(40, 20, 20, 20)));

    // Números máximos de filas de tablas
    define("NUMERO_MAXIMO_FILAS_TABLAS_ACTIVACIONES_EVENTOS", 100);

    // Tipos de perfil horario
    define("TIPO_PERFIL_HORARIO_DIARIO", "diario");
    define("TIPO_PERFIL_HORARIO_SEMANAL", "semanal");
    define("TIPO_PERFIL_HORARIO_CONFIGURABLE", "configurable");

    // Factores de timeouts de envío
    define("FACTOR_FRECUENCIA_ENVIO_TIMEOUTS_SENSORES_REALES", 2);
    define("FACTOR_FRECUENCIA_ENVIO_TIMEOUTS_SENSORES_EXTERNOS", 5);

    // Números máximos de importaciones simultáneas
    define("NUMERO_MAXIMO_IMPORTACIONES_VALORES_SENSOR_RED_SIMULTANEAS", 2);
    define("NUMERO_MAXIMO_IMPORTACIONES_VALORES_SENSOR_TOTALES_SIMULTANEAS", 5);

    // Longitudes máximas de descripciones de parámetros
    define("LONGITUD_MAXIMA_CADENA_DESCRIPCION_PARAMETRO_SENSOR", 100);


    // Elementos de informes del módulo

    // Elementos de informe (Activaciones de eventos)
    define("ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_SENSOR", "grafica_valores_sensor");
    define("ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_ACUMULADOS_SENSOR", "grafica_valores_acumulados_sensor");
    define("ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICAS_ACTIVACIONES_EVENTOS", "graficas_activaciones_eventos");
    define("ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TABLAS_ACTIVACIONES_EVENTOS", "tablas_activaciones_eventos");

    // Elementos de informe (Información)
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_TEMPERATURA", "grafica_temperatura");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_HUMEDAD", "grafica_humedad");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ", "grafica_luz");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ_ARTIFICIAL", "grafica_luz_artificial");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VELOCIDAD_VIENTO", "grafica_velocidad_viento");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DIRECCION_VIENTO", "grafica_direccion_viento");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DIA_NOCHE", "grafica_dia_noche");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DURACION_DIAS", "grafica_duracion_dias");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS", "grafica_valores_acumulados");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION", "grafica_cortes_tension");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION_ACUMULADOS", "grafica_cortes_tension_acumulados");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_ACELERACION", "grafica_aceleracion");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_ORIENTACION", "grafica_orientacion");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_NIVEL_BATERIA", "grafica_nivel_bateria");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_FLAGS", "grafica_flags");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_TIPO_MENSAJE", "grafica_tipo_mensaje");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR", "descripcion_sensor");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION", "texto_informacion");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS", "tabla_comentarios");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICOS_VIENTO", "graficos_viento");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_TEMPERATURA", "mapa_calor_temperatura");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_HUMEDAD", "mapa_calor_humedad");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ", "mapa_calor_luz");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ_ARTIFICIAL", "mapa_calor_luz_artificial");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VELOCIDAD_VIENTO", "mapa_calor_velocidad_viento");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_DIRECCION_VIENTO", "mapa_calor_direccion_viento");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES", "mapa_calor_valores");
    define("ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_CORTES_TENSION", "mapa_calor_cortes_tension");

    // Elementos de informe (Análisis horario)
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_MAPA_CALOR_VALORES", "mapa_calor_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_MEDIAS_VALORES", "grafica_medias_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES", "grafica_coeficientes_variacion_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_LORENZ_VALORES", "grafica_lorenz_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_TABLA_PERCENTILES_VALORES", "tabla_percentiles_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_PORCENTAJES_VALORES", "grafica_porcentajes_valores");

    // Elementos de informe (Análisis diario)
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_MAPA_CALOR_VALORES", "mapa_calor_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_MEDIAS_VALORES", "grafica_medias_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES", "grafica_coeficientes_variacion_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_SUMAS_VALORES", "grafica_sumas_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES_MEDIAS_MAXIMOS_MINIMOS", "grafica_valores_medias_maximos_minimos");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_MAXIMOS_MINIMOS_MEDIAS_MEDIDAS", "tabla_maximos_minimos_medias_medidas");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_VALORES_DIA", "tabla_valores_dia");

    // Elementos de informe (Análisis de comportamiento)
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_COEFICIENTES_ESTABILIDAD_VALORES", "grafica_coeficientes_estabilidad_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_COEFICIENTE_ESTABILIDAD", "texto_explicacion_coeficiente_estabilidad");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_AMPLITUDES_VALORES", "grafica_amplitudes_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_AMPLITUD", "texto_explicacion_amplitud");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_ALTURAS_RELATIVAS_VALORES_MAXIMOS", "grafica_alturas_relativas_valores_maximos");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_ALTURA_RELATIVA_MAXIMA", "texto_explicacion_altura_relativa_maxima");

    // Elementos de informe (Comparación de periodos)
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_TABLA_EVOLUCION_VALORES", "tabla_evolucion_valores");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS", "grafica_diferencias");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS_ACUMULADAS", "grafica_diferencias_acumuladas");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_MAPA_CALOR_DIFERENCIAS", "mapa_calor_diferencias");

    // Elementos de informe (Comparación con perfil horario)
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS", "grafica_diferencias");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS_ACUMULADAS", "grafica_diferencias_acumuladas");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES_PERFIL_HORARIO", "grafica_valores_perfil_horario");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_MAPA_CALOR_DIFERENCIAS", "mapa_calor_diferencias");

    // Elementos de informe (Comparación de campos iguales)
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TABLA_DIFERENCIAS_VALORES", "tabla_diferencias_valores");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_DIFERENCIAS", "grafica_diferencias");
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_MAPAS_CALOR_DIFERENCIAS", "mapas_calor_diferencias");

    // Elementos de informe (Comparación de campos diferentes)
    define("ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_GRAFICA_VALORES", "grafica_valores");

    // Elementos de informe (Análisis comparativo)
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES_ACUMULADOS", "grafica_valores_acumulados");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_MAXIMOS_MINIMOS", "tabla_valores_maximos_minimos");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_PARETO", "grafica_pareto");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_PARETO", "tabla_valores_pareto");
    define("ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_MAPA_CALOR_DIFERENCIAS", "mapa_calor_diferencias");

    // Elementos de informe (Valores generales)
    define("ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES_ACUMULADOS", "grafica_valores_acumulados");
    define("ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_TABLA_VALORES_MAXIMOS_MINIMOS", "tabla_valores_maximos_minimos");

    // Elementos de informe (Incrementos totales)
    define("ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_TOTALES", "grafica_incrementos_totales");
    define("ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_PORCENTAJES_INCREMENTOS", "grafica_porcentajes_incrementos");
    define("ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS", "grafica_incrementos");
    define("ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_ACUMULADOS", "grafica_incrementos_acumulados");
    define("ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_TABLA_INCREMENTOS", "tabla_incrementos");

    // Elementos de informe (Histograma)
    define("ELEMENTO_INFORME_SENSORES_HISTOGRAMA_GRAFICA_HISTOGRAMA", "grafica_histograma");
    define("ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_MEDIDAS_ESTADISTICAS", "tabla_medidas_estadisticas");
    define("ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_PERCENTILES", "tabla_percentiles");

    // Elementos de informe (Correlación)
    define("ELEMENTO_INFORME_SENSORES_CORRELACION_GRAFICA_CORRELACION", "grafica_correlacion");
    define("ELEMENTO_INFORME_SENSORES_CORRELACION_TABLA_FUNCION_CORRELACION", "tabla_funcion_correlacion");


    //
    // Módulo Actuadores
    //


    // Clases de actuadores
    define("CLASE_ACTUADOR_MENSAJE", "MENSAJE");
    define("CLASE_ACTUADOR_INTERRUPTOR", "INTERRUPTOR");
    define("CLASE_ACTUADOR_TELEPOSTE", "TELEPOSTE");
    define("CLASE_ACTUADOR_LUZ_GRADUAL_4", "LUZ_GRADUAL_4");
    define("CLASE_ACTUADOR_GENERICA", "GENERICA");

    // Tipos de actuadores
    define("TIPO_ACTUADOR_HARDWARE", "HARDWARE");
    define("TIPO_ACTUADOR_SOFTWARE", "SOFTWARE");
    define("TIPO_ACTUADOR_TODOS", "TODOS");

    // Estados de actuador (para el filtrado)
    define("ESTADO_ACTUADOR_TODOS", "TODOS");
    define("ESTADO_ACTUADOR_OK", "OK");
    define("ESTADO_ACTUADOR_ERROR", "ERROR");
    define("ESTADO_ACTUADOR_ERROR_EJECUCION_ACCION", "ERROR_EJECUCION_ACCION");
    define("ESTADO_ACTUADOR_NO_CONECTADO", "NO_CONECTADO");
    define("ESTADO_ACTUADOR_EN_EJECUCION", "EN_EJECUCION");
    define("ESTADO_ACTUADOR_SIN_ACCION", "SIN_ACCION");

    // Habilitaciones de reglas
    define("HABILITACION_REGLA_TODAS", "TODAS");
    define("HABILITACION_REGLA_HABILITADA", "HABILITADA");
    define("HABILITACION_REGLA_DESHABILITADA", "DESHABILITADA");

    // Activaciones de reglas
    define("ACTIVACION_REGLA_TODOS", "TODAS");
    define("ACTIVACION_REGLA_ACTIVADA", "ACTIVADA");
    define("ACTIVACION_REGLA_DESACTIVADA", "DESACTIVADA");

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_ACTUADORES", 5);
    define("NUMERO_COLUMNAS_TABLA_PROGRAMACIONES", 2);
    define("NUMERO_COLUMNAS_TABLA_ACCIONES_PROGRAMACIONES", 4);
    define("NUMERO_COLUMNAS_TABLA_EXCEPCIONES_PROGRAMACIONES", 3);
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_REGLAS", 1);
    define("NUMERO_COLUMNAS_TABLA_REGLAS", 5);
    define("NUMERO_COLUMNAS_TABLA_SUCESOS_REGLAS", 5);
    define("NUMERO_COLUMNAS_TABLA_ACCIONES_REGLAS", 3);
    define("NUMERO_COLUMNAS_TABLA_HISTORICO_REGLAS", 4);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_ACCIONES_PROGRAMACIONES", serialize(array(35, 15, 35, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_REGLAS", serialize(array(35, 20, 20, 15, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SUCESOS_REGLAS", serialize(array(20, 25, 30, 15, 10)));

    // Periodos por defecto para las fechas iniciales de los informes
    define("PERIODO_DEFECTO_ACTUADORES_HISTORICO_REGLAS", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS", PERIODO_DIA_INICIO_HOY);

    // Destinos de acciones
    define("DESTINO_ACCION_ACTUADOR", "actuador");
    define("DESTINO_ACCION_GRUPO_ACTUADORES", "grupo_actuadores");

    // Orígenes de ultimas acciones
    define("ORIGEN_ULTIMA_ACCION_PROGRAMACION", "programacion");
    define("ORIGEN_ULTIMA_ACCION_GRUPO_ACTUADORES", "grupo_actuadores");

    // Tipos de acciones de los actuadores
    define("TIPO_ACCIONES_VALORES_UNICOS", "UNICO");
    define("TIPO_ACCIONES_VALORES_INICIAL_FINAL", "INICIAL_FINAL");
    define("TIPO_ACCIONES_VALORES_FIJOS_GRADUALES", "FIJOS_GRADUALES");

    // Tipos de excepciones de las programaciones
    define("TIPO_EXCEPCION_PROGRAMACION_FECHA", "FECHA");
    define("TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS", "RANGO_FECHAS");
    define("TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO", "DIA_ANYO");
    define("TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO", "RANGO_DIAS_ANYO");
    define("TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA", "DIA_SEMANA");

    // Tipos de valores fijos o graduales
    define("TIPO_VALORES_FIJOS", "F");
    define("TIPO_VALORES_GRADUALES", "G");

    // Tipos de reglas
    define("TIPO_REGLA_UNICA", "UNICA");
    define("TIPO_REGLA_MULTIPLE", "MULTIPLE");

    // Modos de activaciones de reglas
    define("MODO_ACTIVACION_REGLA_CUALQUIER_SUCESO", "CUALQUIER_SUCESO");
    define("MODO_ACTIVACION_REGLA_TODOS_SUCESOS", "TODOS_SUCESOS");

    // Número de activaciones de todos los sensores del grupo
    define("NUMERO_ACTIVACIONES_SUCESO_TODOS_SENSORES_GRUPO", -1);

    // Causas de sucesos de reglas
    define("CAUSA_SUCESO_EVENTO", "evento");
    define("CAUSA_SUCESO_REGLA", "regla");
    define("CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR", "timeout_envio_sensor");

    // Orígenes de sucesos de reglas
    define("ORIGEN_SUCESO_SENSOR", "sensor");
    define("ORIGEN_SUCESO_GRUPO_SENSORES", "grupo_sensores");

    // Modos de activación de sucesos de reglas
    define("MODO_ACTIVACION_SUCESO_NORMAL", "normal");
    define("MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO", "tiempo_minimo");
    define("MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO", "repeticiones_minimas_periodo_tiempo");

    // Tipo de acciones de reglas
    define("TIPO_ACCION_ACTIVACION", "activacion");
    define("TIPO_ACCION_DESACTIVACION", "desactivacion");

    // Causas de activación y desactivación de las reglas
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_INICIALIZACION", "inicializacion");
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_ADICION", "adicion");
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_MODIFICACION", "modificacion");
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_ELIMINACION", "eliminacion");
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_CONFIGURACION", "configuracion");
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_SUCESO", "suceso");
    define("CAUSA_ACTIVACION_DESACTIVACION_REGLA_HABILITACION", "habilitacion");

    // Causas de ejecución de acciones
    define("CAUSA_EJECUCION_ACCION_TODAS", "todas");
    define("CAUSA_EJECUCION_ACCION_SUCESO", "suceso");
    define("CAUSA_EJECUCION_ACCION_NO_SUCESO", "no_suceso");

    // Clases de interfaces de actuadores
    define("CLASE_INTERFAZ_ACTUADOR_PWM", "PWM");
    define("CLASE_INTERFAZ_ACTUADOR_SIMULADO", "SIMULADO");
    define("CLASE_INTERFAZ_ACTUADOR_EMAIL", "EMAIL");
    define("CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE", "MODBUS_SERIE");
    define("CLASE_INTERFAZ_ACTUADOR_MODBUS_IP", "MODBUS_IP");

    // Estados de ejecución de acciones
    define("ESTADO_EJECUCION_ACCION_NO_CONECTADO", -2);
    define("ESTADO_EJECUCION_ACCION_EN_EJECUCION", -1);
    define("ESTADO_EJECUCION_ACCION_ERROR", 0);
    define("ESTADO_EJECUCION_ACCION_OK", 1);

    // Errores en ejecución de acciones e-mail
    define("ERROR_EJECUCION_ACCION_EMAIL_ERROR_CREACION_CLIENTE_SMTP", "ERROR_CREACION_CLIENTE_SMTP");
    define("ERROR_EJECUCION_ACCION_EMAIL_ERROR_AUTENTICACION_CLIENTE_SMTP", "ERROR_AUTENTICACION_CLIENTE_SMTP");
    define("ERROR_EJECUCION_ACCION_EMAIL_ERROR_ENVIO_MENSAJE_SERVIDOR_SMTP", "ERROR_ENVIO_MENSAJE_SERVIDOR_SMTP");

    // Errores en ejecución de acciones Modbus IP
    define("ERROR_EJECUCION_ACCION_MODBUS_IP_ERROR_APERTURA_SOCKET", "ERROR_APERTURA_SOCKET");
    define("ERROR_EJECUCION_ACCION_MODBUS_IP_ERROR_ESCRITURA_VALORES", "ERROR_ESCRITURA_VALORES");

    // Orígenes de acciones de actuadores
    define("ORIGEN_ACCION_MANUAL", "MANUAL");
    define("ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_PROGRAMACION", "ULTIMA_ACCION_PROGRAMACION");
    define("ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_GRUPO_ACTUADORES", "ULTIMA_ACCION_GRUPO_ACTUADORES");
    define("ORIGEN_ACCION_AUTOMATICO_REENVIO_ULTIMA_ACCION", "REENVIO_ULTIMA_ACCION");
    define("ORIGEN_ACCION_AUTOMATICO_REGLA_ACTIVADA", "REGLA_ACTIVADA");
    define("ORIGEN_ACCION_AUTOMATICO_REGLA_DESACTIVADA", "REGLA_DESACTIVADA");
    define("ORIGEN_ACCION_AUTOMATICO_PROGRAMACION", "PROGRAMACION");

    // Orígenes de acciones de actuadores (para el informe de acciones enviadas)
    define("ORIGEN_ACCIONES_TODOS", "TODOS");
    define("ORIGEN_ACCIONES_MANUAL", "MANUAL");
    define("ORIGEN_ACCIONES_ULTIMA_ACCION", "ULTIMA_ACCION");
    define("ORIGEN_ACCIONES_REGLA", "REGLA");
    define("ORIGEN_ACCIONES_PROGRAMACION", "PROGRAMACION");

    // Orígenes de controles acciones
    define("ORIGEN_CONTROLES_ACCION_ENVIO_ACCION", "ENVIO_ACCION");
    define("ORIGEN_CONTROLES_ACCION_PROGRAMACION", "PROGRAMACION");
    define("ORIGEN_CONTROLES_ACCION_REGLA", "REGLA");

    // Anchuras de columnas de filtros de actuadores
    define("ANCHURAS_COLUMNAS_FILTRO_ACTUADORES_TABLA", serialize(array(18, -1, -1, -1, 15, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_GRUPOS_ACTUADORES_TABLA", serialize(array(18, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_REGLAS_TABLA", serialize(array(18, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_HISTORICO_REGLAS", serialize(array(18, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_PROGRAMACIONES_TABLA", serialize(array(18, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_ACTUADORES_MAPA", serialize(array(15, -1, -1, -1, 15, -1)));

    // Número de columnas de parámetros de informes
    define("NUMERO_COLUMNAS_PARAMETROS_ACCIONES_ENVIADAS", 4);

    // Anchuras de columnas de parámetros de informes
    define("ANCHURAS_COLUMNAS_PARAMETROS_FILTRO_ACCIONES_ACCIONES_ENVIADAS", serialize(array(-1, -1, 35, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_ACCIONES_ENVIADAS", serialize(array(-1, 35, -1, 10)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ACCIONES_ENVIADAS", serialize(array(-1, -1, -1, -1, -1, -1, -1)));

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_ACCIONES_ENVIADAS", 5);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_ACCIONES_ENVIADAS", serialize(array(15, 25, 20, 25, 15)));

    // Tipos de informes
    define("TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS", "ACTUADORES_ACCIONES_ENVIADAS");

    // Número máximo de caracteres de tooltips de mensajes
    define("NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE_TOOLTIP", 100);
    define("NUMERO_MAXIMO_CARACTERES_CONTENIDO_MENSAJE_TOOLTIP", 100);

    // Número máximo de caracteres de tablas de mensajes
    define("NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE_TABLA", 25);

    // Orígenes de envio de acciones
    define("ORIGEN_ENVIO_ACCION_HERRAMIENTAS_ACTUADORES", "herramientas_actuadores");
    define("ORIGEN_ENVIO_ACCION_DETALLES_TABLA_ACTUADORES", "detalles_tabla_actuadores");
    define("ORIGEN_ENVIO_ACCION_DETALLES_TABLA_GRUPOS_ACTUADORES", "detalles_tabla_grupos_actuadores");
    define("ORIGEN_ENVIO_ACCION_MAPA", "mapa_geografico");

    // Números máximos de filas de tablas
    define("NUMERO_MAXIMO_FILAS_TABLA_ACCIONES_ENVIADAS", 100);

    // Elementos de informes del módulo

    // Elementos de informe (Acciones enviadas)
    define("ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_SENSOR", "grafica_valores_sensor");
    define("ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_ACUMULADOS_SENSOR", "grafica_valores_acumulados_sensor");
    define("ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_ACCIONES_ENVIADAS", "grafica_acciones_enviadas");
    define("ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESCRIPCION_DESTINO", "descripcion_destino");
    define("ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_ACCIONES_ENVIADAS", "tabla_acciones_enviadas");
    define("ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_COMENTARIOS", "tabla_comentarios");


    //
    // Módulo Smartmeter
    //


    // Tablas de electricidad de España
    define("TABLA_TARIFAS_ELECTRICAS_ESPANYA", "tarifas_electricas_Espanya");
    define("TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA", "grupos_tarifas_electricas_Espanya");
    define("TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA", "tramos_tarifas_electricas_Espanya");
    define("TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA", "periodos_calculo_costes_pass_pool_tarifas_electricas_Espanya");
    define("TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA", "conceptos_coste_pass_through_tarifas_electricas_Espanya");
    define("TABLA_CONCEPTOS_COSTE_CIERRE_TARIFAS_ELECTRICAS_ESPANYA", "conceptos_coste_cierre_tarifas_electricas_Espanya");
    define("TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_ESPANYA", "conceptos_adicionales_factura_tarifas_electricas_Espanya");
    define("TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA", "validaciones_facturas_electricas_Espanya");
    define("TABLA_VALORES_INDICADORES_1_ENERGIA_ELECTRICA_ESPANYA", "valores_indicadores_1_energia_electrica_Espanya");
    define("TABLA_VALORES_INDICADORES_2_ENERGIA_ELECTRICA_ESPANYA", "valores_indicadores_2_energia_electrica_Espanya");
    define("TABLA_COEFICIENTES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA", "coeficientes_perdidas_energia_electrica_Espanya");
    define("TABLA_VALORES_COMPENSACION_GAS_AJOM_ESPANYA", "valores_compensacion_gas");
    define("TABLA_VALORES_COMPENSACION_GAS_MAJ3_ESPANYA", "valores_compensacion_gas_maj3");
    define("TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA", "valores_perdidas_energia_electrica_Espanya");
    define("TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA_2021", "valores_perdidas_energia_electrica_Espanya_2021");
    define("TABLA_VALORES_PVPC_ENERGIA_ELECTRICA_ESPANYA", "valores_pvpc_energia_electrica_Espanya");
    define("TABLA_VALORES_DESVIOS_ENERGIA_ELECTRICA_ESPANYA", "valores_desvios_energia_electrica_Espanya");

    // Tablas de electricidad de Portugal
    define("TABLA_TARIFAS_ELECTRICAS_PORTUGAL", "tarifas_electricas_Portugal");
    define("TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL", "grupos_tarifas_electricas_Portugal");
    define("TABLA_TRAMOS_TARIFAS_ELECTRICAS_PORTUGAL", "tramos_tarifas_electricas_Portugal");
    define("TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_PORTUGAL", "conceptos_adicionales_factura_tarifas_electricas_Portugal");


    // Tablas de tarifas de gas
    define("TABLA_TARIFAS_GAS_ESPANYA", "tarifas_gas_Espanya");
    define("TABLA_GRUPOS_TARIFAS_GAS_ESPANYA", "grupos_tarifas_gas_Espanya");
    define("TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_GAS_ESPANYA", "conceptos_adicionales_factura_tarifas_gas_Espanya");

    // Tablas de tarifas de gas
    define("TABLA_TARIFAS_AGUA_ESPANYA", "tarifas_agua_Espanya");
    define("TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA", "grupos_tarifas_agua_Espanya");
    define("TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_AGUA_ESPANYA", "conceptos_adicionales_factura_tarifas_agua_Espanya");

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_CONSUMOS_COSTES_MAXIMOS_MINIMOS", 5);
    define("NUMERO_COLUMNAS_TABLA_PRECIOS_MAXIMOS_MINIMOS", 4);
    define("NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_TRAMOS_EXCESOS_ENERGIA_REACTIVA", 5);
    define("NUMERO_COLUMNAS_TABLA_CORTES_TENSION", 1);
    define("NUMERO_COLUMNAS_TABLA_CONSUMOS_CONSUMOS_COSTES_TOTALES", 3);
    define("NUMERO_COLUMNAS_TABLA_COSTES_CONSUMOS_COSTES_TOTALES", 4);
    define("NUMERO_COLUMNAS_TABLA_CONSUMOS_COSTES_TRAMOS", 5);
    define("NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA", 6);
    define("NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA", 7);
    define("NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA", 7);
    define("NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA_PRORRATEO", 7);
    define("NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA_PRORRATEO", 8);
    define("NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA_PRORRATEO", 8);
    define("NUMERO_COLUMNAS_TABLA_SOBRECAUDAL_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_SELECCION_PERIODOS_POSTERIOR_ANTERIOR", 3);
    define("NUMERO_COLUMNAS_TABLA_EVOLUCION_CONSUMOS_COSTES", 4);
    define("NUMERO_COLUMNAS_TABLA_EVOLUCION_CONSUMOS_TRAMOS", 5);
    define("NUMERO_COLUMNAS_TABLA_EVOLUCION_PRECIOS_MEDIOS", 2);
    define("NUMERO_COLUMNAS_TABLA_COMPARACION_COSTE_ACTUAL_SIMULADOR_TARIFAS", 2);
    define("NUMERO_COLUMNAS_TABLA_COMPARACION_MEJOR_OPCION_SIMULADOR_TARIFAS", 2);
    define("NUMERO_COLUMNAS_TABLA_CONSUMOS_SIMULADOR_AUTOCONSUMO", 4);
    define("NUMERO_COLUMNAS_TABLA_COSTES_SIMULADOR_AUTOCONSUMO", 3);
    define("NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_TRAMOS_SIMULADOR_BATERIA_CONDENSADORES", 8);
    define("NUMERO_COLUMNAS_TABLA_POTENCIAS_OPTIMAS_TRAMOS", 6);
    define("NUMERO_COLUMNAS_TABLA_POTENCIAS_SELECCIONADAS_TRAMOS", 6);
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_COMPRA_ENERGIA", 2);
    define("NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_OPTIMO", 5);
    define("NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_OPTIMO_TARIFAS_2021", 9);
    define("NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_SELECCIONADO", 5);
    define("NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_SELECCIONADO_TARIFAS_2021", 9);
    define("NUMERO_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_ELECTRICA_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_ENERGIA_ACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_MAXIMOS_MENSUALES_ESPANYA", 5);
    define("NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_CUARTOHORARIOS_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_SIN_EXCESOS_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_PORTUGAL", 5);
    define("NUMERO_COLUMNAS_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA_SENSOR_TARIFA_ELECTRICA_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_CONSUMOS_DESVIOS_TOTALES_COMPRA_ENERGIA", 4);
    define("NUMERO_COLUMNAS_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES_COMPRA_ENERGIA", 4);
    define("NUMERO_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_ELECTRICA_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_SIN_EXCESOS_ESPANYA", 2);
    define("NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_2021_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_POR_CLIENTE", 3);
    define("NUMERO_COLUMNAS_TABLA_CAPACIDAD_DEMANDADA_TARIFA_GAS_TARIFAS_2021_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_GAS_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_AGUA_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_CONSUMO_SENSOR_TARIFA_AGUA_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_AGUA_ESPANYA", 3);
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_VALIDACIONES_FACTURAS", 1);
    define("NUMERO_COLUMNAS_TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA", 5);
    define("NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_TARIFAS", 2);
    define("NUMERO_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_ESPANYA", 5);
    define("NUMERO_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_PORTUGAL", 5);
    define("NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO", 5);
    define("NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO_PORTUGAL", 3);
    define("NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_POOL", 5);
    define("NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_THROUGH", 4);
    define("NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_CIERRE", 4);
    define("NUMERO_COLUMNAS_TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL", 2);
    define("NUMERO_COLUMNAS_TABLA_CONCEPTOS_COSTE_PASS_THROUGH", 2);
    define("NUMERO_COLUMNAS_TABLA_CONCEPTOS_COSTE_CIERRE", 2);
    define("NUMERO_COLUMNAS_TABLA_TARIFAS_GAS_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES", 4);
    define("NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TERMINO_FIJO_SIN_EXCESOS", 3);
    define("NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TARIFAS_2021", 5);
    define("NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TF_POR_CLIENTE", 3);
    define("NUMERO_COLUMNAS_TABLA_TARIFAS_AGUA_ESPANYA", 4);
    define("NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_AGUA", 2);
    define("NUMERO_COLUMNAS_TABLA_GRUPOS_TARIFAS", 2);
    define("NUMERO_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_SIN_IMPUESTO", 3);
    define("NUMERO_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_CON_IMPUESTO", 4);
    define("NUMERO_COLUMNAS_TABLA_REPARTO_COSTES_SIMULADOR_FACTURA", 3);
    define("NUMERO_COLUMNAS_TABLA_DATOS_FACTURA", 2);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_CONSUMOS_COSTES_MAXIMOS_MINIMOS", serialize(array(20, 25, 25, 15, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA", serialize(array(10, 15, 20, 25, 20, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA", serialize(array(10, 10, 15, 25, 20, 10, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA", serialize(array(10, 10, 15, 25, 20, 10, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA_PRORRATEO", serialize(array(10, 10, 15, 25, 20, 10, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA_PRORRATEO", serialize(array(10, 10, 10, 20, 20, 10, 10, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA_PRORRATEO", serialize(array(10, 10, 10, 20, 20, 10, 10, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_SOBRECAUDAL_ESPANYA", serialize(array(30, 40, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_EVOLUCION_CONSUMOS_TRAMOS", serialize(array(11, 22, 22, 22, 22)));
    define("ANCHURAS_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_ELECTRICA_ESPANYA", serialize(array(35, 25, 20, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_ENERGIA_ACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA", serialize(array(15, 60, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_MAXIMOS_MENSUALES_ESPANYA", serialize(array(15, 15, 15, 30, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_CUARTOHORARIOS_ESPANYA", serialize(array(15, 60, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_SIN_EXCESOS_ESPANYA", serialize(array(15, 60, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_PORTUGAL", serialize(array(15, 15, 15, 30, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA_SENSOR_TARIFA_ELECTRICA_ESPANYA", serialize(array(15, 25, 35, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_ENERGIA_REACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA", serialize(array(15, 20, 40, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_CONSUMOS_DESVIOS_TOTALES_COMPRA_ENERGIA", serialize(array(25, 25, 25, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES_COMPRA_ENERGIA", serialize(array(25, 25, 25, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_ELECTRICA_ESPANYA", serialize(array(15, 60, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA", serialize(array(25, 25, 25, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA", serialize(array(35, 35, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA", serialize(array(20, 20, 40, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_SIN_EXCESOS_ESPANYA", serialize(array(50, 50)));
    define("ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_2021_ESPANYA", serialize(array(35, 35, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_2021_ESPANYA", serialize(array(35, 35, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_CAPACIDAD_DEMANDADA_TARIFA_GAS_TARIFAS_2021_ESPANYA", serialize(array(35, 35, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_GAS_ESPANYA", serialize(array(15, 60, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_AGUA_ESPANYA", serialize(array(25, 25, 25, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_CONSUMO_SENSOR_TARIFA_AGUA_ESPANYA", serialize(array(35, 35, 30)));
    define("ANCHURAS_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_AGUA_ESPANYA", serialize(array(15, 60, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA", serialize(array(20, 40, 10, 10, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_ESPANYA", serialize(array(30, 25, 15, 20, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_PORTUGAL", serialize(array(30, 25, 15, 20, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO", serialize(array(15, 25, 20, 20, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO_PORTUGAL", serialize(array(20, 40, 40)));
    define("ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_POOL", serialize(array(15, 25, 20, 20, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_THROUGH", serialize(array(15, 30, 30, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_CIERRE", serialize(array(15, 30, 30, 25)));
    define("ANCHURAS_COLUMNAS_TABLA_TARIFAS_GAS_ESPANYA", serialize(array(30, 25, 30, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_TARIFAS_AGUA_ESPANYA", serialize(array(30, 25, 30, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_AGUA", serialize(array(50, 50)));
    define("ANCHURAS_COLUMNAS_TABLA_GRUPOS_TARIFAS", serialize(array(60, 40)));
    define("ANCHURAS_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_SIN_IMPUESTO", serialize(array(40, 20, 40)));
    define("ANCHURAS_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_CON_IMPUESTO", serialize(array(35, 20, 35, 10)));
    define("ANCHURAS_COLUMNAS_TABLA_REPARTO_COSTES_SIMULADOR_FACTURA", serialize(array(40, 30, 30)));

    // Número de columnas en controles de informes
    define("NUMERO_COLUMNAS_POTENCIAS_SIMULADOR_POTENCIAS", 3);

    // Número máximo de sensores seleccionados en las listas de selección de sensores
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_CONSUMOS_COSTES_GENERALES", 100);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_CONSUMOS_COSTES_TOTALES", 100);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_REPARTO_COSTES_SIMULADOR_FACTURA", 25);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_MAPA_CONSUMOS_COSTES", -1);
    define("MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ASIGNACION_TARIFA_SENSORES", -1);

    // Número máximo de sensores para el dibujado de gráficas
    define("NUMERO_MAXIMO_SENSORES_GRAFICAS_CONSUMOS_COSTES_GENERALES", 10);

    // Expiraciones de tarifas eléctricas
    define("EXPIRACION_TARIFA_NINGUNO", "NINGUNO");
    define("EXPIRACION_TARIFA_SI", "SI");
    define("EXPIRACION_TARIFA_NO", "NO");

    // Prorrateo de excesos de potencia de tarifas eléctricas    
    define("PRORRATEO_TARIFA_SI", "SI");
    define("PRORRATEO_TARIFA_NO", "NO");

    // Tipos de tarifas
    define("TIPO_TARIFA_NINGUNO", "NINGUNO");
    define("TIPO_TARIFA_TODOS", "TODOS");
    
    // Tipos de tarifas eléctricas (vigentes a partir de  2026)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026", "ES_20TD_P_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026", "ES_20TD_B_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026", "ES_20TD_C_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026", "ES_20TD_CE_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026", "ES_20TD_ME_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026", "ES_30TD_P_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026", "ES_30TD_B_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026", "ES_30TD_C_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026", "ES_30TD_CE_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026", "ES_30TD_ME_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX", "ES_30TD_P_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX", "ES_30TD_B_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX", "ES_30TD_C_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX", "ES_30TD_CE_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX", "ES_30TD_ME_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026", "ES_61TD_P_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026", "ES_61TD_B_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026", "ES_61TD_C_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026", "ES_61TD_CE_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026", "ES_61TD_ME_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX", "ES_61TD_P_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX", "ES_61TD_B_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX", "ES_61TD_C_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX", "ES_61TD_CE_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX", "ES_61TD_ME_2026_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026", "ES_62TD_P_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026", "ES_62TD_B_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026", "ES_62TD_C_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026", "ES_62TD_CE_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026", "ES_62TD_ME_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026", "ES_63TD_P_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026", "ES_63TD_B_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026", "ES_63TD_C_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026", "ES_63TD_CE_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026", "ES_63TD_ME_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026", "ES_64TD_P_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026", "ES_64TD_B_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026", "ES_64TD_C_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026", "ES_64TD_CE_2026");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026", "ES_64TD_ME_2026");
    
    // Tipos de tarifas eléctricas (vigentes a partir de  Abril de 2025)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL", "ES_20TD_P_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL", "ES_20TD_B_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL", "ES_20TD_C_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL", "ES_20TD_CE_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL", "ES_20TD_ME_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL", "ES_30TD_P_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL", "ES_30TD_B_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL", "ES_30TD_C_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL", "ES_30TD_CE_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL", "ES_30TD_ME_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX", "ES_30TD_P_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX", "ES_30TD_B_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX", "ES_30TD_C_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX", "ES_30TD_CE_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX", "ES_30TD_ME_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL", "ES_61TD_P_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL", "ES_61TD_B_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL", "ES_61TD_C_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL", "ES_61TD_CE_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL", "ES_61TD_ME_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX", "ES_61TD_P_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX", "ES_61TD_B_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX", "ES_61TD_C_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX", "ES_61TD_CE_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX", "ES_61TD_ME_2025_ABRIL_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL", "ES_62TD_P_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL", "ES_62TD_B_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL", "ES_62TD_C_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL", "ES_62TD_CE_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL", "ES_62TD_ME_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL", "ES_63TD_P_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL", "ES_63TD_B_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL", "ES_63TD_C_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL", "ES_63TD_CE_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL", "ES_63TD_ME_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL", "ES_64TD_P_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL", "ES_64TD_B_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL", "ES_64TD_C_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL", "ES_64TD_CE_2025_ABRIL");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL", "ES_64TD_ME_2025_ABRIL");

    // Tipos de tarifas eléctricas (vigentes a partir de enero de 2025)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025", "ES_20TD_P_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025", "ES_20TD_B_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025", "ES_20TD_C_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025", "ES_20TD_CE_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025", "ES_20TD_ME_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025", "ES_30TD_P_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025", "ES_30TD_B_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025", "ES_30TD_C_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025", "ES_30TD_CE_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025", "ES_30TD_ME_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX", "ES_30TD_P_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX", "ES_30TD_B_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX", "ES_30TD_C_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX", "ES_30TD_CE_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX", "ES_30TD_ME_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025", "ES_61TD_P_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025", "ES_61TD_B_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025", "ES_61TD_C_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025", "ES_61TD_CE_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025", "ES_61TD_ME_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX", "ES_61TD_P_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX", "ES_61TD_B_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX", "ES_61TD_C_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX", "ES_61TD_CE_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX", "ES_61TD_ME_2025_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025", "ES_62TD_P_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025", "ES_62TD_B_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025", "ES_62TD_C_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025", "ES_62TD_CE_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025", "ES_62TD_ME_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025", "ES_63TD_P_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025", "ES_63TD_B_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025", "ES_63TD_C_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025", "ES_63TD_CE_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025", "ES_63TD_ME_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025", "ES_64TD_P_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025", "ES_64TD_B_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025", "ES_64TD_C_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025", "ES_64TD_CE_2025");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025", "ES_64TD_ME_2025");

    // Tipos de tarifas eléctricas (vigentes a partir de enero de 2024)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024", "ES_20TD_P_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024", "ES_20TD_B_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024", "ES_20TD_C_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024", "ES_20TD_CE_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024", "ES_20TD_ME_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024", "ES_30TD_P_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024", "ES_30TD_B_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024", "ES_30TD_C_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024", "ES_30TD_CE_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024", "ES_30TD_ME_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX", "ES_30TD_P_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX", "ES_30TD_B_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX", "ES_30TD_C_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX", "ES_30TD_CE_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX", "ES_30TD_ME_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024", "ES_61TD_P_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024", "ES_61TD_B_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024", "ES_61TD_C_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024", "ES_61TD_CE_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024", "ES_61TD_ME_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX", "ES_61TD_P_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX", "ES_61TD_B_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX", "ES_61TD_C_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX", "ES_61TD_CE_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX", "ES_61TD_ME_2024_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024", "ES_62TD_P_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024", "ES_62TD_B_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024", "ES_62TD_C_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024", "ES_62TD_CE_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024", "ES_62TD_ME_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024", "ES_63TD_P_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024", "ES_63TD_B_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024", "ES_63TD_C_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024", "ES_63TD_CE_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024", "ES_63TD_ME_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024", "ES_64TD_P_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024", "ES_64TD_B_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024", "ES_64TD_C_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024", "ES_64TD_CE_2024");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024", "ES_64TD_ME_2024");

    // Tipos de tarifas eléctricas (vigentes a partir de enero de 2023)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023", "ES_20TD_P_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023", "ES_20TD_B_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023", "ES_20TD_C_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023", "ES_20TD_CE_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023", "ES_20TD_ME_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023", "ES_30TD_P_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023", "ES_30TD_B_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023", "ES_30TD_C_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023", "ES_30TD_CE_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023", "ES_30TD_ME_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX", "ES_30TD_P_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX", "ES_30TD_B_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX", "ES_30TD_C_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX", "ES_30TD_CE_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX", "ES_30TD_ME_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023", "ES_61TD_P_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023", "ES_61TD_B_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023", "ES_61TD_C_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023", "ES_61TD_CE_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023", "ES_61TD_ME_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX", "ES_61TD_P_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX", "ES_61TD_B_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX", "ES_61TD_C_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX", "ES_61TD_CE_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX", "ES_61TD_ME_2023_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023", "ES_62TD_P_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023", "ES_62TD_B_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023", "ES_62TD_C_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023", "ES_62TD_CE_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023", "ES_62TD_ME_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023", "ES_63TD_P_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023", "ES_63TD_B_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023", "ES_63TD_C_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023", "ES_63TD_CE_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023", "ES_63TD_ME_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023", "ES_64TD_P_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023", "ES_64TD_B_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023", "ES_64TD_C_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023", "ES_64TD_CE_2023");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023", "ES_64TD_ME_2023");

    // Tipos de tarifas eléctricas (vigentes a partir de enero de 2022)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022", "ES_20TD_P_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022", "ES_20TD_B_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022", "ES_20TD_C_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022", "ES_20TD_CE_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022", "ES_20TD_ME_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022", "ES_30TD_P_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022", "ES_30TD_B_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022", "ES_30TD_C_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022", "ES_30TD_CE_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022", "ES_30TD_ME_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX", "ES_30TD_P_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX", "ES_30TD_B_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX", "ES_30TD_C_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX", "ES_30TD_CE_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX", "ES_30TD_ME_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022", "ES_61TD_P_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022", "ES_61TD_B_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022", "ES_61TD_C_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022", "ES_61TD_CE_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022", "ES_61TD_ME_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX", "ES_61TD_P_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX", "ES_61TD_B_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX", "ES_61TD_C_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX", "ES_61TD_CE_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX", "ES_61TD_ME_2022_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022", "ES_62TD_P_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022", "ES_62TD_B_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022", "ES_62TD_C_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022", "ES_62TD_CE_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022", "ES_62TD_ME_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022", "ES_63TD_P_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022", "ES_63TD_B_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022", "ES_63TD_C_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022", "ES_63TD_CE_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022", "ES_63TD_ME_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022", "ES_64TD_P_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022", "ES_64TD_B_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022", "ES_64TD_C_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022", "ES_64TD_CE_2022");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022", "ES_64TD_ME_2022");

    // Tipos de tarifas eléctricas (vigentes a partir de 2020)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P", "ES_20TD_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B", "ES_20TD_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C", "ES_20TD_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE", "ES_20TD_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME", "ES_20TD_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P", "ES_30TD_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B", "ES_30TD_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C", "ES_30TD_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE", "ES_30TD_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME", "ES_30TD_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX", "ES_30TD_P_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX", "ES_30TD_B_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX", "ES_30TD_C_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX", "ES_30TD_CE_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX", "ES_30TD_ME_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P", "ES_61TD_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B", "ES_61TD_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C", "ES_61TD_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE", "ES_61TD_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME", "ES_61TD_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX", "ES_61TD_P_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX", "ES_61TD_B_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX", "ES_61TD_C_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX", "ES_61TD_CE_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX", "ES_61TD_ME_MAX");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P", "ES_62TD_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B", "ES_62TD_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C", "ES_62TD_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE", "ES_62TD_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME", "ES_62TD_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P", "ES_63TD_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B", "ES_63TD_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C", "ES_63TD_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE", "ES_63TD_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME", "ES_63TD_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P", "ES_64TD_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B", "ES_64TD_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C", "ES_64TD_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE", "ES_64TD_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME", "ES_64TD_ME");

    // Tipos de tarifas eléctricas (obsoletas a partir de 2020)
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P", "ES_20DHA_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B", "ES_20DHA_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C", "ES_20DHA_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE", "ES_20DHA_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME", "ES_20DHA_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P_MAXIMETRO", "ES_20DHA_P_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B_MAXIMETRO", "ES_20DHA_B_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO", "ES_20DHA_C_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE_MAXIMETRO", "ES_20DHA_CE_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME_MAXIMETRO", "ES_20DHA_ME_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P", "ES_21DHA_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B", "ES_21DHA_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C", "ES_21DHA_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE", "ES_21DHA_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME", "ES_21DHA_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P_MAXIMETRO", "ES_21DHA_P_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B_MAXIMETRO", "ES_21DHA_B_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO", "ES_21DHA_C_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE_MAXIMETRO", "ES_21DHA_CE_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME_MAXIMETRO", "ES_21DHA_ME_MAXIMETRO");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30A_P", "ES_30A_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30A_B", "ES_30A_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C", "ES_30A_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30A_CE", "ES_30A_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_30A_ME", "ES_30A_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_31A_P", "ES_31A_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_31A_B", "ES_31A_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C", "ES_31A_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_31A_CE", "ES_31A_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_31A_ME", "ES_31A_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61A_P", "ES_61A_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61A_B", "ES_61A_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C", "ES_61A_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61A_CE", "ES_61A_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_61A_ME", "ES_61A_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62A_P", "ES_62A_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62A_B", "ES_62A_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C", "ES_62A_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62A_CE", "ES_62A_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_62A_ME", "ES_62A_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63A_P", "ES_63A_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63A_B", "ES_63A_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C", "ES_63A_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63A_CE", "ES_63A_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_63A_ME", "ES_63A_ME");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64A_P", "ES_64A_P");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64A_B", "ES_64A_B");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C", "ES_64A_C");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64A_CE", "ES_64A_CE");
    define("TIPO_TARIFA_ELECTRICA_ESPANYA_64A_ME", "ES_64A_ME");

    //Tipos de tarifas para la venta de energia "ELI"
    define("TIPO_TARIFA_ELECTRICA_VENTA_ENERGIA", "ES_VENTA");

    // Tipos de tarifas eléctricas Portugal
    define("TIPO_TARIFA_ELECTRICA_PORTUGAL_MAT", "MAT");
    define("TIPO_TARIFA_ELECTRICA_PORTUGAL_AT", "AT");
    define("TIPO_TARIFA_ELECTRICA_PORTUGAL_MT", "MT");
    define("TIPO_TARIFA_ELECTRICA_PORTUGAL_BTE", "BTE");
    define("TIPO_TARIFA_ELECTRICA_PORTUGAL_BTN", "BTN");

    // Ciclos de tarifas eléctricas Portugal
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_NINGUNO", "NINGUNO");
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_TODOS", "TODOS");
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_COM_FERIADOS", "SEMANAL_COM");
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_SEM_FERIADOS", "SEMANAL_SEM");
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_OPCIONAL", "SEMANAL_OPCIONAL");
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_DIARIO", "DIARIO");
    define("CICLO_TARIFA_ELECTRICA_PORTUGAL_DIARIO_OPCIONAL", "DIARIO_OPCIONAL");

    // Regiones tarifarias Portugal
    define("REGION_PORTUGAL_CONTINENTAL", "CONTINENTAL");
    define("REGION_PORTUGAL_AZORES", "AZORES");
    define("REGION_PORTUGAL_MADEIRA", "MADEIRA");


    // Tipos de tarifas de gas (España)
    define("TIPO_TARIFA_GAS_ESPANYA_1X", "ES_1X");
    define("TIPO_TARIFA_GAS_ESPANYA_2X", "ES_2X");
    define("TIPO_TARIFA_GAS_ESPANYA_3X", "ES_3X");
    define("TIPO_TARIFA_GAS_ESPANYA_35", "ES_35");
    define("TIPO_TARIFA_GAS_ESPANYA_4X", "ES_4X");

    // Tipos de tarifas de gas España 2021 - 2022
    // Facturadas por capacidad
    define("TIPO_TARIFA_GAS_ESPANYA_RL1", "ES_RL1");
    define("TIPO_TARIFA_GAS_ESPANYA_RL2", "ES_RL2");
    define("TIPO_TARIFA_GAS_ESPANYA_RL3", "ES_RL3");
    define("TIPO_TARIFA_GAS_ESPANYA_RL4", "ES_RL4");
    define("TIPO_TARIFA_GAS_ESPANYA_RL5", "ES_RL5");
    define("TIPO_TARIFA_GAS_ESPANYA_RL6", "ES_RL6");
    define("TIPO_TARIFA_GAS_ESPANYA_RL7", "ES_RL7");
    define("TIPO_TARIFA_GAS_ESPANYA_RL8", "ES_RL8");
    define("TIPO_TARIFA_GAS_ESPANYA_RL9", "ES_RL9");
    define("TIPO_TARIFA_GAS_ESPANYA_RL10", "ES_RL10");
    define("TIPO_TARIFA_GAS_ESPANYA_RL11", "ES_RL11");
    // Tarifas gas facturadas por cliente y no por capacidad
    define("TIPO_TARIFA_GAS_ESPANYA_RL1_C", "ES_RL1_C");
    define("TIPO_TARIFA_GAS_ESPANYA_RL2_C", "ES_RL2_C");
    define("TIPO_TARIFA_GAS_ESPANYA_RL3_C", "ES_RL3_C");
    define("TIPO_TARIFA_GAS_ESPANYA_RL4_C", "ES_RL4_C");
    define("TIPO_TARIFA_GAS_ESPANYA_RL5_C", "ES_RL5_C");
    define("TIPO_TARIFA_GAS_ESPANYA_RL6_C", "ES_RL6_C");

    // Tipos de tarifas de agua (España)
    define("TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_P_B_CE_ME", "ES_ESTANDAR_P_B_CE_ME");
    define("TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_C", "ES_ESTANDAR_C");

    // Bonificaciones de 85% de tarifas eléctricas
    define("BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA", "NINGUNA");
    define("BONIFICACION_85_TARIFA_ELECTRICA_SI", "SI");
    define("BONIFICACION_85_TARIFA_ELECTRICA_NO", "NO");
    define("BONIFICACION_85_TARIFA_ELECTRICA_MINIMO_100", "MINIMO_100");
    define("BONIFICACION_85_TARIFA_ELECTRICA_REAL", "POTENCIA_MEDIDA"); //ELI - Para facturar la potencia real sin tener en cuenta la contratada

    // Tipos de medida de tarifas eléctricas
    define("TIPO_MEDIDA_TARIFA_ELECTRICA_NINGUNA", "NINGUNA");
    define("TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION", "BAJA_TENSION");
    define("TIPO_MEDIDA_TARIFA_ELECTRICA_ALTA_TENSION", "ALTA_TENSION");

    // Contratos de tarifas eléctricas
    define("CONTRATO_TARIFA_ELECTRICA_NINGUNO", "NINGUNO");
    define("CONTRATO_TARIFA_ELECTRICA_TODOS", "TODOS");

    // Contratos de tarifas eléctricas (España)
    define("CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO", "FIJO");
    define("CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL", "PASS_POOL");
    define("CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH", "PASS_THROUGH");
    define("CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE", "CIERRE");

    // Tipos de días para las tarifas eléctricas
    define("TIPO_DIA_TARIFA_ELECTRICA_ESPANYA_6XTD_A", "A");
    define("TIPO_DIA_TARIFA_ELECTRICA_ESPANYA_6XTD_B", "B");
    define("TIPO_DIA_TARIFA_ELECTRICA_ESPANYA_6XTD_C", "C");
    define("TIPO_DIA_TARIFA_ELECTRICA_ESPANYA_6XTD_D", "D");

    // Ids de indicadores de OMIE
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO", "NINGUNO");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_PENINSULA", "IND_805");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_GRAN_CANARIA", "IND_1336_8795");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LANZAROTE_FUERTEVENTURA", "IND_1336_8796");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_TENERIFE", "IND_1336_8797");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LA_PALMA", "IND_1336_8798");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LA_GOMERA", "IND_1336_8799");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_EL_HIERRO", "IND_1336_8800");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_CEUTA", "IND_1336_8803");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_MELILLA", "IND_1336_8804");
    define("ID_INDICADOR_OMIE_TARIFA_ELECTRICA_BALEARES", "IND_1336_8823");

    // Tipos de cálculo de costes (de consumo) de tarifas eléctricas con contratos 'pass pool'
    define("TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO", "NINGUNO");
    define("TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_AUTOMATICO", "AUTOMATICO");
    define("TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_MANUAL", "MANUAL");

    // Tamaño y número de tarifas por defecto en las listas de selección de tarifas en la simulación de tarifas
    define("MAX_TARIFAS_SELECCIONADAS_DEFECTO_LISTA_TARIFAS_SIMULADOR_TARIFAS", 5);

    // Duración de periodos por defecto en el módulo Smartmeter
    define("DIAS_DURACION_DEFECTO_SMARTMETER_PERIODO", 7);

    // Periodos por defecto para las fechas iniciales de los informes del módulo Smartmeter
    define("PERIODO_DEFECTO_SMARTMETER_CONSUMOS_COSTES_GENERALES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_CONSUMOS_COSTES_TOTALES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_CONSUMOS_COSTES_TRAMOS", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_SIMULADOR_AUTOCONSUMO", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_EXCESOS_POTENCIA", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_CORTES_TENSION", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_CALCULO_BATERIA_CONDENSADORES", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_PREVISION_COMPRA_ENERGIA", PERIODO_DIA_INICIO_MANYANA_DURACION_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_PREVISION_COMPRA_ENERGIA_PERFIL_HORARIO", PERIODO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_DESVIOS_COMPRA_ENERGIA", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_EXCESOS_CAUDAL", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_MAPA_CONSUMOS_COSTES", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_SIMULADOR_TARIFAS", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_COMPARACION_PERIODOS", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_SMARTMETER_OPTIMIZADOR_POTENCIAS", PERIODO_ANYO);
    define("PERIODO_DEFECTO_SMARTMETER_SIMULADOR_POTENCIAS", PERIODO_ANYO);
    define("PERIODO_DEFECTO_SMARTMETER_OPTIMIZADOR_CAUDALES", PERIODO_ANYO);
    define("PERIODO_DEFECTO_SMARTMETER_SIMULADOR_CAUDALES", PERIODO_ANYO);
    define("PERIODO_DEFECTO_SMARTMETER_VALIDACIONES_FACTURAS", PERIODO_DIA_INICIO_HOY);
    define("PERIODO_DEFECTO_SMARTMETER_SIMULADOR_FACTURA", PERIODO_DIA_INICIO_MES);
    define("PERIODO_DEFECTO_SMARTMETER_INFORME_GENERAL", PERIODO_DIA_INICIO_SEMANA);

    // Número de columnas de parámetros de informes
    define("NUMERO_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_GENERALES", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TOTALES", 3);
    define("NUMEROS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TRAMOS", "2,3");
    define("NUMERO_COLUMNAS_PARAMETROS_EXCESOS_POTENCIA", 3);
    define("NUMEROS_COLUMNAS_PARAMETROS_EXCESOS_ENERGIA_REACTIVA", "2,3");
    define("NUMEROS_COLUMNAS_PARAMETROS_CORTES_TENSION", "2,3");
    define("NUMEROS_COLUMNAS_PARAMETROS_EXCESOS_CAUDAL", "2,3");
    define("NUMERO_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_CONSUMOS_COSTES", 4);
    define("NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_TARIFAS", 2);
    define("NUMERO_COLUMNAS_PARAMETROS_MAPA_CONSUMOS_COSTES", 2);
    define("NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_AUTOCONSUMO", 5);
    define("NUMERO_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_AUTOMATICO", 5);
    define("NUMEROS_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_MANUAL", "1,2");
    define("NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_AUTOMATICO", 5);
    define("NUMEROS_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_MANUAL", "1,2");
    define("NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_BATERIA_CONDENSADORES", 3);
    define("NUMEROS_COLUMNAS_PARAMETROS_PREVISION_COMPRA_ENERGIA", "2,4");
    define("NUMEROS_COLUMNAS_PARAMETROS_DESVIOS_COMPRA_ENERGIA", "2,3");
    define("NUMEROS_COLUMNAS_PARAMETROS_DESVIOS_PONDERADOS_COMPRA_ENERGIA", "2,3");
    define("NUMERO_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_AUTOMATICO", 3);
    define("NUMEROS_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_MANUAL", "1,2");
    define("NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_AUTOMATICO", 3);
    define("NUMEROS_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_MANUAL", "1,2");
    define("NUMEROS_COLUMNAS_PARAMETROS_SIMULADOR_FACTURAS", "2,3");
    define("NUMEROS_COLUMNAS_PARAMETROS_ESTUDIO_GENERAL", "2,3");

    // Anchuras de columnas de parámetros de informes
    // Nota: Puede no ser hasta el 100% ya que influye el padding, margins, etc de los controles ...
    define("ANCHURAS_COLUMNAS_PARAMETROS_TARIFAS_ELECTRICAS", serialize(array(35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_TARIFAS_GAS", serialize(array(35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_GENERALES", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TOTALES", serialize(array(-1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TRAMOS", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_AUTOCONSUMO", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_EXCESOS_POTENCIA", serialize(array(-1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_EXCESOS_ENERGIA_REACTIVA", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_CORTES_TENSION", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_EXCESOS_CAUDAL", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_CONSUMOS_COSTES", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_TARIFAS", serialize(array(-1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_MAPA_CONSUMOS_COSTES", serialize(array(-1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_TARIFA", serialize(array(35, 35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_SENSOR", serialize(array(35, 35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_SENSOR_TARIFA", serialize(array(30, 30, 25)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_AUTOMATICO", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_MANUAL", serialize(array(60, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_AUTOMATICO", serialize(array(-1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_MANUAL", serialize(array(60, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_BATERIA_CONDENSADORES", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_PREVISION_COMPRA_ENERGIA", serialize(array(-1, -1, -1, -1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_DESVIOS_COMPRA_ENERGIA", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_DESVIOS_PONDERADOS_COMPRA_ENERGIA", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_AUTOMATICO", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_MANUAL", serialize(array(60, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_AUTOMATICO", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_MANUAL", serialize(array(60, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_FACTURAS", serialize(array(-1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_ESTUDIO_GENERAL", serialize(array(-1, -1, -1, -1, -1)));

    // Anchuras de columnas de filtros
    define("ANCHURAS_COLUMNAS_FILTRO_VALIDACIONES_FACTURAS_ELECTRICIDAD_ESPANYA", serialize(array(18, -1, -1, -1)));

    // Opciones extra para añadir a las listas de tarifas
    define("OPCIONES_EXTRA_LISTA_TARIFAS_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA", "SIN_NINGUNA");
    define("OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO", "SIN_GRUPO");
    define("OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS", "TARIFA_VIGENTE_SEGUN_FECHAS");
    define("OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL", "ACTUAL");

    // Opciones extra para añadir a las listas de tipos de tarifa
    define("OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS", "TODOS");

    // Opciones extra para añadir a las listas de ciclos de tarifa
    define("OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_SIN_OPCIONES", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_TODOS", "TODOS");

    // Opciones extra para añadir a las listas de regiones de Portugal
    define("REGIONES_PORTUGAL_SIN_OPCIONES", "SIN_OPCIONES");
    define("REGIONES_PORTUGAL_NINGUNO", "NINGUNO");
    define("REGIONES_PORTUGAL_TODOS", "TODOS");

    // Opciones extra para añadir a las listas de tarifas eléctricas (España)
    define("OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES", "TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES");
    define("OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_CON_EXCESOS", "TIPO_CALCULO_COSTE_POTENCIAS_CON_EXCESOS");

    // Opciones extra para añadir a las listas de contratos de tarifa eléctrica
    define("OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_TODOS", "TODOS");

    // Opciones extra para añadir a las listas de bonificaciones 85 % de tarifa eléctrica
    define("OPCIONES_EXTRA_LISTA_BONIFICACION_85_TARIFA_ELECTRICA_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA", "NINGUNA");

    // Opciones extra para añadir a las listas de tipos de medida de tarifa eléctrica
    define("OPCIONES_EXTRA_LISTA_TIPOS_MEDIDA_TARIFA_ELECTRICA_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_TIPOS_MEDIDA_TARIFA_ELECTRICA_NINGUNA", "NINGUNA");

    // Opciones extra para añadir a las listas de tarifas de gas (España)
    define("OPCIONES_EXTRA_LISTA_TARIFAS_GAS_TIPO_CALCULO_COSTE_TERMINO_FIJO_CON_EXCESOS", "TIPO_CALCULO_COSTE_TERMINO_FIJO_CON_EXCESOS");

    // Opciones extra para añadir a las listas de grupos de tarifas
    define("OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO", "NINGUNO");
    define("OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS", "TODOS");
    define("OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS_NINGUNO", "TODOS_NINGUNO");

    // Número máximo de tramos de tarifas eléctricas
    define("NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA", 6);

    // Orígenes de importación de valores diarios
    define("ORIGEN_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR_HERRAMIENTAS", "HERRAMIENTAS");
    define("ORIGEN_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR_INFORME", "INFORME");

    // Tipos de informes
    define("TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES", "SMARTMETER_CONSUMOS_COSTES_GENERALES");
    define("TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES", "SMARTMETER_CONSUMOS_COSTES_TOTALES");
    define("TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS", "SMARTMETER_CONSUMOS_COSTES_TRAMOS");
    define("TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA", "SMARTMETER_EXCESOS_POTENCIA");
    define("TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA", "SMARTMETER_EXCESOS_ENERGIA_REACTIVA");
    define("TIPO_INFORME_SMARTMETER_CORTES_TENSION", "SMARTMETER_CORTES_TENSION");
    define("TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL", "SMARTMETER_EXCESOS_CAUDAL");
    define("TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS", "SMARTMETER_OPTIMIZADOR_POTENCIAS");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS", "SMARTMETER_SIMULADOR_POTENCIAS");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES", "SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES");
    define("TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES", "SMARTMETER_OPTIMIZADOR_CAUDALES");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES", "SMARTMETER_SIMULADOR_CAUDALES");
    define("TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS", "SMARTMETER_COMPARACION_PERIODOS");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS", "SMARTMETER_SIMULADOR_TARIFAS");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO", "SMARTMETER_SIMULADOR_AUTOCONSUMO");
    define("TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_AUTOMATICO", "SMARTMETER_OPTIMIZADOR_POTENCIAS_AUTOMATICO");
    define("TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_MANUAL", "SMARTMETER_OPTIMIZADOR_POTENCIAS_MANUAL");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_AUTOMATICO", "SMARTMETER_SIMULADOR_POTENCIAS_AUTOMATICO");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_MANUAL", "SMARTMETER_SIMULADOR_POTENCIAS_MANUAL");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES", "SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES");
    define("TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA", "SMARTMETER_PREVISION_COMPRA_ENERGIA");
    define("TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA", "SMARTMETER_DESVIOS_COMPRA_ENERGIA");
    define("TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA", "SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA");
    define("TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO", "SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO");
    define("TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL", "SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO", "SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_MANUAL", "SMARTMETER_SIMULADOR_CAUDALES_MANUAL");
    define("TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA", "SMARTMETER_SIMULADOR_FACTURA");
    define("TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL", "SMARTMETER_ESTUDIO_GENERAL");

    // Tipo de informe (especial porque es un mapa)
    define("TIPO_INFORME_SMARTMETER_MAPA_CONSUMOS_COSTES", "SMARTMETER_MAPA_CONSUMOS_COSTES");

    // Tipos de fichero de validaciones de facturas eléctricas
    define("TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CIERRE_FACTURACION_ENERGY_MINUS", "CIERRE_FACTURACION_ENERGY_MINUS");
    define("TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ATR_DISTRIBUIDORA_XML", "ATR_DISTRIBUIDORA_XML");
    define("TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_GEMWEB", "GEMWEB");

    // Cosenos de phi
    define("MINIMO_COSENO_PHI_PENALIZABLE_1", 0.95);
    define("MINIMO_COSENO_PHI_PENALIZABLE_2", 0.80);
    define("MINIMO_COSENO_PHI_PENALIZABLE_CAPACITIVA", 0.98);

    // Precios de exceso de energía reactiva
    define("PRECIO_EXCESO_ENERGIA_REACTIVA_1", 0.041554);
    define("PRECIO_EXCESO_ENERGIA_REACTIVA_2", 0.062332);
    define("PRECIO_EXCESO_ENERGIA_REACTIVA_CAPACITIVA", 0.05);

    // Número máximo de caracteres por fila al leer un fichero csv de potencias máximas
    define("NUMERO_MAXIMO_CARACTERES_FILA_FICHERO_CSV_POTENCIAS_MAXIMAS", 1000);

    // Separador de columnas de ficheros
    define("SEPARADOR_COLUMNAS_FICHERO_CSV_POTENCIAS_MAXIMAS", ";");
    define("SEPARADOR_COLUMNAS_FICHERO_CSV_VALORES_DIARIOS_COMPRA_ENERGIA", ";");
    define("SEPARADOR_COLUMNAS_FICHERO_CSV_CAUDALES_DIARIOS_MAXIMOS", ";");

    // Tipos de cálculo de costes de potencias de tarifas eléctricas
    define("TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO", "NINGUNO");
    define("TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES", "EXCESOS_MAXIMOS_MENSUALES");
    define("TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS", "EXCESOS_CUARTOHORARIOS");
    define("TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO", "EXCESOS_MAXIMETRO");
    define("TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS", "SIN_EXCESOS");

    // Tipos de cálculo de costes de termino fijo
    define("TIPO_CALCULO_COSTE_TERMINO_FIJO_NINGUNO", "NINGUNO");
    define("TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES", "EXCESOS_MAXIMOS_MENSUALES");
    define("TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS", "SIN_EXCESOS");
    define("TIPO_CALCULO_COSTE_TARIFAS_2021","TARIFAS_2021");
    define("TIPO_CALCULO_COSTE_POR_CLIENTE","TF_POR_CLIENTE");
    define("CONSTANTE_TARIFA_GAS_MULTIPLICADOR_DIARIO_2021", serialize(array(2.28,1.72,1.59,1.26,1.23,1.31,1.52,1.39,1.39,1.52,1.97,2.02)));

    // Constantes para el cálculo de costes de potencia con excesos horarios
    define("CONSTANTE_CALCULO_PENALIZACION_SOBREPOTENCIA_EXCESOS_CUARTOHORARIOS", 1.4064);
    define("CONSTANTE_TARIFA_ELECTRICA_K_PENALIZACION_TRAMOS", serialize(array(1.0, 0.5, 0.37, 0.37, 0.37, 0.17)));

    // Valores por defecto de parámetros de factura de tarifas eléctricas (España)
    define("VALOR_DEFECTO_IMPUESTO_ELECTRICO_FACTURAS_ELECTRICAS_ESPANYA", 5.11);
    define("VALOR_DEFECTO_IVA_FACTURAS_ELECTRICAS_ESPANYA", 21);
    define("VALOR_DEFECTO_IGIC_REDUCIDO_FACTURAS_ELECTRICAS_ESPANYA", 3);
    define("VALOR_DEFECTO_IGIC_NORMAL_FACTURAS_ELECTRICAS_ESPANYA", 7);

        // Valores por defecto de parámetros de factura de tarifas eléctricas (Portugal)
    define("VALOR_DEFECTO_IMPUESTO_ELECTRICO_FACTURAS_ELECTRICAS_PORTUGAL", 0.1);
    define("VALOR_DEFECTO_IVA_FACTURAS_ELECTRICAS_PORTUGAL", 23);
    define("VALOR_DEFECTO_IVA_REDUCIDO_FACTURAS_ELECTRICAS_PORTUGAL", 6);
    define("VALOR_DEFECTO_CONTRIBUCION_AUDIOVISUAL_FACTURAS_ELECTRICAS_PORTUGAL", 2.85);

    // Valores por defecto de parámetros de factura de tarifas de gas (España)
    define("VALOR_DEFECTO_IMPUESTO_GAS_FACTURAS_GAS_ESPANYA", 0.234);
    define("VALOR_DEFECTO_IVA_FACTURAS_GAS_ESPANYA", 21);

    // Opciones extra para añadir a las listas de tipos de límites de consumo de tramos de tarifa
    define("OPCIONES_EXTRA_LISTA_TIPOS_LIMITES_CONSUMO_TRAMOS_TARIFA_SIN_OPCIONES_EXTRA", "SIN_OPCIONES");
    define("OPCIONES_EXTRA_LISTA_TIPOS_LIMITES_CONSUMO_TRAMOS_TARIFA_NINGUNO", "NINGUNO");

    // Tipos de límite de consumo de tramos
    define("TIPO_LIMITES_CONSUMO_TRAMOS_NINGUNO", "NINGUNO");
    define("TIPO_LIMITES_CONSUMO_TRAMOS_ABSOLUTO", "ABSOLUTO");
    define("TIPO_LIMITES_CONSUMO_TRAMOS_DIARIO", "DIARIO");

    // Valores por defecto de parámetros de factura de tarifas de agua (España)
    define("VALOR_DEFECTO_IVA_CONSUMO_FACTURAS_AGUA_ESPANYA", 10);
    define("VALOR_DEFECTO_IGIC_CONSUMO_FACTURAS_AGUA_ESPANYA", 0);
    define("VALOR_DEFECTO_IVA_ALQUILER_CONTADOR_FACTURAS_AGUA_ESPANYA", 21);
    define("VALOR_DEFECTO_IGIC_ALQUILER_CONTADOR_FACTURAS_AGUA_ESPANYA", 7);

    // Números máximo de días
    define("NUMERO_MAXIMO_DIAS_RECALCULO_DATOS", 365);
    define("NUMERO_MAXIMO_DIAS_EXPORTACION_VALORES_PARAMETROS_ENERGIA_ELECTRICA", 100);
    define("NUMERO_MAXIMO_DIAS_EXPORTACION_COSTES_CONCEPTOS_CONSUMO_SENSOR_ELECTRICIDAD", 100);

    // Tipos de autoconsumo
    define("TIPO_AUTOCONSUMO_SIN_ACUMULACION", "SIN_ACUMULACION");
    define("TIPO_AUTOCONSUMO_CON_ACUMULACION", "CON_ACUMULACION");

    // Rangos de potencias
    define("RANGO_POTENCIAS_MAXIMO", "MAXIMO");
    define("RANGO_POTENCIAS_MEDIO", "MEDIO");
    define("RANGO_POTENCIAS_MINIMO", "MINIMO");

    // Porcentajes de rangos de potencias para el cálculo de optimización de potencias
    define("PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_TRAMO_MAXIMO", 100);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_TRAMO_MEDIO", 50);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_TRAMO_MINIMO", 5);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_POTENCIA_OPTIMA_TRAMO_CONTIGUO_MAXIMO", 50);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_POTENCIA_OPTIMA_TRAMO_CONTIGUO_MEDIO", 25);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_POTENCIA_OPTIMA_TRAMO_CONTIGUO_MINIMO", 10);

    // Rangos de caudales diarios
    define("RANGO_CAUDALES_DIARIOS_MAXIMO", "MAXIMO");
    define("RANGO_CAUDALES_DIARIOS_MEDIO", "MEDIO");
    define("RANGO_CAUDALES_DIARIOS_MINIMO", "MINIMO");

    // Porcentajes de rangos de caudales diarios para el cálculo de optimización de caudales diarios
    define("PORCENTAJE_RANGO_OPTIMIZADOR_CAUDALES_DIARIOS_MAXIMO", 100);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_CAUDALES_DIARIOS_MEDIO", 50);
    define("PORCENTAJE_RANGO_OPTIMIZADOR_CAUDALES_DIARIOS_MINIMO", 5);

    // Tipos de alquiler de contador
    define("TIPO_ALQUILER_CONTADOR_NINGUNO", "NINGUNO");
    define("TIPO_ALQUILER_CONTADOR_DIARIO", "DIARIO");
    define("TIPO_ALQUILER_CONTADOR_FIJO", "FIJO");

    // Tipos de administración de tarifas eléctricas
    define("TIPO_ADMINISTRACION_TARIFAS_UNICA", "unica");
    define("TIPO_ADMINISTRACION_TARIFAS_MULTIPLE", "multiple");

    // Tipos de conceptos adicionales de factura de tarifa
    define("TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO", "fijo");
    define("TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO", "diario");
    define("TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO", "consumo_absoluto");
    define("TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO", "consumo_diario");

    // Coste de conceptos de consumo
    define("COSTE_CONCEPTO_CONSUMO_DIRECTO", "_d");
    define("COSTE_CONCEPTO_CONSUMO_TARIFA_ACCESO", "_ta");
    define("COSTE_CONCEPTO_CONSUMO_OTROS", "_o");

    # Separador
    define("SEPARADOR_INDICE_NOMBRE_CONCEPTO_CONSUMO", "#");

    // Apartados del informe de estudio general (electricidad - España)
    define("APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_ELECTRICIDAD_ESPANYA", "portada");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA", "introduccion");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_ELECTRICIDAD_ESPANYA", "instalacion");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_CONSUMO_ELECTRICIDAD_ESPANYA", "resumen_consumo");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_COSTE_ELECTRICIDAD_ESPANYA", "resumen_coste");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA", "analisis_consumo");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA", "analisis_coste");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA", "excesos_potencia");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA", "excesos_energia_reactiva");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_CORTES_TENSION_ELECTRICIDAD_ESPANYA", "cortes_tension");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_ELECTRICIDAD_ESPANYA", "simulacion_factura");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_ELECTRICIDAD_ESPANYA", "conclusiones");

    // Apartados del informe de estudio general (gas - España)
    define("APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_GAS_ESPANYA", "portada");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_GAS_ESPANYA", "introduccion");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_GAS_ESPANYA", "instalacion");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_GAS_ESPANYA", "analisis_consumo");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_GAS_ESPANYA", "analisis_coste");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_CAUDAL_GAS_ESPANYA", "excesos_caudal");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_GAS_ESPANYA", "simulacion_factura");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_GAS_ESPANYA", "conclusiones");

    // Apartados del informe de estudio general (agua - España)
    define("APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_AGUA_ESPANYA", "portada");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_AGUA_ESPANYA", "introduccion");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_AGUA_ESPANYA", "instalacion");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA", "analisis_consumo");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_AGUA_ESPANYA", "simulacion_factura");
    define("APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_AGUA_ESPANYA", "conclusiones");

    // Anchuras de columnas de filtros de tarifas
    define("ANCHURAS_COLUMNAS_FILTRO_TARIFAS_ELECTRICAS_TABLA_ESPANYA", serialize(array(18, -1, -1, -1, 15, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_TARIFAS_GAS_TABLA_ESPANYA", serialize(array(18, -1, -1, 15, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_TARIFAS_AGUA_TABLA_ESPANYA", serialize(array(18, -1, -1, 15, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_GRUPOS_TARIFAS_TABLA", serialize(array(18, -1, -1)));

    // Estados de tarifas eléctricas
    define("ESTADO_TARIFA_TODOS", "TODOS");
    define("ESTADO_TARIFA_OK", "OK");
    define("ESTADO_TARIFA_AVISO_EXPIRACION", "AVISO_EXPIRACION");
    define("ESTADO_TARIFA_EXPIRADA", "EXPIRADA");

    // Conceptos de factura eléctrica
    define("CONCEPTO_FACTURA_ELECTRICA_TOTAL_ESPANYA", "total");
    define("CONCEPTO_FACTURA_ELECTRICA_ENERGIA_POTENCIA_ESPANYA", "energia_potencia");
    define("CONCEPTO_FACTURA_ELECTRICA_ENERGIA_ACTIVA_ESPANYA", "energia_activa");
    define("CONCEPTO_FACTURA_ELECTRICA_POTENCIA_ESPANYA", "potencia");
    define("CONCEPTO_FACTURA_ELECTRICA_EXCESOS_POTENCIA_ESPANYA", "excesos_potencia");
    define("CONCEPTO_FACTURA_ELECTRICA_ENERGIA_REACTIVA_ESPANYA", "energia_reactiva");
    define("CONCEPTO_FACTURA_ELECTRICA_OTROS_CONCEPTOS_ESPANYA", "otros_conceptos");

    // Conceptos de factura de gas
    define("CONCEPTO_FACTURA_GAS_TOTAL_ESPANYA", "total");
    define("CONCEPTO_FACTURA_GAS_CONSUMO_ESPANYA", "consumo");
    define("CONCEPTO_FACTURA_GAS_TERMINO_FIJO_ESPANYA", "gas");
    define("CONCEPTO_FACTURA_GAS_EXCESOS_CAUDAL_ESPANYA", "excesos_caudal");
    define("CONCEPTO_FACTURA_GAS_OTROS_CONCEPTOS_ESPANYA", "otros_conceptos");

    // Conceptos de factura de agua
    define("CONCEPTO_FACTURA_AGUA_TOTAL_ESPANYA", "total");
    define("CONCEPTO_FACTURA_AGUA_CONSUMO_ESPANYA", "consumo");
    define("CONCEPTO_FACTURA_AGUA_OTROS_CONCEPTOS_ESPANYA", "otros_conceptos");

    // Valor de consumos y costes
    define("VALOR_CONSUMO", "consumo");
    define("VALOR_COSTE", "coste");

    // Número de decimales a mostrar en las potencias y caudales seleccionados
    define("NUMERO_DECIMALES_POTENCIAS_SELECCIONADAS", 5);
    define("NUMERO_DECIMALES_CAUDALES_SELECCIONADOS", 5);

    // Tipos de valores de parámetros de energía eléctrica
    define("TIPO_VALORES_PARAMETROS_ENERGIA_ELECTRICA_ESTIMADOS", "ESTIMADOS");
    define("TIPO_VALORES_PARAMETROS_ENERGIA_ELECTRICA_AJUSTADOS", "AJUSTADOS");

    // Unidades de medida
    define("UNIDAD_MEDIDA_EUROS", "EUROS");
    define("UNIDAD_MEDIDA_CENTIMOS_EURO", "CENTIMOS_EURO");

    // Elementos de informes del módulo

    // Elementos de informe (Consumos y costes generales)
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS", "grafica_consumos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS_ACUMULADOS", "grafica_consumos_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_DESCRIPCIONES_SENSORES", "descripciones_sensores");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_CONSUMOS_MAXIMOS_MINIMOS", "tabla_consumos_maximos_minimos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES", "grafica_costes");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES_ACUMULADOS", "grafica_costes_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COSTES_MAXIMOS_MINIMOS", "tabla_costes_maximos_minimos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_PRECIOS", "grafica_precios");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_PRECIOS_MAXIMOS_MINIMOS", "tabla_precios_maximos_minimos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COMENTARIOS", "tabla_comentarios");

    // Elementos de informe (Consumos y costes totales)
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_CONSUMOS_TOTALES", "grafica_consumos_totales");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_CONSUMOS", "grafica_porcentajes_consumos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_CONSUMOS", "tabla_consumos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_COSTES_TOTALES", "grafica_costes_totales");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_COSTES", "grafica_porcentajes_costes");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PRECIOS_MEDIOS", "grafica_precios_medios");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_COSTES", "tabla_costes");

    // Elementos de informe (Comparación de periodos)
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS", "grafica_consumos");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES", "grafica_costes");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_COSTES", "tabla_evolucion_consumos_costes");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_TRAMOS", "tabla_evolucion_consumos_tramos");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS_TOTALES", "grafica_consumos_totales");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES_TOTALES", "grafica_costes_totales");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_PRECIOS_MEDIOS", "grafica_precios_medios");
    define("ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_PRECIOS_MEDIOS", "tabla_evolucion_precios_medios");

    // Elementos de informe (Simulador de tarifas)
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES", "grafica_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES_TOTALES", "grafica_costes_totales");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_COSTE_ACTUAL", "tabla_comparacion_coste_actual");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_MEJOR_OPCION", "tabla_comparacion_mejor_opcion");

    // Elementos de informe (Consumos y costes por tramos) (electricidad)
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_HORARIOS", "grafica_consumos_tramos_horarios");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_DIARIOS", "grafica_consumos_tramos_diarios");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_CONSUMOS_TRAMOS_DIAS_SEMANA", "grafica_medias_consumos_tramos_dias_semana");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_CONSUMOS_TRAMOS", "tabla_evolucion_consumos_tramos");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_HORARIOS", "grafica_costes_tramos_horarios");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_DIARIOS", "grafica_costes_tramos_diarios");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_COSTES_TRAMOS_DIAS_SEMANA", "grafica_medias_costes_tramos_dias_semana");
    define("ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_COSTES_TRAMOS", "tabla_evolucion_costes_tramos");

    // Elementos de informe (Cortes de tensión) (electricidad)
    define("ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_GRAFICA_CORTES_TENSION_CONSUMOS", "grafica_consumos_cortes_tension");
    define("ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TABLA_CORTES_TENSION", "tabla_cortes_tension");

    // Elementos de informe (Excesos de potencia) (electricidad - España)
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_POTENCIAS_POTENCIAS_CONTRATADAS", "grafica_potencias_potencias_contratadas");
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_SOBREPOTENCIAS_ABSOLUTAS", "grafica_sobrepotencias_absolutas");
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_TABLA_SOBREPOTENCIAS_TRAMOS", "tabla_sobrepotencias_tramos");

    // Elementos de informe (Excesos de energía reactiva) (electricidad - España)
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_CONSUMOS_ENERGIA", "grafica_consumos_energia");
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_COSENO_PHI", "grafica_coseno_phi");
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_PENALIZABLE", "grafica_penalizable");
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_TABLA_COSTES_ENERGIA_REACTIVA_TRAMOS", "tabla_energia_reactiva_tramos");

    // Elementos de informe (Excesos de caudal) (gas - España)
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_GRAFICA_CAUDALES_SOBRECAUDALES", "grafica_caudales_sobrecaudales");
    define("ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_TABLA_SOBRECAUDALES", "tabla_sobrecaudales");

    // Elementos de informe (Desvíos de compra de energía) (España)
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_DESVIOS_TOTALES", "tabla_consumos_desvios_totales");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS", "grafica_consumos");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS", "grafica_consumos_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO", "grafica_desvios_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO_ACUMULADOS", "grafica_desvios_consumo_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_DESVIOS_CONSUMO", "mapa_calor_desvios_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS", "grafica_costes_desvios");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_ACUMULADOS", "grafica_costes_desvios_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS", "mapa_calor_costes_desvios");

    // Elementos de informe (Desvíos ponderados de compra de energía) (España)
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES", "tabla_consumos_coste_desvio_ponderado_totales");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS", "grafica_consumos");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS", "grafica_consumos_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS", "grafica_costes_desvios_ponderados");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS_ACUMULADOS", "grafica_costes_desvios_ponderados_acumulados");
    define("ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS_PONDERADOS", "mapa_calor_costes_desvios_ponderados");

    // Elementos de informe (Simulación de factura) (electricidad - España)
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DATOS", "titulo_datos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_DATOS", "tabla_datos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_RESUMEN", "titulo_resumen");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_COSTE_CONSUMO", "tabla_coste_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DETALLES", "titulo_detalles");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_ACTIVA", "tabla_energia_activa");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA", "tabla_potencia");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA", "tabla_potencia_maxima_excesos_potencia");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_REACTIVA", "energia_reactiva");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_OTROS_CONCEPTOS", "tabla_otros_conceptos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS", "grafica_porcentajes_costes_conceptos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_REPARTO_COSTES", "titulo_reparto_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_REPARTO_COSTES", "tabla_reparto_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES", "grafica_porcentajes_reparto_costes");

    // Elementos de informe (Simulación de factura) (gas - España)
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_DATOS", "titulo_datos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_DATOS", "tabla_datos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_RESUMEN", "titulo_resumen");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_COSTE_CONSUMO", "tabla_coste_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_DETALLES", "titulo_detalles");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_CONSUMO", "tabla_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_TERMINO_FIJO", "tabla_termino_fijo");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_OTROS_CONCEPTOS", "tabla_otros_conceptos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS", "grafica_porcentajes_costes_conceptos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_REPARTO_COSTES", "titulo_reparto_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_REPARTO_COSTES", "tabla_reparto_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES", "grafica_porcentajes_reparto_costes");

    // Elementos de informe (Simulación de factura) (agua - España)
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_DATOS", "titulo_datos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_DATOS", "tabla_datos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_RESUMEN", "titulo_resumen");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_COSTE_CONSUMO", "tabla_coste_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_DETALLES", "titulo_detalles");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_CONSUMO", "tabla_consumo");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_OTROS_CONCEPTOS", "tabla_otros_conceptos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS", "grafica_porcentajes_costes_conceptos");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_REPARTO_COSTES", "titulo_reparto_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_REPARTO_COSTES", "tabla_reparto_costes");
    define("ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES", "grafica_porcentajes_reparto_costes");


    //
    // Módulo Proyectos
    //


    // Tipos de objetivos de proyecto
    define("TIPO_OBJETIVO_PROYECTO_PORCENTUAL", "porcentual");
    define("TIPO_OBJETIVO_PROYECTO_ABSOLUTO", "absoluto");

    // Tipos de valores de objetivos de proyecto
    define("TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR", "inferior");
    define("TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR", "superior");

    // Estados de proyecto
    define("ESTADO_PROYECTO_TODOS", "TODOS");
    define("ESTADO_PROYECTO_NINGUNO", "NINGUNO");
    define("ESTADO_PROYECTO_SIN_LINEA_BASE", "SIN_LINEA_BASE");
    define("ESTADO_PROYECTO_ERROR", "ERROR");
    define("ESTADO_PROYECTO_PENDIENTE", "PENDIENTE");
    define("ESTADO_PROYECTO_ACTIVO", "ACTIVO");
    define("ESTADO_PROYECTO_FINALIZADO", "FINALIZADO");

    // Estados de avance de proyecto
    define("ESTADO_AVANCE_PROYECTO_TODOS", "TODOS");
    define("ESTADO_AVANCE_PROYECTO_SIN_DATOS", "SIN_DATOS");
    define("ESTADO_AVANCE_PROYECTO_NINGUNO", "NINGUNO");
    define("ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO", "SIN_VALOR_OBJETIVO");
    define("ESTADO_AVANCE_PROYECTO_POSITIVO", "POSITIVO");
    define("ESTADO_AVANCE_PROYECTO_NEGATIVO", "NEGATIVO");

    // Porcentajes de avance máximo y mínimo
    define("PORCENTAJE_AVANCE_MAXIMO", 1000000);
    define("PORCENTAJE_AVANCE_MINIMO", -1000000);

    // Número de columnas de tablas
    define("NUMERO_COLUMNAS_TABLA_PROYECTOS", 5);
    define("NUMERO_COLUMNAS_TABLA_VALORES_ADICIONALES_PROYECTO", 7);
    define("NUMERO_COLUMNAS_TABLA_LINEAS_BASE", 4);
    define("NUMERO_COLUMNAS_TABLA_VARIABLES_LINEA_BASE", 3);
    define("NUMERO_COLUMNAS_TABLA_EXCEPCIONES_LINEA_BASE", 2);
    define("NUMERO_COLUMNAS_TABLA_PARAMETROS_PROYECTO", 6);
    define("NUMERO_COLUMNAS_TABLA_INFORMACION_PROYECTO", 6);
    define("NUMERO_COLUMNAS_TABLAS_ERRORES_COEFICIENTES_LINEAS_BASE", 4);

    // Anchuras de columnas de tablas
    define("ANCHURAS_COLUMNAS_TABLA_PROYECTOS", serialize(array(20, 30, 15, 20, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_VALORES_ADICIONALES_PROYECTO", serialize(array(20, 10, 15, 10, 12, 12, 21)));
    define("ANCHURAS_COLUMNAS_TABLA_LINEAS_BASE", serialize(array(25, 35, 20, 20)));
    define("ANCHURAS_COLUMNAS_TABLA_PARAMETROS_PROYECTO", serialize(array(30, 10, 20, 10, 15, 15)));
    define("ANCHURAS_COLUMNAS_TABLA_INFORMACION_PROYECTO", serialize(array(15, 15, 15, 15, 25, 15)));

    // Anchuras de columnas de filtros de tablas
    define("ANCHURAS_COLUMNAS_FILTRO_PROYECTOS_TABLA", serialize(array(18, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_FILTRO_LINEAS_BASE_TABLA", serialize(array(18, -1, -1, -1)));

    // Tipos de líneas base
    define("TIPO_LINEA_BASE_PERIODICA", "PERIODICA");
    define("TIPO_LINEA_BASE_FUNCIONAL", "FUNCIONAL");

    // Periodicidades de valores de líneas base
    define("PERIODICIDAD_VALORES_LINEA_BASE_DIARIA", "diaria");
    define("PERIODICIDAD_VALORES_LINEA_BASE_SEMANAL", "semanal");

    // Tipos de cálculo de valores de líneas base
    define("TIPO_CALCULO_VALORES_LINEA_BASE_MEDIA", "media");
    define("TIPO_CALCULO_VALORES_LINEA_BASE_MEDIANA", "mediana");

    // Valor de prueba por defecto para la evaluación de función de línea base
    define("VALOR_PRUEBA_DEFECTO_FUNCION_LINEA_BASE", 1);

    // Número de decimales a mostrar en las variables estadísticas de la línea base
    define("NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE", 5);
    define("NUMERO_DECIMALES_COEFICIENTE_VARIACION_LINEA_BASE", 5);
    define("NUMERO_DECIMALES_COEFICIENTE_CORRELACION_LINEA_BASE", 5);

    // Opciones extra para añadir a listas de estados de proyectos
    define("OPCIONES_EXTRA_LISTA_ESTADOS_AVANCE_PROYECTO_TODOS", "TODOS");
    define("OPCIONES_EXTRA_LISTA_ESTADOS_PROYECTO_TODOS", "TODOS");

    // Periodos por defecto de informes
    define("PERIODO_DEFECTO_PROYECTOS_SIMULADOR_LINEA_BASE", PERIODO_DIA_INICIO_SEMANA);
    define("PERIODO_DEFECTO_PROYECTOS_INFORMACION_PROYECTO", PERIODO_DIA_INICIO_SEMANA);

    // Número de columnas de parámetros de informes
    define("NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_LINEA_BASE", "3, 3");
    define("NUMERO_COLUMNAS_PARAMETROS_INFORMACION_PROYECTO", "2, 3");

    // Anchuras de columnas de parámetros de informes
    define("ANCHURAS_COLUMNAS_PARAMETROS_LINEA_BASE", serialize(array(35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_LINEA_BASE", serialize(array(-1, -1, -1, -1, -1, -1)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_PROYECTO", serialize(array(35)));
    define("ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_PROYECTO", serialize(array(-1, -1, -1, -1, -1)));

    // Destinos de valores adicionales de proyectos
    define("DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_REALES", "valores_reales");
    define("DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_SIMULADOS", "valores_simulados");

    // Periodicidades de valores adicionales de proyectos
    define("PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_NINGUNA", "ninguna");
    define("PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_PUNTUAL", "puntual");
    define("PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_HORARIA", "horaria");
    define("PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_DIARIA", "diaria");
    define("PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_SEMANAL", "semanal");
    define("PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_MENSUAL", "mensual");

    // Tipos de informes
    define("TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE", "PROYECTOS_SIMULADOR_LINEA_BASE");
    define("TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO", "PROYECTOS_INFORMACION_PROYECTO");

    // Elementos de informes del módulo

    // Elementos de informe (Simulador de línea base)
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS", "grafica_diferencias");
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS_ACUMULADAS", "grafica_diferencias_acumuladas");
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_DESCRIPCION_SENSOR", "descripcion_sensor");
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERROR_COEFICIENTES_LINEA_BASE", "tabla_error_coeficientes_linea_base");
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES", "tabla_errores_coeficientes_lineas_base_excepciones");
    define("ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_COMENTARIOS", "tabla_comentarios");

    // Elementos de informe (Información de proyecto)
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_PARAMETROS_PROYECTO", "tabla_parametros_proyecto");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_INFORMACION_PROYECTO", "tabla_informacion_proyecto");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_VALORES", "grafica_valores");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS", "grafica_diferencias");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS_ACUMULADAS", "grafica_diferencias_acumuladas");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_VALORES_ADICIONALES_PROYECTO", "tabla_valores_adicionales_proyecto");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERROR_COEFICIENTES_LINEA_BASE", "tabla_error_coeficientes_linea_base");
    define("ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES", "tabla_errores_coeficientes_lineas_base_excepciones");


    //
    // Procesado EMIOS
    //


    // Nombres de funciones de Procesado EMIOS
    define("NOMBRE_FUNCION_BORRA_VALORES_REALES_SIMULADOS_PROYECTO", "BORRA_VALORES_REALES_SIMULADOS_PROYECTO");
    define("NOMBRE_FUNCION_BORRA_VALORES_REALES_SIMULADOS_PROYECTOS_RED", "BORRA_VALORES_REALES_SIMULADOS_PROYECTOS_RED");
    define("NOMBRE_FUNCION_BORRA_VALORES_SENSOR", "BORRA_VALORES_SENSOR");
    define("NOMBRE_FUNCION_BORRA_VALORES_SENSORES_RED", "BORRA_VALORES_SENSORES_RED");
    define("NOMBRE_FUNCION_CALCULA_AVANCE_ESTADO_PROYECTO", "CALCULA_AVANCE_ESTADO_PROYECTO");
    define("NOMBRE_FUNCION_CALCULA_AVANCE_ESTADO_PROYECTOS_REDES_ZONA_HORARIA", "CALCULA_AVANCE_ESTADO_PROYECTOS_REDES_ZONA_HORARIA");
    define("NOMBRE_FUNCION_CALCULA_CORRELACION_VALORES_SENSORES", "CALCULA_CORRELACION_VALORES_SENSORES");
    define("NOMBRE_FUNCION_CALCULA_COSTES_CONSUMO_SENSOR_TARIFA", "CALCULA_COSTES_CONSUMO_SENSOR_TARIFA");
    define("NOMBRE_FUNCION_CALCULA_COSTES_CONSUMO_TARIFA_CONSUMOS", "CALCULA_COSTES_CONSUMO_TARIFA_CONSUMOS");
    define("NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_OPTIMAS_SENSOR_TARIFA_ELECTRICA_ESPANYA", "CALCULA_COSTES_POTENCIAS_OPTIMAS_SENSOR_TARIFA_ELECTRICA_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_OPTIMAS_POTENCIAS_MAXIMAS_MENSUALES_TARIFA_ELECTRICA_ESPANYA", "CALCULA_COSTES_POTENCIAS_OPTIMAS_POTENCIAS_MAXIMAS_MENSUALES_TARIFA_ELECTRICA_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_SELECCIONADAS_SENSOR_TARIFA_ELECTRICA_ESPANYA", "CALCULA_COSTES_POTENCIAS_SELECCIONADAS_SENSOR_TARIFA_ELECTRICA_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_SELECCIONADAS_POTENCIAS_MAXIMAS_MENSUALES_TARIFA_ELECTRICA_ESPANYA", "CALCULA_COSTES_POTENCIAS_SELECCIONADAS_POTENCIAS_MAXIMAS_MENSUALES_TARIFA_ELECTRICA_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_OPTIMO_SENSOR_TARIFA_GAS_ESPANYA", "CALCULA_COSTE_CAUDAL_DIARIO_OPTIMO_SENSOR_TARIFA_GAS_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_OPTIMO_CAUDALES_DIARIOS_MAXIMOS_MENSUALES_TARIFA_GAS_ESPANYA", "CALCULA_COSTE_CAUDAL_DIARIO_OPTIMO_CAUDALES_DIARIOS_MAXIMOS_MENSUALES_TARIFA_GAS_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_SELECCIONADO_SENSOR_TARIFA_GAS_ESPANYA", "CALCULA_COSTE_CAUDAL_DIARIO_SELECCIONADO_SENSOR_TARIFA_GAS_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_SELECCIONADO_CAUDALES_DIARIOS_MAXIMOS_MENSUALES_TARIFA_GAS_ESPANYA", "CALCULA_COSTE_CAUDAL_DIARIO_SELECCIONADO_CAUDALES_DIARIOS_MAXIMOS_MENSUALES_TARIFA_GAS_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA", "CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA");
    define("NOMBRE_FUNCION_CALCULA_DESVIOS_PONDERADOS_COMPRA_ENERGIA_SENSOR_ESPANYA", "CALCULA_DESVIOS_PONDERADOS_COMPRA_ENERGIA_SENSOR_ESPANYA");
    define("NOMBRE_FUNCION_CALCULA_HISTOGRAMA_VALOR_SENSOR", "CALCULA_HISTOGRAMA_VALOR_SENSOR");
    define("NOMBRE_FUNCION_CALCULA_VALORES_REALES_SIMULADOS_LINEA_BASE", "CALCULA_VALORES_REALES_SIMULADOS_LINEA_BASE");
    define("NOMBRE_FUNCION_CALCULA_VALORES_REALES_SIMULADOS_INFO_LINEA_BASE", "CALCULA_VALORES_REALES_SIMULADOS_INFO_LINEA_BASE");
    define("NOMBRE_FUNCION_CALCULA_VALORES_REALES_SIMULADOS_PERFIL_HORARIO", "CALCULA_VALORES_REALES_SIMULADOS_PERFIL_HORARIO");
    define("NOMBRE_FUNCION_EVALUA_FORMULA_PRECIO_CONSUMO_PASS_THROUGH_ESPANYA", "EVALUA_FORMULA_PRECIO_CONSUMO_PASS_THROUGH_ESPANYA");
    define("NOMBRE_FUNCION_EVALUA_FORMULA_PRECIO_CONSUMO_CIERRE_ESPANYA", "EVALUA_FORMULA_PRECIO_CONSUMO_CIERRE_ESPANYA");
    define("NOMBRE_FUNCION_EVALUA_FUNCION_VALORES", "EVALUA_FUNCION_VALORES");
    define("NOMBRE_FUNCION_IMPORTA_INCREMENTOS_VALORES_SENSOR_FICHERO_CSV", "IMPORTA_INCREMENTOS_VALORES_SENSOR_FICHERO_CSV");
    define("NOMBRE_FUNCION_IMPORTA_VALORES_SENSOR_FICHERO_CSV", "IMPORTA_VALORES_SENSOR_FICHERO_CSV");
    define("NOMBRE_FUNCION_MODIFICA_VALORES_SENSOR", "MODIFICA_VALORES_SENSOR");
    define("NOMBRE_FUNCION_VALIDA_FACTURAS_FICHEROS", "VALIDA_FACTURAS_FICHEROS");

    // Tipos de tareas de Procesado EMIOS
    define("TIPO_TAREA_AGRUPA_INCREMENTOS_VALORES_PERIODOS_SENSORES", "AGRUPA_INCREMENTOS_VALORES_PERIODOS_SENSORES");
    define("TIPO_TAREA_BORRA_VALORES_CADUCADOS_SENSORES", "BORRA_VALORES_CADUCADOS_SENSORES");
    define("TIPO_TAREA_BORRA_VALORES_PENDIENTES_BORRADO_SENSORES", "BORRA_VALORES_PENDIENTES_BORRADO_SENSORES");
    define("TIPO_TAREA_CALCULA_VALORES_PERIODOS_SENSORES", "CALCULA_VALORES_PERIODOS_SENSORES");
    define("TIPO_TAREA_CALCULA_VALORES_SENSORES_CLASE_SENSOR", "CALCULA_VALORES_SENSORES_CLASE_SENSOR");
    define("TIPO_TAREA_CALCULA_VALORES_SENSORES_PROCESADO", "CALCULA_VALORES_SENSORES_PROCESADO");
    define("TIPO_TAREA_CALCULA_VALORES_SENSORES_VIRTUALES", "CALCULA_VALORES_SENSORES_VIRTUALES");
    define("NOMBRE_FUNCION_REGISTRA_FICHEROS_EXCEL", "REGISTRA_FICHEROS_EXCEL");
    define("NOMBRE_FUNCION_ELIMINA_FICHEROS_EXCEL", "ELIMINA_FICHEROS_EXCEL");
    define("NOMBRE_FUNCION_DESCARGA_FICHERO_EXCEL", "DESCARGA_FICHEROS_EXCEL");


    //
    // Servicios EMIOS
    //


    // Nombres de funciones de Servicios EMIOS
    define("NOMBRE_FUNCION_GENERA_INFORME_FICHERO", "GENERA_INFORME_FICHERO");
?>
