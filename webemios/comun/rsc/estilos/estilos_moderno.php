<?php
header('content-type:text/css');
session_start();

include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');

// Forzar tema verde (independientemente de preferencias)
$_SESSION["tema"] = TEMA_DEFECTO;
$_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_DEFECTO_OSCURO;
$_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_DEFECTO_INTERMEDIO;
$_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_DEFECTO_CLARO;
$_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_DEFECTO_FONDO;
$_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_DEFECTO_FONDO_CLARO;
$co = $_SESSION["colores"]["color_tema_oscuro"];
$ci = $_SESSION["colores"]["color_tema_intermedio"];
$cc = $_SESSION["colores"]["color_tema_claro"];
$ts = $_SESSION["tamanyo_letra"] ?? TAMANYO_LETRA_DEFECTO;
?>

:root {
    --primary: <?php echo $co; ?>;
    --primary-light: <?php echo $ci; ?>;
    --primary-lighter: <?php echo $cc; ?>;
    --bg-body: #eef1f5; --bg-card: #ffffff;
    --text-primary: #111827; --text-secondary: #4b5563;
    --text-muted: #6b7280; --border-color: #d1d5db;
    --header-height: 60px; --subnav-height: 46px; --footer-height: 46px;
    --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px;
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
    --shadow-md: 0 6px 20px rgba(0,0,0,0.07);
    --shadow-lg: 0 12px 40px rgba(0,0,0,0.1);
    --transition: all 0.25s ease;
}

body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
    background: var(--bg-body) !important;
    color: var(--text-primary) !important;
    font-size: <?php echo $ts; ?>px !important;
    margin: 0 !important; padding: 0 !important;
}
#contenedor {
    background: transparent !important; width: 100% !important;
    max-width: 100% !important; min-width: 0 !important;
    margin: 0 !important; padding: 0 !important;
}

/* ===== HEADER (dark bar with menu inside) ===== */
#banner {
    position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important;
    height: var(--header-height) !important;
    background: linear-gradient(135deg, #1b3a1b 0%, #0d260d 50%, #1a2e1a 100%) !important;
    display: flex !important; align-items: center !important;
    padding: 0 20px !important; z-index: 1000 !important;
    box-shadow: 0 2px 24px rgba(0,0,0,0.35) !important;
    float: none !important; gap: 4px !important;
}
#banner::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light), var(--primary-lighter));
}

#logo-web {
    float: none !important; padding: 0 !important;
    display: flex !important; align-items: center !important; gap: 6px !important;
    flex-shrink: 0 !important;
    margin-right: 12px !important;
}
    height: 18px !important; filter: brightness(0) invert(1); opacity: 0.95; }
