<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_arboles.php');


    // Carga la información de las líneas base padres e hijas en las excepciones
	function carga_informacion_lineas_base_padres_hijas_excepciones(&$info_lineas_base_padres, &$info_lineas_base_hijas)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_hijas_lineas_base_excepciones = "
            SELECT
                excepciones_lineas_base.linea_base_padre,
                excepciones_lineas_base.linea_base_hija
            FROM
                excepciones_lineas_base,
                lineas_base
            WHERE
                (excepciones_lineas_base.linea_base_padre = lineas_base.id)
                AND (lineas_base.red = '".$_SESSION["id_red"]."')
            ORDER BY
                excepciones_lineas_base.linea_base_padre ASC,
                excepciones_lineas_base.linea_base_hija ASC";
        $res_hijas_lineas_base_excepciones = $bd_red->ejecuta_consulta($consulta_hijas_lineas_base_excepciones);
        if ($res_hijas_lineas_base_excepciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_hijas_lineas_base_excepciones."'");
        }

        $info_lineas_base_padres = array();
        $info_lineas_base_hijas = array();
        while ($fila_hija_linea_base = $res_hijas_lineas_base_excepciones->dame_siguiente_fila())
        {
            $id_linea_base_padre = $fila_hija_linea_base["linea_base_padre"];
            $id_linea_base_hija = $fila_hija_linea_base["linea_base_hija"];

            // Se añade la información de líneas base padres y de líneas base hijas
            anyade_nodo_hijo($info_lineas_base_padres, $id_linea_base_hija, $id_linea_base_padre);
            anyade_nodo_hijo($info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija);
        }
    }


    //
    // Funciones de administración de líneas base hijas
    //


    function anyade_linea_base_padre(&$info_lineas_base_padres, $id_linea_base_padre, $id_linea_base_hija)
	{
        anyade_nodo_hijo($info_lineas_base_padres, $id_linea_base_hija, $id_linea_base_padre);
    }


    function elimina_linea_base_padre(&$info_lineas_base_padres, $id_linea_base_padre, $id_linea_base_hija)
	{
        elimina_nodo_hijo($info_lineas_base_padres, $id_linea_base_hija, $id_linea_base_padre);
    }


    function anyade_linea_base_hija(&$info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija)
	{
        anyade_nodo_hijo($info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija);
    }


    function elimina_linea_base_hija(&$info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija)
	{
        elimina_nodo_hijo($info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija);
    }


    function existe_bucle_lineas_base_hijas($info_lineas_base_hijas)
	{
        return (existe_bucle_nodos_hijos($info_lineas_base_hijas));
    }


    //
    // Funciones de obtención de líneas base ascendientes y descendientes
    //


    // Devuelve las líneas base ascendientes de las localizaciones especificadas
    function dame_ids_lineas_base_ascendientes_lineas_base($info_lineas_base_padres, $ids_lineas_base_hijas, $incluir_ids_lineas_base_hijas)
	{
        $nodos_visitados = array();
        $ids_lineas_base_ascendientes = dame_nodos_descendientes_nodos($info_lineas_base_padres, $ids_lineas_base_hijas, $nodos_visitados);
        if ($incluir_ids_lineas_base_hijas == false)
        {
            $ids_lineas_base_ascendientes = array_diff($ids_lineas_base_ascendientes, $ids_lineas_base_hijas);
        }
        return ($ids_lineas_base_ascendientes);
    }
?>
