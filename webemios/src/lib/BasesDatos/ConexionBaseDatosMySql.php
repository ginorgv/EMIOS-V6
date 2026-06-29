<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include_once($_SESSION["directorio"].'/comun/log/log.php');

include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ConexionBaseDatos.php');
include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ResultadoConsultaMySql.php');


// Constantes

// Número máximo de filas permitido en una consulta
define("NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL", 50000);

// Códigos de excepciones
define("CODIGO_EXCEPCION_ERROR_CONEXION_MYSQL", 2);
define("CODIGO_EXCEPCION_ERROR_VERSION_BASE_DATOS_MYSQL", 3);
define("CODIGO_EXCEPCION_NUMERO_MAXIMO_FILAS_CONSULTA_SUPERADO_MYSQL", 4);


class ConexionBaseDatosMySql extends ConexionBaseDatos
{
    // Inicia la conexión con la base de datos (comprueba la versión de la base de datos)
    function conecta()
	{
        $this->conexion = mysqli_connect(
            $this->params["direccion_ip"],
            $this->params["usuario"],
            $this->params["contrasenya"],
            $this->params["base_datos"],
            $this->params["puerto"]);
        if ($this->conexion == false)
        {
            throw new Exception(
                "No se ha podido conectar a la base de datos: ".mysqli_connect_error(),
                CODIGO_EXCEPCION_ERROR_CONEXION_MYSQL);

        }
        mysqli_autocommit($this->conexion, true);

		$charset = 'utf8';
		$consulta = "SET names ".$charset;

        session_write_close();
		$this->conexion->query($consulta);
        session_start();

        $this->comprueba_version();
        $this->conectado = true;
    }


    // Finaliza la conexión con la base de datos
    // (http://stackoverflow.com/questions/336078/php-mysql-when-is-the-best-time-to-disconnect-from-the-database)
    function desconecta()
	{
        mysqli_close($this->conexion);
        $this->conectado = false;
    }


    // Comprueba la versión de la base de datos
    function comprueba_version()
	{
        $consulta_version = "
			SELECT version
			FROM version";
		$res_version = $this->conexion->query($consulta_version);
        if (($res_version == false) || ($res_version->num_rows == 0))
        {
            throw new Exception(
                "No existe información de versión de la base de datos",
                CODIGO_EXCEPCION_ERROR_VERSION_BASE_DATOS_MYSQL);
        }

		$fila_version = $res_version->fetch_assoc();
        $version = $fila_version['version'];
        if ($version != $this->params["version"])
        {
            throw new Exception(
                "Versión de base de datos de red incorrecta: '".$version."' (versión requerida: '".$this->params["version"]."')",
                CODIGO_EXCEPCION_ERROR_VERSION_BASE_DATOS_MYSQL);
        }
    }


    // Ejecuta una operación en la base de datos
    function ejecuta_operacion($operacion)
	{
        session_write_close();
        $res = $this->conexion->query($operacion);
        session_start();

        return ($res);
	}


    // Devuelve el id autoincremental de la última inserción
    function dame_id_autoincremental_ultima_insercion()
    {
        $id = $this->conexion->insert_id;
        return ($id);
    }


    // Devuelve el número de filas afectadas de la última operación
    function dame_numero_filas_afectadas_ultima_operacion()
    {
        // Nota: 'mysqli_affected_rows' no funciona, siempre devuelve -1
        $consulta_numero_filas = "SELECT ROW_COUNT() AS numero_filas";

        session_write_close();
		$res_numero_filas = $this->conexion->query($consulta_numero_filas);
        session_start();

        if ($res_numero_filas == false)
        {
            throw new Exception("Error al recuperar el número de filas afectadas: ".mysqli_connect_error());
        }

		$fila_numero_filas = $res_numero_filas->fetch_assoc();
        $numero_filas = $fila_numero_filas['numero_filas'];
        return ($numero_filas);
    }