.logo-texto { color: #fff; font-weight: 700; font-size: 1.1rem; display: inline; letter-spacing: 0.3px; }
.logo-texto small { font-weight: 400; color: var(--primary-lighter); font-size: 0.7rem; }

/* ===== MAIN NAV (horizontal dentro del header) ===== */
#menu-modulos {
    flex: 1 !important;
    display: flex !important; align-items: center !important;
    height: 100% !important;
    background: transparent !important;
    box-shadow: none !important; border: none !important;
    padding: 0 4px !important;
    min-width: 0 !important;
    overflow: hidden !important;
}
.menu-modulos-opciones-modulos {
    display: flex !important; align-items: center !important;
    gap: 2px !important; height: 100% !important;
    max-width: none !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    scrollbar-width: none !important;
}
.menu-modulos-opciones-modulos::-webkit-scrollbar { display: none !important; }
.menu-modulos-opcion-modulo {
    float: none !important; padding: 0 !important;
    height: 100% !important; display: flex !important; align-items: center !important;
    position: relative !important;
}
.menu-modulos-opcion-modulo::after {
    content: '' !important;
    position: absolute !important;
    right: -2px !important; top: 25% !important;
    height: 50% !important;
    width: 1px !important;
    background: rgba(255,255,255,0.1) !important;
}
.menu-modulos-opcion-modulo:last-child::after { display: none !important; }
.menu-modulos-opcion-modulo a,
.menu-modulos-opcion-modulo a:link,
.menu-modulos-opcion-modulo a:visited {
    display: flex !important; align-items: center !important; gap: 6px !important;
    padding: 0 16px !important; height: 100% !important;
    color: rgba(255,255,255,0.75) !important;
    font-size: 0.85rem !important; font-weight: 500 !important;
    text-decoration: none !important;
    border-bottom: 3px solid transparent !important;
    transition: var(--transition) !important;
    white-space: nowrap !important;
    letter-spacing: 0.2px !important;
}
.menu-modulos-opcion-modulo a:hover { color: #fff !important; background: rgba(255,255,255,0.07) !important; }
.modulo-actual a, .modulo-actual a:link, .modulo-actual a:visited {
    color: #fff !important;
    border-bottom-color: var(--primary-lighter) !important;
    font-weight: 600 !important;
    background: rgba(255,255,255,0.08) !important;
}

.menu-modulos-opcion-salir {
    display: flex !important; align-items: center !important; gap: 5px !important;
    color: #f87171 !important; font-size: 0.82rem !important;
    cursor: pointer !important; white-space: nowrap !important;
    padding: 5px 12px !important; border-radius: 6px !important;
    transition: var(--transition) !important;
    flex-shrink: 0 !important;
    font-weight: 500 !important;
}
.menu-modulos-opcion-salir:hover { background: rgba(220,38,38,0.25) !important; color: #fff !important; }

/* User */
#descripcion-usuario {
    float: none !important; padding: 0 !important;
    display: flex !important; align-items: center !important;
    margin-left: auto !important; flex-shrink: 0 !important;
}
.usuario-header {
    display: flex !important; align-items: center !important; gap: 10px !important;
    padding: 4px 14px 4px 4px !important;
    border-radius: 30px !important;
    background: rgba(255,255,255,0.08) !important;
    transition: var(--transition) !important;
    cursor: pointer !important;
}
.usuario-header:hover { background: rgba(255,255,255,0.12) !important; }
.usuario-avatar {
    width: 34px !important; height: 34px !important;
    border-radius: 50% !important;
    background: var(--primary) !important;
    display: flex !important; align-items: center !important; justify-content: center !important;
    color: #fff !important; font-weight: 700 !important; font-size: 0.6rem !important;
    flex-shrink: 0 !important;
}
.usuario-info { display: flex !important; flex-direction: column !important; line-height: 1.2 !important; }
.usuario-nombre { color: #fff !important; font-size: 0.82rem !important; font-weight: 500 !important; }
.usuario-perfil { color: rgba(255,255,255,0.45) !important; font-size: 0.68rem !important; }

/* ===== SUBNAV ===== */
#subnav-bar {
    position: fixed !important; top: var(--header-height) !important;
    left: 0 !important; right: 0 !important;
    height: var(--subnav-height) !important;
    background: var(--bg-card) !important;
    border-bottom: 1px solid var(--border-color) !important;
    display: flex !important; align-items: center !important;
    padding: 0 24px !important; z-index: 999 !important;
    box-shadow: var(--shadow-sm) !important; gap: 20px !important;
}
.subnav-network {
    display: flex !important; align-items: center !important; gap: 10px !important;
    font-size: 0.84rem !important; font-weight: 600 !important;
    color: #111827 !important;
    padding-right: 20px !important;
    border-right: 1px solid var(--border-color) !important;
}
.network-dot {
    width: 6px !important; height: 6px !important;
    border-radius: 50% !important; background: var(--primary) !important;
    animation: pulse 2s infinite !important;
    box-shadow: 0 0 6px rgba(41,139,33,0.4) !important;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

.subnav-sections {
    display: flex !important; align-items: center !important;
    gap: 2px !important; height: 100% !important;
    flex: 1 !important;
}
.subnav-sections a {
    display: flex !important; align-items: center !important; gap: 6px !important;
    padding: 0 16px !important; height: 100% !important;
    color: #4b5563 !important;
    font-size: 0.84rem !important; font-weight: 500 !important;
    text-decoration: none !important;
    border-bottom: 2px solid transparent !important;
    transition: var(--transition) !important;
}
.subnav-sections a:hover { color: #111827 !important; background: var(--bg-body) !important; }
.subnav-sections a.active {
    color: var(--primary) !important;
    font-weight: 600 !important;
    border-bottom-color: var(--primary) !important;
}

/* ===== CONTENT ===== */
#contenido {
    margin-top: calc(var(--header-height) + var(--subnav-height)) !important;
    padding: 0 16px 24px 0 !important;
    width: 100% !important;
    min-height: calc(100vh - var(--header-height) - var(--subnav-height) - var(--footer-height)) !important;
    overflow: visible !important;
    box-sizing: border-box !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: flex-start !important;
    gap: 16px !important;
}

/* Botón de pantalla completa en header */
#boton-pantalla-completa { display: none !important; }

/* Section sidebar */
#contenedor-menu-secciones {
    float: none !important; width: 220px !important;
    flex-shrink: 0 !important;
    background: var(--bg-card) !important;
    border-radius: var(--radius-md) !important;
    box-shadow: var(--shadow-sm) !important;
    padding: 0 0 8px !important; border: 1px solid var(--border-color) !important;
    margin-right: 0 !important;
    position: sticky !important;
    top: calc(var(--header-height) + var(--subnav-height) + 20px) !important;
    margin-bottom: 20px !important;
    overflow: hidden !important;
}
/* Barra de acento superior */
#contenedor-menu-secciones::before {
    content: '' !important;
    display: block !important;
    height: 3px !important;
    background: linear-gradient(90deg, var(--primary), var(--primary-light), var(--primary-lighter)) !important;
}
#cabecera-menu-secciones {
    background: transparent !important; color: #6b7280 !important;
    font-size: 0.6rem !important; text-transform: uppercase !important;
    letter-spacing: 1.5px !important; padding: 8px 16px 4px !important;
    font-weight: 700 !important;
}
#contenido-menu-secciones { border: none !important; padding: 0 !important; min-height: 0 !important; }
#menu-secciones nav p { 
    margin: 1px 4px !important; padding: 0 !important;
}
#menu-secciones a, #menu-secciones a:link, #menu-secciones a:visited {
    display: flex !important; align-items: center !important; gap: 10px !important;
    padding: 5px 14px !important; border-radius: 6px !important;
    color: #374151 !important; font-size: 0.85rem !important;
    border-left: 3px solid transparent !important;
    text-decoration: none !important;
    transition: var(--transition) !important;
    margin: 1px 8px !important;
    line-height: 1.2 !important;
    position: relative !important;
}
#menu-secciones a:hover { 
    background: #f3f4f6 !important; 
    color: #111827 !important; 
    border-left-color: #d1d5db !important;
}
.seccion-actual a, .seccion-actual a:link, .seccion-actual a:visited {
    color: var(--primary) !important; font-weight: 700 !important;
    border-left-color: var(--primary) !important;
    background: rgba(41,139,33,0.06) !important;
}
/* Efecto de brillo en item activo */
.seccion-actual a::after {
    content: '' !important;
    position: absolute !important;
    right: 8px !important; top: 50% !important;
    transform: translateY(-50%) !important;
    width: 6px !important; height: 6px !important;
    border-radius: 50% !important;
    background: var(--primary) !important;
    opacity: 0.5 !important;
}

