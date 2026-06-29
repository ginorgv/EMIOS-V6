<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ConexionBaseDatosMySql.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    // Devuelve el mensaje de error de la excepción (si existe)
    function dame_mensaje_error_excepcion($excepcion)
    {
        // Mensaje de error dependiente del código de la excepción
        $codigo_excepcion = $excepcion->getCode();
        switch ($codigo_excepcion)
        {
            case CODIGO_EXCEPCION_SESION_INCORRECTA:
            {
                $mensaje_error_excepcion = "Usuario o red incorrectos";
                $cadena_usuario_actual = "usuario actual";
                $cadena_red_actual = "red actual";
                if ($_SESSION["idioma"] !== NULL)
                {
                    $idiomas = new Idiomas();
                    $mensaje_error_excepcion = $idiomas->_($mensaje_error_excepcion);
                    $cadena_usuario_actual = $idiomas->_($cadena_usuario_actual);
                    $cadena_red_actual = $idiomas->_($cadena_red_actual);
                }
                $nombre_red = dame_nombre_red($_SESSION["id_red"]);
                $mensaje_error_excepcion .= "<br/>(".
                    $cadena_usuario_actual.": ".$_SESSION["id_usuario"].", ".
                    $cadena_red_actual.": ".$nombre_red.")";
                break;
            }
            case CODIGO_EXCEPCION_ERROR_CONEXION_MYSQL:
            {
                $mensaje_error_excepcion = "No se ha podido conectar a la base de datos";
                $cadena_codigo_error = "código de error";
                $codigo_error_conexion_mysql = mysqli_connect_errno();
                if ($_SESSION["idioma"] !== NULL)
                {
                    $idiomas = new Idiomas();
                    $mensaje_error_excepcion = $idiomas->_($mensaje_error_excepcion);
                    $cadena_codigo_error = $idiomas->_($cadena_codigo_error);
                }
                $mensaje_error_excepcion .= " (".$cadena_codigo_error.": ".$codigo_error_conexion_mysql.")";
                break;
            }
            case CODIGO_EXCEPCION_ERROR_VERSION_BASE_DATOS_MYSQL:
            {
                $mensaje_error_excepcion = "Error de versión de base de datos";
                if ($_SESSION["idioma"] !== NULL)
                {
                    $idiomas = new Idiomas();
                    $mensaje_error_excepcion = $idiomas->_($mensaje_error_excepcion);
                }
                break;
            }
            case CODIGO_EXCEPCION_NUMERO_MAXIMO_FILAS_CONSULTA_SUPERADO_MYSQL:
            {
                $mensaje_error_excepcion = "Número máximo de valores superado (refine la búsqueda)";
                $cadena_numero_maximo_valores = "número máximo de valores";
                if ($_SESSION["idioma"] !== NULL)
                {
                    $idiomas = new Idiomas();
                    $mensaje_error_excepcion = $idiomas->_($mensaje_error_excepcion);
                    $cadena_numero_maximo_valores = $idiomas->_($cadena_numero_maximo_valores);
                }
                $numero_maximo_valores = formatea_numero(NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL, 0);
                $mensaje_error_excepcion .= "<br/>(".$cadena_numero_maximo_valores.": ".$numero_maximo_valores.")";
                break;
            }
            default:
            {
                $mensaje_error_excepcion = "Ha ocurrido un error";
                if ($_SESSION["idioma"] !== NULL)
                {
                    $idiomas = new Idiomas();
                    $mensaje_error_excepcion = $idiomas->_($mensaje_error_excepcion);
                }
                break;
            }
        }

        // Se devuelve el mensaje de error de la excepción
        return ($mensaje_error_excepcion);
    }
?>