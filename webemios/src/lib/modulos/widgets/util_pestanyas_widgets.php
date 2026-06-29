<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');


    // Devuelve las pestañas de widgets del módulo especificado
    function dame_pestanyas_widgets_modulo($modulo, $id_pestanya_seleccionada)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan las pestañas de widgets
        $consulta_pestanyas_widgets = "
            SELECT
                id,
                nombre
            FROM pestanyas_widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (modulo = '".$bd_red->_($modulo)."')
            ORDER BY posicion";
        $res_pestanyas_widgets = $bd_red->ejecuta_consulta($consulta_pestanyas_widgets);
        if ($res_pestanyas_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas_widgets."'");
        }

        $contenido = "
            <div id='pestanyas-widgets' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='tabs-pestanyas-widgets'>";

        // Se añaden las pestañas de widgets
        $ids_pestanyas = array();
        while ($fila_pestanya_widgets = $res_pestanyas_widgets->dame_siguiente_fila())
        {
            $nombre = $fila_pestanya_widgets['nombre'];
            $id_pestanya = $fila_pestanya_widgets['id'];

            // Se añade el identificador de pestaña
            array_push($ids_pestanyas, $id_pestanya);

            // Se añade la pestaña
            $contenido .= "
                    <li class='titulo-pestanya";
            if (((count($ids_pestanyas) == 1) && ($id_pestanya_seleccionada === NULL)) ||
                ($id_pestanya_seleccionada == $id_pestanya))
            {
                $contenido .= " active";
            }
            $contenido .= "'><a data-toggle='tab' href='#tab-pestanya-widgets__".$id_pestanya."'>".htmlspecialchars($nombre, ENT_QUOTES)."</a></li>";
        }

        // Pestaña (+) para añadir nueva pestaña
        // - Nota: No se pone como activa (aunque no haya pestañas de widgets) para que se muestre siempre en blanco (sin seleccionar)
        $administracion_widgets = dame_administracion_widgets();
        if ($administracion_widgets == true)
        {
            $contenido .= "
                    <li class='titulo-pestanya'><a data-toggle='tab' id='tab-pestanya-widgets-anyadir-pestanya'>+</a></li>";
        }

        $contenido .= "
                </ul>
            </div>";

        // Contenido de las pestañas de widgets
        $contenido .= "
            <div id='tab-widgets-pestanyas' class='tab-content'>";
        for ($i = 0; $i < count($ids_pestanyas); $i++)
        {
            // Identificador de pestaña
            $id_pestanya = $ids_pestanyas[$i];

            // Se añade el contenido de la pestaña
            $contenido .= "
                    <div class='tab-pane pestanya-widgets";
            if ((($i == 0) && ($id_pestanya_seleccionada === NULL)) ||
                ($id_pestanya_seleccionada == $id_pestanya))
            {
                $contenido .= " active";
            }
            $contenido .= "' id='tab-pestanya-widgets__".$id_pestanya."'>";
            $contenido .= dame_contenido_pestanya_widgets_vacia($id_pestanya);
            $contenido .= "
                    </div>";
        }
        // Se añade el contenido de la pestaña de añadir pestaña
        $contenido .= "
                <div class='tab-pane pestanya-widgets";
        if (count($ids_pestanyas) == 0)
        {
            $contenido .= " active";
        }
        $contenido .= "' id='tab-pestanya-widgets-anyadir-pestanya"."'>";
        $contenido .= dame_contenido_pestanya_widgets_anyadir_pestanya();
        $contenido .= "
                </div>";
        $contenido .= "
            </div>
        </div>";

        return ($contenido);
    }


    // Devuelve información de pestañas de widgets del módulo especificada para la actualización periódica
    function dame_info_pestanyas_widgets_modulo_actualizacion_periodica($modulo, $id_pestanya_seleccionada)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_pestanyas_widgets = "
            SELECT
                id,
                nombre,
                actualizacion_periodica_rotatoria
            FROM pestanyas_widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (modulo = '".$bd_red->_($modulo)."')
            ORDER BY posicion ASC";
        $res_pestanyas_widgets = $bd_red->ejecuta_consulta($consulta_pestanyas_widgets);
        if ($res_pestanyas_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas_widgets."'");
        }

        $id_primera_pestanya_widgets = ID_NINGUNO;
        $id_pestanya_seleccionada_actualizacion_periodica = false;
        $ids_pestanyas_widgets = array();
        $nombres_pestanyas_widgets = array();
        while ($fila_pestanya_widgets = $res_pestanyas_widgets->dame_siguiente_fila())
        {
            if ($id_primera_pestanya_widgets == ID_NINGUNO)
            {
                $id_primera_pestanya_widgets = $fila_pestanya_widgets['id'];
            }
            $id_pestanya_widgets = $fila_pestanya_widgets['id'];
            $nombre_pestanya_widgets = $fila_pestanya_widgets['nombre'];
            $actualizacion_periodica_rotatoria = $fila_pestanya_widgets['actualizacion_periodica_rotatoria'];
            if ($actualizacion_periodica_rotatoria == VALOR_SI)
            {
                if ($id_pestanya_seleccionada == ID_NINGUNO)
                {
                    $id_pestanya_seleccionada = $fila_pestanya_widgets['id'];
                }
                array_push($ids_pestanyas_widgets, $id_pestanya_widgets);
                array_push($nombres_pestanyas_widgets, $nombre_pestanya_widgets);
                if ($id_pestanya_widgets == $id_pestanya_seleccionada)
                {
                    $id_pestanya_seleccionada_actualizacion_periodica = true;
                }
            }
        }
        if ($id_primera_pestanya_widgets != ID_NINGUNO)
        {
            if ($id_pestanya_seleccionada_actualizacion_periodica == false)
            {
                if ($id_pestanya_seleccionada == ID_NINGUNO)
                {
                    $id_pestanya_seleccionada = $id_primera_pestanya_widgets;
                }
                $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya_seleccionada);
                $ids_pestanyas_widgets = array($id_pestanya_seleccionada);
                $nombres_pestanyas_widgets = array($fila_pestanya_widgets["nombre"]);
            }
        }

        $info_pestanyas_widgets = array(
            "ids_pestanyas" => $ids_pestanyas_widgets,
            "nombres_pestanyas" => $nombres_pestanyas_widgets);
        return ($info_pestanyas_widgets);
    }


    // Devuelve los números de columnas de los widgets de la pestaña de widgets
    function dame_numeros_columnas_widgets_pestanya_widgets($id_pestanya)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_widgets = "
            SELECT numero_columnas
            FROM widgets
            WHERE
                pestanya = '".$bd_red->_($id_pestanya)."'
            ORDER BY posicion ASC";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }
        $numeros_columnas_widgets = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            array_push($numeros_columnas_widgets, $fila_widget["numero_columnas"]);
        }
        return ($numeros_columnas_widgets);
    }


    // Devuelve el contenido de una pestaña de widgets vacía
    function dame_contenido_pestanya_widgets_vacia($id_pestanya)
    {
        $idiomas = new Idiomas();
        $parametros_tabla = array(
            "opciones" => dame_botones_pestanya_widgets($id_pestanya)
        );
        $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya);
        $nombre_pestanya_widgets = $fila_pestanya_widgets["nombre"];
        $tabla = new TablaDatos(
            "tabla-personal-widgets__" + $id_pestanya,
            $nombre_pestanya_widgets,
            TIPO_TABLA_DATOS_CONTENEDOR,
            $parametros_tabla
        );
        $parametros_contenido = array(
            "clase_contenido" => "contenedor-cuadricula-widgets",
            "sin_margenes" => true
        );
        $texto_cuadricula_widgets = "
            <div class='texto-cuadricula-widgets-vacia elemento-no-seleccionable'>
                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("Actualizando widgets")." ..."."
            </div>";
        $cuadricula_widgets = "<div id=cuadricula-widgets-pestanya__".$id_pestanya.">".$texto_cuadricula_widgets."</div>";
        $tabla->anyade_contenido("contenedor-cuadricula-widgets-pestanya__".$id_pestanya, $cuadricula_widgets, $parametros_contenido);

        $contenido = $tabla->dame_tabla();
        return ($contenido);
    }


    // Devuelve el contenido de la pestaña de añadir pestaña
    function dame_contenido_pestanya_widgets_anyadir_pestanya()
    {
        $idiomas = new Idiomas();
        $tabla = new TablaDatos(
            "tabla-personal-widgets-anyadir-pestanya",
            $idiomas->_("Widgets"),
            TIPO_TABLA_DATOS_CONTENEDOR,
            array()
        );
        $parametros_contenido = array(
            "sin_margenes" => true
        );
        $texto_sin_pestanyas_widgets = "
            <div class='texto-cuadricula-widgets-vacia'>
                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay pestañas de widgets configuradas")."
            </div>";
        $contenedor_texto_sin_pestanyas_widgets = "<div>".$texto_sin_pestanyas_widgets."</div>";
        $tabla->anyade_contenido("", $contenedor_texto_sin_pestanyas_widgets, $parametros_contenido);

        $contenido = $tabla->dame_tabla();
        return ($contenido);
    }


    // Funciones los botones de una pestaña de widgets
    function dame_botones_pestanya_widgets($id_pestanya)
    {
        // Botones visibles siempre
        $boton_ayuda_tabla_widgets = "<i id='ayuda_cuadricula_widgets__".$id_pestanya."' class='icon-question-sign color-blanco boton_personal_ayuda_tabla_widgets boton-tabla-datos'></i>";
        $boton_actualizar_cuadricula_widgets = "<i id='actualiza_cuadricula_widgets__".$id_pestanya."' class='icon-refresh color-blanco boton-tabla-datos boton_actualizar_cuadricula_widgets'></i>";
        $boton_actualizacion_periodica_cuadricula_widgets = "<i id='boton_actualizacion_periodica_cuadricula_widgets__".$id_pestanya."' class='icon-play color-blanco boton-tabla-datos boton_actualizacion_periodica_cuadricula_widgets'></i>";
        $botones = array(
            $boton_actualizacion_periodica_cuadricula_widgets,
            $boton_actualizar_cuadricula_widgets,
            $boton_ayuda_tabla_widgets
        );

        // Botones visibles sólo con administración de widgets
        $administracion_widgets = dame_administracion_widgets();
        if ($administracion_widgets == true)
        {
            $boton_mostrar_ventana_anyadir_widget = "<i id='anyade_modifica_widget' class='icon-plus color-blanco boton-tabla-datos boton_mostrar_ventana_anyadir_modificar_widget boton_mostrar_ventana_anyadir_widget'></i>";
            $boton_mostrar_ventana_duplicar_pestanya_widgets = "<i id='anyade_modifica_pestanya_widgets__".$id_pestanya."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' class='icon-copy color-blanco boton-tabla-datos boton_mostrar_ventana_anyadir_modificar_pestanya_widgets'></i>";
            $boton_mostrar_ventana_modificar_pestanya_widgets = "<i id='anyade_modifica_pestanya_widgets__".$id_pestanya."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' class='icon-pencil color-blanco boton-tabla-datos boton_mostrar_ventana_anyadir_modificar_pestanya_widgets'></i>";
            $boton_eliminar_pestanya_widgets ="<i id='elimina_pestanya_widgets__".$id_pestanya."' class='icon-remove color-blanco boton-tabla-datos boton_eliminar_pestanya_widgets'></i>";
            $botones = array(
                $boton_eliminar_pestanya_widgets,
                $boton_mostrar_ventana_duplicar_pestanya_widgets,
                $boton_mostrar_ventana_modificar_pestanya_widgets,
                $boton_mostrar_ventana_anyadir_widget,
                $boton_actualizacion_periodica_cuadricula_widgets,
                $boton_actualizar_cuadricula_widgets,
                $boton_ayuda_tabla_widgets
            );
        }

        return ($botones);
    }


    // Devuelve la cuadrícula de widgets
    function dame_cuadricula_widgets($id_pestanya)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se crea la cuadrícula de widgets
        $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya);
        $cuadricula_widgets = new CuadriculaWidgets($id_pestanya, $fila_pestanya_widgets);

        // Se añaden los widgets de la pestaña a la cuadricula de widgets
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                pestanya = '".$bd_red->_($id_pestanya)."'
            ORDER BY posicion ASC";
        $res_consulta_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_consulta_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }
        while ($fila = $res_consulta_widgets->dame_siguiente_fila())
        {
            $id = $fila['id'];
            $nombre = $fila['nombre'];
            $tipo = $fila['tipo'];
            $cadena_numero_columnas = $fila['numero_columnas'];

            $cuadricula_widgets->anyade_widget(
                $id,
                $nombre,
                $tipo,
                $cadena_numero_columnas);
        }

        return ($cuadricula_widgets);
    }


    // Devuelve la lista de pestañas de widgets del módulo especificado
    function dame_lista_pestanyas_widgets_modulo($modulo, $id_pestanya_seleccionada)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_pestanyas_widgets = "
            SELECT
                id,
                nombre
            FROM pestanyas_widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (modulo = '".$bd_red->_($modulo)."')
            ORDER BY posicion ASC";
        $res_pestanyas_widgets = $bd_red->ejecuta_consulta($consulta_pestanyas_widgets);
        if ($res_pestanyas_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas_widgets."'");
        }

        $lista_pestanyas_widgets = "";
        while ($fila_pestanya_widgets = $res_pestanyas_widgets->dame_siguiente_fila())
        {
            $lista_pestanyas_widgets .= "<option value='".$fila_pestanya_widgets['id']."'";
			if ($fila_pestanya_widgets['id'] == $id_pestanya_seleccionada)
			{
				$lista_pestanyas_widgets .= " selected";
			}
			$lista_pestanyas_widgets .= ">".htmlspecialchars($fila_pestanya_widgets['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_pestanyas_widgets);
    }


    // Devuelve la lista de pestañas de widgets del módulo especificado (para seleccionar la pestaña anterior)
    function dame_lista_pestanyas_widgets_modulo_anteriores($modulo, $posicion)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_pestanyas = "
             SELECT
                id,
                nombre,
                posicion
            FROM pestanyas_widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (modulo = '".$bd_red->_($modulo)."')
                AND (posicion <> '".$bd_red->_($posicion)."')
            ORDER BY posicion";
        $res_pestanyas = $bd_red->ejecuta_consulta($consulta_pestanyas);
        if ($res_pestanyas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas."'");
        }

        $filas_pestanyas = array();
        $id_pestanya_anterior = NULL;
        while ($fila_pestanya = $res_pestanyas->dame_siguiente_fila())
        {
            array_push($filas_pestanyas, $fila_pestanya);
            if ($fila_pestanya['posicion'] < $posicion)
            {
                $id_pestanya_anterior = $fila_pestanya['id'];
            }
        }

        $lista_pestanyas = "";
        $lista_pestanyas .= "<option value='".POSICION_PESTANYA_NINGUNA."'>".$idiomas->_("Ninguna")."</option>";
        foreach ($filas_pestanyas as $fila_pestanya)
        {
            $lista_pestanyas .= "<option value='".$fila_pestanya['posicion']."'";
			if ($fila_pestanya['id'] == $id_pestanya_anterior)
			{
				$lista_pestanyas .= " selected";
			}
			$lista_pestanyas .= ">".htmlspecialchars($fila_pestanya['nombre'], ENT_QUOTES)."</option>";
        }
        $lista_pestanyas .= "<option value='".POSICION_PESTANYA_ULTIMA."'";
        if (POSICION_PESTANYA_ULTIMA == $posicion)
        {
            $lista_pestanyas .= " selected";
        }
        $lista_pestanyas .= ">".$idiomas->_("Última")."</option>";
        return ($lista_pestanyas);
    }


    // Actualiza las posiciones de las pestañas de widgets
    function actualiza_posiciones_pestanyas_widgets_modulo($modulo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_pestanyas = "
             SELECT
                id,
                posicion
            FROM pestanyas_widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (modulo = '".$bd_red->_($modulo)."')
            ORDER BY posicion";
        $res_pestanyas = $bd_red->ejecuta_consulta($consulta_pestanyas);
        if ($res_pestanyas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas."'");
        }

        $posicion_normalizada_pestanya = 1;
        while ($fila_pestanya = $res_pestanyas->dame_siguiente_fila())
        {
            $operacion_modificacion = "
                UPDATE pestanyas_widgets
                SET
                    posicion = '".$bd_red->_($posicion_normalizada_pestanya)."'
                WHERE
                    id = '".$bd_red->_($fila_pestanya["id"])."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            $posicion_normalizada_pestanya++;
        }
    }


    // Duplica los widgets de la pestaña origen a la pestaña destino del usuario especificado
    function duplica_widgets_pestanya_widgets_usuario(
        $id_pestanya_origen,
        $id_pestanya_destino,
        $id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los widgets de la pestanya origen
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                pestanya = '".$bd_red->_($id_pestanya_origen)."'
            ORDER BY posicion ASC";
        $res_consulta_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_consulta_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        // Se recuperan las filas de widgets y se eliminan las de los widgets no visibles por el usuario
        $filas_widgets = array();
        while ($fila_widget = $res_consulta_widgets->dame_siguiente_fila())
        {
            array_push($filas_widgets, $fila_widget);
        }
        $numero_filas_widgets = count($filas_widgets);
        if (strtolower($id_usuario) != $_SESSION["id_usuario"])
        {
            $filas_widgets = dame_filas_widgets_visibles_usuario(
                $id_usuario,
                PERFIL_USUARIO_ESTANDAR,
                $filas_widgets);
        }
        $numero_filas_widgets_visibles_usuario = count($filas_widgets);

        // Se recorre cada una de las filas de widgets y se añaden a la pestaña destino
        $posicion_widget = 1;
        foreach ($filas_widgets as $fila_widget)
        {
            $id_widget = $fila_widget["id"];
            $nombre = $fila_widget["nombre"];
            $tipo = $fila_widget["tipo"];
            $parametros_tipo = $fila_widget["parametros_tipo"];
            $numero_columnas = $fila_widget["numero_columnas"];

            $operacion_insercion = "
                INSERT INTO widgets (
                    red,
                    usuario,
                    nombre,
                    posicion,
                    tipo,
                    parametros_tipo,
                    pestanya,
                    numero_columnas
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($nombre)."',
                    '".$bd_red->_($posicion_widget)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($parametros_tipo)."',
                    '".$bd_red->_($id_pestanya_destino)."',
                    '".$bd_red->_($numero_columnas)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }

            // Si el widget es de tipo imagen, se duplica la imagen
            switch ($tipo)
            {
                case TIPO_WIDGET_IMAGEN:
                {
                    $id_widget_anyadido = $bd_red->dame_id_autoincremental_ultima_insercion();
                    $id_origen_anterior = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_pestanya_origen, $id_widget));
                    $id_origen_duplicado = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_pestanya_destino, $id_widget_anyadido));
                    duplica_imagen_base_datos(ORIGEN_IMAGEN_WIDGET_IMAGEN, $id_origen_anterior, $id_origen_duplicado);
                    break;
                }
            }

            // Se incrementa la posición del widget en la pestaña
            $posicion_widget += 1;
        }

        // Se devuelven el número de widgets no duplicados
        $numero_widgets_no_duplicados = ($numero_filas_widgets - $numero_filas_widgets_visibles_usuario);
        return ($numero_widgets_no_duplicados);
    }


    // Devuelve las opciones de pantalla completa de la pestaña de widgets
    function dame_opciones_pantalla_completa_pestanya_widgets($id_pestanya)
    {
        $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya);
        $opciones_pantalla_completa = dame_nombres_valores_parametros_opciones_pantalla_completa_pestanya_widgets(
            $fila_pestanya_widgets["parametros_opciones_pantalla_completa"]);
        return ($opciones_pantalla_completa);
    }


    //
    // Funciones de obtención de información de pestañas de widgets
    //


    // Devuelve la fila de la pestaña de widgets
    function dame_fila_pestanya_widgets($id_pestanya)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_pestanya_widgets = "
            SELECT *
            FROM pestanyas_widgets
            WHERE
                id = '".$bd_red->_($id_pestanya)."'";
        $res_pestanya_widgets = $bd_red->ejecuta_consulta($consulta_pestanya_widgets);
        if (($res_pestanya_widgets == false) || ($res_pestanya_widgets->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_pestanya_widgets."'");
        }
        $fila_pestanya_widgets = $res_pestanya_widgets->dame_siguiente_fila();
        return ($fila_pestanya_widgets);
    }


    //
    // Funciones de permisos de usuario
    //


    // Devuelve los identificadores de las pestañas de widgets del usuario actual
    function dame_ids_pestanyas_widgets_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de pestañas de widgets
        $consulta_pestanyas_widgets = "
            SELECT id
            FROM pestanyas_widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_pestanyas_widgets = $bd_red->ejecuta_consulta($consulta_pestanyas_widgets);
        if ($res_pestanyas_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas_widgets."'");
        }
        $ids_pestanyas_widgets = array();
        while ($fila_pestanya_widgets = $res_pestanyas_widgets->dame_siguiente_fila())
        {
            $id_pestanya_widgets = $fila_pestanya_widgets['id'];
            array_push($ids_pestanyas_widgets, $id_pestanya_widgets);
        }
        return ($ids_pestanyas_widgets);
    }


    //
    // Obtención de parámetros de pestaña de widgets
    //


    // Devuelve los nombres y valores de los parámetros de apariencia de pestaña de pestaña de widgets
    function dame_nombres_valores_parametros_apariencia_pestanya_pestanya_widgets($cadena_parametros_apariencia_pestanya)
    {
        $parametros_apariencia_pestanya = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_apariencia_pestanya);
        $nombres_valores_parametros_apariencia_pestanya = array();
        $nombres_valores_parametros_apariencia_pestanya["imagen_fondo"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_IMAGEN_FONDO];
        $nombres_valores_parametros_apariencia_pestanya["nombre_imagen_fondo"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_NOMBRE_IMAGEN_FONDO];
        $nombres_valores_parametros_apariencia_pestanya["mostrar_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["mostrar_hora_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_HORA_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["color_hora_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_HORA_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["mostrar_fecha_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_FECHA_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["color_fecha_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_FECHA_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["mostrar_titulo_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_TITULO_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["color_titulo_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_TITULO_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["prefijo_titulo_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_PREFIJO_TITULO_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["color_prefijo_titulo_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_PREFIJO_TITULO_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["sufijo_titulo_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_SUFIJO_TITULO_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["color_sufijo_titulo_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_SUFIJO_TITULO_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["numero_lineas_separacion_cabecera"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_NUMERO_LINEAS_SEPARACION_CABECERA];
        $nombres_valores_parametros_apariencia_pestanya["modificar_color_titulo_filas_widgets"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MODIFICAR_COLOR_TITULO_FILAS_WIDGETS];
        $nombres_valores_parametros_apariencia_pestanya["color_titulo_filas_widgets"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_TITULO_FILAS_WIDGETS];
        $nombres_valores_parametros_apariencia_pestanya["mostrar_pie"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_PIE];
        $nombres_valores_parametros_apariencia_pestanya["numero_lineas_separacion_pie"] = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_NUMERO_LINEAS_SEPARACION_PIE];
        return ($nombres_valores_parametros_apariencia_pestanya);
    }


    // Devuelve los nombres y valores de los parámetros de apariencia de widgets de pestaña de widgets
    function dame_nombres_valores_parametros_apariencia_widgets_pestanya_widgets($cadena_parametros_apariencia_widgets)
    {
        $parametros_apariencia_widgets = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_apariencia_widgets);
        $nombres_valores_parametros_apariencia_widgets = array();
        $nombres_valores_parametros_apariencia_widgets["mostrar_opciones"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_OPCIONES];
        $nombres_valores_parametros_apariencia_widgets["mostrar_fechas"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_FECHAS];
        $nombres_valores_parametros_apariencia_widgets["mostrar_botones"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_BOTONES];
        $nombres_valores_parametros_apariencia_widgets["estilo_fuente"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_ESTILO_FUENTE];
        $nombres_valores_parametros_apariencia_widgets["modificar_borde"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MODIFICAR_BORDE];
        $nombres_valores_parametros_apariencia_widgets["mostrar_borde"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_BORDE];
        $nombres_valores_parametros_apariencia_widgets["color_borde"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_BORDE];
        $nombres_valores_parametros_apariencia_widgets["modificar_colores_titulo"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MODIFICAR_COLORES_TITULO];
        $nombres_valores_parametros_apariencia_widgets["color_titulo"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_TITULO];
        $nombres_valores_parametros_apariencia_widgets["color_fondo_titulo"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_FONDO_TITULO];
        $nombres_valores_parametros_apariencia_widgets["transparencia_fondo_titulo"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_FONDO_TITULO];
        $nombres_valores_parametros_apariencia_widgets["modificar_colores"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MODIFICAR_COLORES];
        $nombres_valores_parametros_apariencia_widgets["color"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR];
        $nombres_valores_parametros_apariencia_widgets["color_fondo"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_FONDO];
        $nombres_valores_parametros_apariencia_widgets["transparencia_fondo"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_FONDO];
        $nombres_valores_parametros_apariencia_widgets["color_icono"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_ICONO];
        $nombres_valores_parametros_apariencia_widgets["transparencia_icono"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_ICONO];
        $nombres_valores_parametros_apariencia_widgets["transparencia_fondo_graficas"] = $parametros_apariencia_widgets[INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_FONDO_GRAFICAS];
        return ($nombres_valores_parametros_apariencia_widgets);
    }


    // Devuelve los nombres y valores de los parámetros de opciones de pantalla completa de pestaña de widgets
    function dame_nombres_valores_parametros_opciones_pantalla_completa_pestanya_widgets($cadena_parametros_opciones_pantalla_completa)
    {
        $parametros_opciones_pantalla_completa = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_opciones_pantalla_completa);
        $nombres_valores_parametros_opciones_pantalla_completa = array();
        $nombres_valores_parametros_opciones_pantalla_completa["modificar"] = $parametros_opciones_pantalla_completa[INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_MODIFICAR];
        $nombres_valores_parametros_opciones_pantalla_completa["mostrar_opciones"] = $parametros_opciones_pantalla_completa[INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_MOSTRAR_OPCIONES];
        $nombres_valores_parametros_opciones_pantalla_completa["estilo_fuente_titulo"] = $parametros_opciones_pantalla_completa[INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_ESTILO_FUENTE_TITULO];
        $nombres_valores_parametros_opciones_pantalla_completa["color"] = $parametros_opciones_pantalla_completa[INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_COLOR_PANTALLA_COMPLETA];
        $nombres_valores_parametros_opciones_pantalla_completa["color_fondo"] = $parametros_opciones_pantalla_completa[INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_COLOR_FONDO_PANTALLA_COMPLETA];
        $nombres_valores_parametros_opciones_pantalla_completa["mostrar_pie_pagina"] = $parametros_opciones_pantalla_completa[INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_MOSTRAR_PIE_PAGINA];
        return ($nombres_valores_parametros_opciones_pantalla_completa);
    }
?>