#contenedor-contenido-seccion { width: auto !important; float: none !important; padding: 0 !important; flex: 1 !important; min-width: 0 !important; }
#contenido-seccion { padding: 0 !important; }

/* ===== FOOTER ===== */
#pie-pagina {
    background: linear-gradient(135deg, #1b3a1b 0%, #0d260d 50%, #1a2e1a 100%) !important;
    height: var(--footer-height) !important;
    display: flex !important; align-items: center !important;
    justify-content: space-between !important; padding: 0 24px !important;
    color: rgba(255,255,255,0.55) !important; font-size: 0.8rem !important;
    clear: both !important;
    position: relative !important;
    border-top: 1px solid rgba(255,255,255,0.05) !important;
}
#pie-pagina .span2, #pie-pagina .span8, #pie-pagina .span1 {
    width: auto !important; float: none !important; margin: 0 !important;
    display: flex !important; align-items: center !important;
}
#pie-pagina p { padding: 0 !important; margin: 0 !important; line-height: 1 !important; }
#descripcion-perfil { color: #fff !important; font-weight: 600 !important; }
#texto-pie-pagina { color: rgba(255,255,255,0.7) !important; }
.texto-fijo-pie-pagina { color: rgba(255,255,255,0.7) !important; }

/* ===== TABLES & CARDS ===== */
.tabla-datos, .contenedor-tabla-datos {
    background: var(--bg-card) !important;
    border-radius: 0 !important;
    box-shadow: var(--shadow-sm) !important;
    margin-bottom: 8px !important;
    border: 1px solid var(--border-color) !important;
    float: none !important;
    transition: var(--transition) !important;
}
/* overflow hidden solo para tablas tipo lista, no para cards con formularios/dropdowns */
.tabla-datos-lista {
    overflow: hidden !important;
}
.tabla-datos:hover { box-shadow: var(--shadow-md) !important; }
.titulo-tabla-datos {
    background: transparent !important;
    color: #111827 !important;
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    border: none !important;
    border-bottom: 1px solid var(--border-color) !important;
    padding: 2px 14px !important;
    border-radius: 0 !important;
}
.titulo-tabla-datos-contenido-oculto { border-radius: var(--radius-md) !important; }
.texto-titulo-tabla-datos { padding-left: 0 !important; }
.cabecera-tabla-datos {
    border: none !important;
    color: #374151 !important;
    font-size: 0.72rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    background: #f3f4f6 !important;
    padding: 8px 14px !important;
    font-weight: 700 !important;
    border-bottom: 2px solid #d1d5db !important;
}
.contenido-fila-tabla-datos {
    padding: 8px 14px !important;
    border-bottom: 1px solid #e5e7eb !important;
    background: #fff !important;
    color: #1f2937 !important;
    font-size: 0.85rem !important;
    line-height: 1.4 !important;
    display: flex !important;
    align-items: center !important;
}
.contenido-fila-tabla-datos:hover { background: rgba(41,139,33,0.04) !important; }
/* Sin hover en filas de filtro */
.contenido-fila-tabla-datos[id*="filtro"]:hover { background: #fff !important; }
/* Sin hover en fila de selección de localización */
.contenido-fila-tabla-datos#fila_seleccion-localizacion-actual:hover { background: #fff !important; }
.detalle-tabla-datos { margin-top: 0 !important; }
/* Opciones de fila (iconos editar/eliminar) */
.contenedor-opciones-fila-tabla-datos {
    padding: 2px 4px !important;
    float: none !important;
}
.contenedor-datos-fila-tabla-datos-con-opciones,
.contenedor-datos-fila-tabla-datos-sin-opciones {
    float: none !important;
}
.contenedor-opciones-fila-tabla-datos i {
    color: var(--primary) !important;
    font-size: 1rem !important;
    margin: 0 3px !important;
    transition: opacity 0.2s ease !important;
}
.contenedor-opciones-fila-tabla-datos i.icon-remove {
    color: #dc2626 !important;
}
.contenedor-opciones-fila-tabla-datos i:hover {
    opacity: 0.6 !important;
}
/* Columnas Cliente y Nombre alineadas a la izquierda */
.dato-tabla-datos:nth-child(1),
.dato-tabla-datos:nth-child(2) {
    text-align: left !important;
    padding-left: 8px !important;
}
/* Mejor distribución de ancho de columnas */
.dato-tabla-datos:nth-child(1) { width: 40% !important; }
.dato-tabla-datos:nth-child(2) { width: 30% !important; }
.dato-tabla-datos:nth-child(3) { width: 10% !important; }
.dato-tabla-datos:nth-child(4) { width: 10% !important; }
.dato-tabla-datos:nth-child(5) { width: 10% !important; }

/* Iconos superiores derecha de tabla en verde */
.titulo-tabla-datos .opciones-tabla-datos i,
.opciones-tabla-datos i {
    color: var(--primary) !important;
    font-size: 1.1rem !important;
}
.titulo-tabla-datos .opciones-tabla-datos i:hover,
.opciones-tabla-datos i:hover {
    color: var(--primary-light) !important;
}
/* Quitar bordes laterales verdes de estilos.php, mantener solo borde inferior sutil */
.contenedor-fila-tabla-datos {
    border-left: none !important;
    border-right: none !important;
    border-bottom: 1px solid #e5e7eb !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}
.contenedor-ultima-fila-tabla-datos {
    border-bottom: none !important;
}
.contenedor-fila-tabla-datos-par,
.contenedor-fila-tabla-datos-impar { background: #ffffff !important; }

/* Tabla contenedor datos */
.contenedor-datos-tabla-datos {
    border: none !important;
    padding: 0 !important;
}

/* ===== TABS (Bootstrap nav-tabs) ===== */
.nav-tabs {
    border-bottom: 1px solid #d1d5db !important;
    margin-bottom: 0 !important;
    background: transparent !important;
    padding: 0 8px !important;
}
.nav-tabs > li { margin-bottom: -1px !important; }
.nav-tabs > li > a {
    border: none !important;
    color: #6b7280 !important;
    font-size: 0.82rem !important;
    padding: 10px 20px !important;
    margin-right: 2px !important;
    border-radius: 0 !important;
    border-bottom: 2px solid transparent !important;
    transition: all 0.2s ease !important;
    background: transparent !important;
    font-weight: 500 !important;
    letter-spacing: 0.2px !important;
}
.nav-tabs > li > a:hover {
    color: #111827 !important;
    background: #f3f4f6 !important;
    border-color: transparent !important;
}
.nav-tabs > li.active > a,
.nav-tabs > li.active > a:hover,
.nav-tabs > li.active > a:focus {
    border: none !important;
    border-bottom: 2px solid var(--primary) !important;
    color: var(--primary) !important;
    font-weight: 600 !important;
    background: transparent !important;
}

/* ===== TAB-CONTENT ===== */
.tab-content { padding: 0 !important; }
.tab-pane { padding: 0 !important; }





/* Compensar offset del sticky del sidebar */
#contenedor-contenido-seccion {
    position: relative !important;
    top: 20px !important;
}

/* Alinear sidebar con contenido cuando hay pestañas */
#contenido:has(.nav-tabs) #contenedor-menu-secciones {
    margin-top: 32px !important;
}

/* ===== CONTENEDOR DE DATOS ===== */
.contenedor-datos-tabla-datos-sin-margenes,
.contenedor-datos-tabla-datos-sin-pie {
    padding: 0 !important;
    border: none !important;
}

/* ===== TOOLBARS ===== */
[id*="Herramientas"],
.grupo-botones-herramientas, .toolbar {
    background: transparent !important;
    padding: 8px 0 !important;
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 6px !important;
    border: none !important;
}
[id*="Herramientas"] button,
.grupo-botones-herramientas button,
.boton-herramienta,
button.boton-formulario {
    padding: 8px 16px !important;
    border-radius: var(--radius-sm) !important;
    border: 1px solid #d1d5db !important;
    background: var(--bg-card) !important;
    color: #374151 !important;
    font-size: 0.82rem !important;
    cursor: pointer !important;
    transition: var(--transition) !important;
    font-family: inherit !important;
    line-height: 1.3 !important;
    box-shadow: none !important;
    text-shadow: none !important;
    font-weight: 500 !important;
}
[id*="Herramientas"] button:hover,
.grupo-botones-herramientas button:hover,
.boton-herramienta:hover {
    border-color: var(--primary) !important;
    color: var(--primary) !important;
    background: rgba(41,139,33,0.03) !important;
}

/* ===== TITLES WITH BACKGROUNDS ===== */
.titulo-tabla-datos,
[id*="cabecera"] {
    background: transparent !important;
}
/* Override any hardcoded green backgrounds in titles */
.titulo-tabla-datos, .contenedor-detalle-tabla-datos .titulo-tabla-datos,
.pestanyas-embebidas .titulo-tabla-datos {
    background: transparent !important;
}
.titulo-tabla-datos-contenido-desplegable {
    cursor: pointer !important;
}
.titulo-tabla-datos-contenido-desplegable:hover {
    background: var(--bg-body) !important;
}
.opciones-tabla-datos {
    color: #6b7280 !important;
}

/* ===== INFORMATION CONTAINERS ===== */
[id*="tabla-contenido-seccion-error"] {
    background: #fff5f5 !important;
    border: 1px solid #fecaca !important;
    border-radius: var(--radius-sm) !important;
    padding: 16px !important;
}

/* ===== LISTS IN TABLES ===== */
.tabla-datos ul, .tabla-datos li {
    list-style: none !important;
    padding: 2px 0 !important;
    margin: 0 !important;
}

/* ===== SIDEBAR INTERNAL PADDING ===== */
#margen-inferior-menu-secciones { height: 0 !important; }

/* ===== CHOSEN SELECTS ===== */
.chosen-select, select {
    border: 1px solid var(--border-color) !important;
    border-radius: var(--radius-sm) !important;
    padding: 6px 10px !important;
    font-size: 0.85rem !important;
    background: var(--bg-card) !important;
    transition: var(--transition) !important;
}
.chosen-select:focus, select:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(41,139,33,0.1) !important;
}
/* Fix chosen dropdown list - ensure it's visible */
.chosen-container .chosen-drop {
    z-index: 9999 !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 0 0 var(--radius-sm) var(--radius-sm) !important;
    box-shadow: var(--shadow-md) !important;
    background: var(--bg-card) !important;
}
.chosen-container .chosen-results {
    max-height: 240px !important;
    padding: 4px !important;
}
.chosen-container .chosen-results li {
    padding: 6px 10px !important;
    border-radius: 4px !important;
    font-size: 0.85rem !important;
    line-height: 1.3 !important;
}
.chosen-container .chosen-results li:hover {
    background: var(--bg-body) !important;
}
.chosen-container .chosen-results li.highlighted {
    background: rgba(41,139,33,0.1) !important;
    color: var(--primary) !important;
}
.chosen-container .chosen-single {
    border: 1px solid var(--border-color) !important;
    border-radius: var(--radius-sm) !important;
    padding: 6px 10px !important;
    font-size: 0.85rem !important;
    background: var(--bg-card) !important;
    box-shadow: none !important;
    height: auto !important;
    line-height: 1.3 !important;
}
.chosen-container-active .chosen-single {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(41,139,33,0.1) !important;
}