    // Ejecuta una consulta en la base de datos
    function ejecuta_consulta($consulta)
	{
        $reutilizar_consultas = false;
        if (isset($GLOBALS['reutilizar_consultas_bases_datos']))
        {
            $reutilizar_consultas = $GLOBALS['reutilizar_consultas_bases_datos'];
        }
        if ($reutilizar_consultas == true)
        {
            $clave_consulta = trim(preg_replace('/\s+/', ' ', $consulta));
            if (isset($GLOBALS['resultado_consulta: '.$clave_consulta]))
            {
                /*$log = dame_log();
                $log->info("Consulta reutilizada: '".$clave_consulta."'");*/

                $res_consulta = $GLOBALS['resultado_consulta: '.$clave_consulta];
                $res_consulta->reinicia_contador_iteracion_filas(0);
                return ($res_consulta);
            }
        }

        // Se comprueba si la consulta supera el número de filas máximo
        if ($this->comprobar_limite_numero_filas_consultas == true)
        {
            $numero_filas_consulta = $this->dame_numero_filas_consulta($consulta);
            if ($numero_filas_consulta > NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL)
            {
                throw new Exception(
                    "Número máximo de filas superado (consulta: '".$consulta."', número de filas: '".$numero_filas_consulta."')",
                    CODIGO_EXCEPCION_NUMERO_MAXIMO_FILAS_CONSULTA_SUPERADO_MYSQL);
            }
        }

        session_write_close();
        $res = $this->conexion->query($consulta);
        session_start();

        if ($res == false)
        {
            return (false);
        }
        else
        {
            $res_consulta = new ResultadoConsultaMySql($res);
            if ($reutilizar_consultas == true)
            {
                /*$log = dame_log();
                $log->info("Consulta realizada: '".$clave_consulta."'");*/

                $GLOBALS['resultado_consulta: '.$clave_consulta] = $res_consulta;
            }
            return ($res_consulta);
        }
	}


    // Devuelve el número de filas de la consulta
    function dame_numero_filas_consulta($consulta)
    {
        try
        {
            // Puede haber subconsultas unidas por 'UNION DISTINCT' (están entre paréntesis)
            // (se comprueba cada una de ellas)
            $hay_subconsultas = (strpos($consulta, "UNION DISTINCT") !== false);
            if ($hay_subconsultas == true)
            {
                $subconsultas = explode("UNION DISTINCT", $consulta);
            }
            else
            {
                $subconsultas = array($consulta);
            }

            // Se recorren cada una de las subconsultas
            $numero_maximo_filas_consulta = -INF;
            foreach ($subconsultas as $subconsulta)
            {
                // Si hay límite de filas en la consulta no se comprueban el número de filas totales
                $hay_clausula_limit = strpos($subconsulta, "LIMIT ");
                if ($hay_clausula_limit != false)
                {
                    continue;
                }

                // Se crea la consulta del número de filas
                $pos_from = strpos($subconsulta, "FROM");
                $subconsulta_sin_select = substr($subconsulta, $pos_from);
                $subconsulta_numero_filas = "";
                if ($hay_subconsultas == false)
                {
                    $subconsulta_numero_filas = "";
                }
                else
                {
                    $subconsulta_numero_filas = "(";
                }
                $subconsulta_numero_filas .= "SELECT COUNT(*) AS numero_filas ".$subconsulta_sin_select;

                // Se elimina la ordenación (si existe)
                $pos_order_by = strpos($subconsulta_numero_filas, "ORDER BY");
                if ($pos_order_by != false)
                {
                    $subconsulta_numero_filas = substr($subconsulta_numero_filas, 0, $pos_order_by);
                }

                // Se realiza la consulta del número de filas
                $res_numero_filas = $this->conexion->query($subconsulta_numero_filas);
                if ($res_numero_filas == false)
                {
                    throw new Exception("Error al realizar la consulta de número de filas: '".$subconsulta_numero_filas."'\n(consulta original: '".$consulta."'");
                }
                else
                {
                    $res_numero_filas = new ResultadoConsultaMySql($res_numero_filas);

                    // Si hay claúsula 'GROUP BY', haber 0, 1 o varias filas en la respuesta
                    $numero_filas_subconsulta = 0;
                    while ($fila_numero_filas = $res_numero_filas->dame_siguiente_fila())
                    {
                        $numero_filas_subconsulta_fila = $fila_numero_filas["numero_filas"];
                        $numero_filas_subconsulta += $numero_filas_subconsulta_fila;
                    }

                    // Comprobación de número máximo de filas de la subconsulta
                    if ($numero_filas_subconsulta > $numero_maximo_filas_consulta)
                    {
                        $numero_maximo_filas_consulta = $numero_filas_subconsulta;
                    }
                    if ($numero_maximo_filas_consulta > NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL)
                    {
                        break;
                    }
                }
            }
        }
        catch (Exception $exception)
        {
            // Se añade información de la excepción en el log
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] ".
                "Excepción capturada: ", $exception);

            // Si hay un error se permite realizar la consulta
            $numero_maximo_filas_consulta = 0;
        }

        // Se devuelve el número máximo de filas de la consulta (por si hay varias subsconsultas)
        return ($numero_maximo_filas_consulta);
    }


    // Convierte los caracteres especiales de una cadena
	function _($cadena)
	{
		return ($this->conexion->escape_string($cadena));
	}
}
?>