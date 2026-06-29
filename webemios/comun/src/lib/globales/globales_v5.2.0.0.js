// Ficheros WEB concatenados
var ficheros_web_concatenados = false;

// Log de peticiones 'ajax' activado
var log_peticiones_ajax_activado = false;

// Número de reintentos restantes de petición 'ajax' actual
var numero_reintentos_restantes_peticion_ajax = null;
var ultima_peticion_ajax_correcta = null;

// Id de sesión
var id_sesion = "";

// Flags de entrando y saliendo de sesión
var entrando_sesion = false;
var saliendo_sesion = false;

// Error de 'ajax' capturado
var error_ajax_capturado = false;

// Usuario interno
var usuario_interno = false;
var comprobar_usuario_interno = false;

// Comprobación periódica de sesión correcta
var temporizador_comprobacion_sesion_correcta = null;

// Flag de pantalla completa activada
var pantalla_completa_activada = false;

// Colores de tema
var color_tema_oscuro = null;
var color_tema_intermedio = null;
var color_tema_claro = null;
var color_tema_fondo = null;

// Formatos de fecha
var formato_fecha_local = FORMATO_FECHA_LOCAL_DEFECTO;
var formato_dia_anyo_local = FORMATO_DIA_ANYO_LOCAL_DEFECTO;

// Formateado de números
var separador_miles = SEPARADOR_MILES_DEFECTO;
var punto_decimal = PUNTO_DECIMAL_DEFECTO;