/* ===== FILTROS ===== */
.elemento-filtro label {
    font-size: 0.8rem !important;
    font-weight: 500 !important;
    color: #4b5563 !important;
}

/* ===== PESTAÑAS EMBEBIDAS ===== */
.pestanyas-embebidas .titulo-tabla-datos {
    background: transparent !important;
}

/* ===== SELECCIÓN DE RED ===== */
#tabla-administracion-seleccion-red > .tabla-datos {
    background: var(--bg-card) !important;
    border-radius: var(--radius-md) !important;
    box-shadow: var(--shadow-sm) !important;
    border: none !important;
}
#tabla-administracion-seleccion-red .titulo-tabla-datos {
    max-width: 85% !important;
    box-sizing: border-box !important;
}
#fila_seleccion-red {
    background: #f8fafc !important;
    border-radius: 6px !important;
    padding: 8px 14px !important;
    margin: 4px 4px !important;
    max-width: 85% !important;
    box-sizing: border-box !important;
}
}

/* ===== TEXTOS ===== */
.texto-contenido-seccion-error { color: #dc2626 !important; font-size: 0.9rem !important; }
.elemento-oculto { display: none !important; }

/* ===== SPINNER MODAL ===== */
.spinner-modal {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 30px !important;
    background: transparent !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15) !important;
}
.spinner-circle {
    width: 44px !important;
    height: 44px !important;
    border: 4px solid rgba(41,139,33,0.15) !important;
    border-top-color: var(--primary) !important;
    border-radius: 50% !important;
    animation: spinner-rotate 0.7s linear infinite !important;
}
@keyframes spinner-rotate {
    to { transform: rotate(360deg); }
}
.blockUI.blockOverlay {
    background: rgba(0,0,0,0.25) !important;
    opacity: 1 !important;
}
/* Eliminar cualquier rastro del progress GIF */
img[src*="progress.gif"] { display: none !important; }

/* Prevenir bloqueo por overlays residuales: el overlay solo bloquea si el modal está visible */
#popup_overlay {
    pointer-events: none !important;
}
#popup_overlay ~ #popup_container {
    pointer-events: auto !important;
}

/* ===== MODAL ===== */
.modal { border-radius: var(--radius-lg) !important; border: none !important; box-shadow: var(--shadow-lg) !important; }
.modal-header { background: linear-gradient(135deg, #1b3a1b 0%, #0d260d 50%, #1a2e1a 100%) !important; color: #fff !important; border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important; padding: 16px 20px !important; border: none !important; }
.modal-header h3 { color: #fff !important; font-size: 1.1rem !important; }
.modal-body { padding: 20px !important; }

/* ===== JALERT POPUPS ===== */
#popup_container {
    border-radius: var(--radius-lg) !important;
    border: 1px solid var(--border-color) !important;
    box-shadow: var(--shadow-lg) !important;
    background: var(--bg-card) !important;
    font-family: 'Segoe UI', system-ui, sans-serif !important;
    min-width: 400px !important;
}
#popup_title {
    background: linear-gradient(135deg, #1b3a1b 0%, #0d260d 50%, #1a2e1a 100%) !important;
    color: #fff !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    padding: 14px 20px !important;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
    margin: 0 !important;
    text-align: left !important;
    border: none !important;
    cursor: move !important;
}
#popup_content {
    padding: 24px 20px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    margin-top: 0 !important;
}
#popup_content .icon-info-sign {
    color: var(--primary) !important;
    font-size: 1.8rem !important;
    width: auto !important;
    margin-left: 0 !important;
    float: none !important;
}
#popup_message {
    font-size: 0.9rem !important;
    color: #374151 !important;
    line-height: 1.5 !important;
    text-align: left !important;
    width: auto !important;
    float: none !important;
}
#popup_panel {
    padding: 12px 18px !important;
    text-align: right !important;
    border-top: 1px solid var(--border-color) !important;
    margin-bottom: 0 !important;
}
#popup_panel input[type=button],
#popup_panel .btn {
    padding: 8px 24px !important;
    border-radius: var(--radius-sm) !important;
    border: none !important;
    background: var(--primary) !important;
    color: #fff !important;
    font-size: 0.85rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: var(--transition) !important;
    font-family: inherit !important;
}
#popup_panel input[type=button]:hover,
#popup_panel .btn:hover {
    box-shadow: 0 4px 14px rgba(41,139,33,0.35) !important;
    transform: translateY(-1px) !important;
}

/* ===== INPUTS ===== */
input, select, textarea { border: 1.5px solid var(--primary) !important; border-radius: 0 !important; padding: 7px 10px !important; font-size: 0.85rem !important; background: var(--bg-card) !important; transition: var(--transition) !important; font-family: inherit !important; color: #111827 !important; }
input:focus, select:focus, textarea:focus { outline: none !important; border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(41,139,33,0.15) !important; }
select { cursor: pointer !important; }

/* ===== FILTROS ===== */
.filtro-informes {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin: 4px 8px 4px 0 !important;
    padding: 4px 6px !important;
    background: #f8fafc !important;
    border-radius: 0 !important;
}
.filtro-informes div {
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    color: #374151 !important;
    white-space: nowrap !important;
}
.filtro-informes select,
.filtro-informes input {
    margin: 0 !important;
    padding: 5px 8px !important;
    font-size: 0.82rem !important;
}
.filtro-informes .btn,
.filtro-informes button {
    margin: 0 !important;
}
/* Etiqueta + dropdown en horizontal */
.filtro-informes [id*="perfil"] > div,
.filtro-informes [id*="control_perfil"] {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 6px !important;
}
.contenido-fila-tabla-datos[id*="filtro"] {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 2px !important;
    padding: 6px 14px !important;
}

/* ===== TOOLTIPS ===== */
#tooltip_general, .tooltip {
    border-radius: 6px !important;
    padding: 6px 12px !important;
    font-size: 0.78rem !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    border: none !important;
    background: #1a1a2e !important;
    color: #fff !important;
}

/* ===== MODAL refinements ===== */
.modal .close {
    color: rgba(255,255,255,0.6) !important;
    opacity: 1 !important;
    font-size: 1.5rem !important;
    transition: var(--transition) !important;
}
.modal .close:hover { color: #fff !important; opacity: 1 !important; }
#boton_ayuda_ventana_modal { color: rgba(255,255,255,0.6) !important; cursor: pointer !important; }
#boton_ayuda_ventana_modal:hover { color: #fff !important; }
.modal-footer { padding: 14px 18px !important; border-top: 1px solid var(--border-color) !important; }

/* ===== LINKS ===== */
a:link, a:visited, a:active { text-decoration: none !important; color: var(--primary) !important; }
a:hover { color: var(--primary-dark, #1f6e19) !important; }

/* ===== BOTONES GENERALES ===== */
.boton-formulario, button.btn, a.btn {
    padding: 8px 18px !important;
    border-radius: var(--radius-sm) !important;
    border: 1px solid var(--border-color) !important;
    font-size: 0.85rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: var(--transition) !important;
    font-family: inherit !important;
    background: var(--bg-card) !important;
    color: var(--text-secondary) !important;
    text-shadow: none !important;
    box-shadow: none !important;
    line-height: 1.3 !important;
}
.boton-formulario:hover, button.btn:hover, a.btn:hover {
    border-color: var(--primary) !important;
    color: var(--primary) !important;
}

.boton-formulario.btn-success,
button.btn-success, a.btn-success,
input[type=submit], button[type=submit] {
    background: var(--primary) !important;
    color: #fff !important;
    border: none !important;
    text-shadow: none !important;
}
.boton-formulario.btn-success:hover,
button.btn-success:hover, a.btn-success:hover,
input[type=submit]:hover, button[type=submit]:hover {
    box-shadow: 0 4px 14px rgba(41,139,33,0.35) !important;
    transform: translateY(-1px) !important;
    color: #fff !important;
}

/* Ajuste vertical del botón de selección de localización respecto al desplegable */
#tabla-seleccion-localizacion-actual button.boton-formulario {
    margin-top: 1.8em !important;
}

/* ===== INFORMACIÓN ICONS ===== */
[class*="icon-info-sign"], [class*="icon-question-sign"] {
    color: var(--text-secondary) !important;
    opacity: 0.6 !important;
}
[class*="icon-warning-sign"] { color: #dc2626 !important; }

/* ===== MENSAJES ===== */
.jAlert_popup {
    border-radius: var(--radius-md) !important;
    border: none !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15) !important;
}
.jAlert_popup .modal-header {
    background: linear-gradient(135deg, #1b3a1b 0%, #0d260d 50%, #1a2e1a 100%) !important;
    color: #fff !important;
}
.jAlert_popup .btn {
    border-radius: var(--radius-sm) !important;
    padding: 8px 24px !important;
    font-size: 0.85rem !important;
}

/* ===== BOOTSTRAP OVERRIDES ===== */
.alert {
    border-radius: var(--radius-sm) !important;
    border: 1px solid transparent !important;
    padding: 12px 16px !important;
    font-size: 0.85rem !important;
}
.alert-success { background: #dcfce7 !important; border-color: #bbf7d0 !important; color: #16a34a !important; }
.alert-error, .alert-danger { background: #fee2e2 !important; border-color: #fecaca !important; color: #dc2626 !important; }
.alert-info { background: #dbeafe !important; border-color: #bfdbfe !important; color: #2563eb !important; }
.label, .badge {
    padding: 3px 8px !important;
    border-radius: 10px !important;
    font-size: 0.72rem !important;
    font-weight: 500 !important;
}
.label-success, .badge-success { background: #dcfce7 !important; color: #16a34a !important; }
.label-important, .badge-important { background: #fee2e2 !important; color: #dc2626 !important; }
.label-warning, .badge-warning { background: #ffedd5 !important; color: #ea580c !important; }
.label-info, .badge-info { background: #dbeafe !important; color: #2563eb !important; }
.row-fluid { width: 100% !important; }
[class*="span"] { float: none !important; }
.breadcrumb { background: transparent !important; padding: 0 !important; }
.well { background: var(--bg-card) !important; border: 1px solid var(--border-color) !important; border-radius: var(--radius-sm) !important; box-shadow: none !important; }
.progress { background: var(--bg-body) !important; border-radius: 10px !important; }
.btn-group { display: inline-flex !important; gap: 4px !important; }
.dropdown-menu {
    border-radius: var(--radius-sm) !important;
    border: 1px solid #d1d5db !important;
    box-shadow: var(--shadow-md) !important;
    padding: 4px !important;
}
.dropdown-menu > li > a {
    border-radius: 4px !important;
    padding: 6px 14px !important;
    font-size: 0.85rem !important;
    transition: var(--transition) !important;
    color: #374151 !important;
}
.dropdown-menu > li > a:hover {
    background: var(--bg-body) !important;
    color: #111827 !important;
}

/* ===== SMARTMETER ===== */
/* Botones de tipo de medición como pestañas (Electricidad, Gas, Agua) */
.seleccion-medicion {
    display: flex !important;
    gap: 0 !important;
    border-bottom: 1px solid #d1d5db !important;
    margin-bottom: 16px !important;
    padding: 0 !important;
}
.seleccion-medicion .boton-medicion {
    border: none !important;
    background: transparent !important;
    color: #6b7280 !important;
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    padding: 10px 20px !important;
    margin: 0 !important;
    border-radius: 0 !important;
    border-bottom: 2px solid transparent !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    letter-spacing: 0.2px !important;
    text-shadow: none !important;
    box-shadow: none !important;
    font-family: inherit !important;
}
.seleccion-medicion .boton-medicion:hover {
    color: #111827 !important;
    background: #f3f4f6 !important;
}
.seleccion-medicion .btn-medicion-seleccionada {
    color: var(--primary) !important;
    border-bottom: 2px solid var(--primary) !important;
    font-weight: 600 !important;
    background: transparent !important;
}
/* Iconos de tipo de medición en verde */
.seleccion-medicion .boton-medicion [class*="icon-"] {
    color: var(--primary) !important;
}
/* Sensores - grupo de selección */
.seleccion-sensores {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: flex-start !important;
    gap: 10px !important;
    padding: 8px 0 !important;
}
/* Multi-select layout: flex en fila */
.lista-sensores,
[id*="smartmeter"] .lista-sensores {
    display: block !important;
}
[id*="smartmeter"] .lista-sensores .ms2side__div,
.ms2side__div {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    align-items: stretch !important;
    gap: 4px !important;
    width: 100% !important;
}
/* Header a ancho completo */
.ms2side__header {
    width: 100% !important;
    margin-bottom: 4px !important;
    flex: 0 0 100% !important;
    order: -1 !important;
}
.ms2side__header input {
    width: 320px !important;
    min-height: 30px !important;
    box-sizing: border-box !important;
}
/* Selects comparten espacio equitativamente */
.ms2side__div .ms2side__select {
    flex: 1 1 0 !important;
    min-width: 0 !important;
    width: auto !important;
}
/* Source (origen) y target se ajustan a su contenido */
.ms2side__div .ms2side__select:nth-child(2),
.ms2side__div .ms2side__select:last-child {
    flex: 0 0 auto !important;
}
/* Selects source y target con 400px de ancho */
#ids_sensores_smartmeter_consumos_costes_generalesms2side__sx,
#ids_sensores_smartmeter_consumos_costes_generalesms2side__dx {
    width: 400px !important;
}
.ms2side__div .ms2side__select select {
    width: 100% !important;
    min-height: 150px !important;
    padding: 6px 8px !important;
    font-size: 0.85rem !important;
}
/* Opciones centradas verticalmente */
.ms2side__div .ms2side__options {
    flex: 0 0 auto !important;
    width: auto !important;
    padding: 0 !important;
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    min-height: 140px !important;
}
/* Filtro de sensores */
.seleccion-sensores input[type="text"],
[id*="smartmeter"] input[type="text"] {
    margin: 0 !important;
    padding: 5px 8px !important;
    font-size: 0.82rem !important;
}
/* Listas multi-select de sensores */
[id*="smartmeter"] select[multiple] {
    border: 1px solid var(--primary) !important;
    border-radius: 0 !important;
    padding: 4px !important;
    min-height: 120px !important;
    font-size: 0.82rem !important;
    width: 200px !important;
}
[id*="smartmeter"] select[multiple] option {
    padding: 5px 8px !important;
    margin: 1px 0 !important;
    border-radius: 0 !important;
}
[id*="smartmeter"] select[multiple] option:hover {
    background: rgba(41,139,33,0.08) !important;
}
/* Botones de navegación entre listas (>, >>, <, <<) */
.seleccion-sensores div,
[id*="smartmeter"] [class*="seleccion"] > div:not(.ms2side__div) {
    display: inline-flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    padding: 4px !important;
}
.ms2side__options p,
.seleccion-sensores p {
    cursor: pointer !important;
    padding: 8px 14px !important;
    margin: 2px 0 !important;
    border: 1px solid #d1d5db !important;
    background: var(--bg-card) !important;
    text-align: center !important;
    font-size: 1.1rem !important;
    color: #374151 !important;
    transition: all 0.2s ease !important;
    min-width: 36px !important;
    line-height: 1 !important;
}
.ms2side__options p:hover,
.seleccion-sensores p:hover {
    background: rgba(41,139,33,0.08) !important;
    border-color: var(--primary) !important;
}
/* Configuración - alineación de campos */
[id*="smartmeter"] .configuracion-fila {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
    align-items: flex-end !important;
    padding: 8px 0 !important;
}
[id*="smartmeter"] .configuracion-fila .campo {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
}
[id*="smartmeter"] .configuracion-fila label {
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    color: #374151 !important;
}
/* Secciones plegables (Horario semanal, Exclusión/Inclusión fechas) */
[id*="smartmeter"] .plegable-titulo {
    padding: 10px 14px !important;
    background: #f3f4f6 !important;
    border: 1px solid #e5e7eb !important;
    cursor: pointer !important;
    font-weight: 600 !important;
    font-size: 0.85rem !important;
    color: #111827 !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-top: 8px !important;
}
[id*="smartmeter"] .plegable-titulo:hover {
    background: #e5e7eb !important;
}
[id*="smartmeter"] .plegable-contenido {
    padding: 12px 14px !important;
    border: 1px solid #e5e7eb !important;
    border-top: none !important;
}

/* ===== GLOBAL FLOATS ===== */
.ui-draggable, .jAlert_popup { z-index: 9999 !important; }

/* ===== RESPONSIVE ===== */
@media (max-width: 1100px) {
    #contenedor-menu-secciones { width: 180px !important; margin-right: 0 !important; }
    #contenedor-contenido-seccion { width: auto !important; flex: 1 !important; }
    .menu-modulos-opcion-modulo a span { display: none; }
    #banner { padding: 0 12px !important; gap: 2px !important; }
    .menu-modulos-opcion-modulo a { padding: 0 10px !important; }
    .usuario-nombre { display: none !important; }
    .usuario-perfil { display: none !important; }
    .menu-modulos-opcion-modulo::after { display: none !important; }
}
@media (max-width: 768px) {
    #contenedor-menu-secciones { display: none !important; }
    #contenedor-contenido-seccion { width: 100% !important; float: none !important; flex: 1 !important; }
    .logo-texto { display: none !important; }
}
