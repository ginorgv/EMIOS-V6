<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EMIOS - Panel de Control</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0b1a0e 0%, #1a3a1a 50%, #0d2818 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
        }
        /* Header */
        .header {
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-left img {
            height: 36px;
            width: auto;
        }
        .header-left span {
            font-size: 1.2rem;
            font-weight: 600;
            background: linear-gradient(90deg, #4ade80, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.9rem;
        }
        .user-badge {
            background: rgba(74,222,128,0.12);
            border: 1px solid rgba(74,222,128,0.25);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.85rem;
            color: #4ade80;
        }
        .btn-logout {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: #ccc;
            padding: 6px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,0.2);
            border-color: rgba(239,68,68,0.3);
            color: #fca5a5;
        }
        /* Main */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }
        .main h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, #4ade80, #2dd4bf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .main p.sub {
            color: #94a3b8;
            font-size: 1.05rem;
            margin-bottom: 2.5rem;
            max-width: 500px;
        }
        /* Tarjetas de acceso rápido */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            max-width: 680px;
            width: 100%;
            margin-bottom: 2rem;
        }
        .card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 1.5rem 1rem;
            text-decoration: none;
            color: #e0e0e0;
            transition: all 0.25s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .card:hover {
            background: rgba(74,222,128,0.08);
            border-color: rgba(74,222,128,0.25);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .card .icon {
            font-size: 2rem;
        }
        .card .label {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .card .desc {
            font-size: 0.8rem;
            color: #94a3b8;
        }
        /* Info del sistema */
        .system-info {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 1rem 2rem;
            display: inline-flex;
            gap: 2rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        .system-info span strong {
            color: #ccc;
        }
        /* Login landing */
        .login-prompt {
            text-align: center;
        }
        .login-prompt h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, #4ade80, #2dd4bf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .login-prompt p {
            color: #94a3b8;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .btn-login {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            padding: 14px 40px;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 4px 20px rgba(34,197,94,0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(34,197,94,0.4);
        }
        /* Footer */
        .footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: #4a5a4a;
            border-top: 1px solid rgba(255,255,255,0.04);
        }
        @media (max-width: 600px) {
            .header { padding: 0 1rem; }
            .main h1 { font-size: 1.6rem; }
            .cards { grid-template-columns: 1fr 1fr; }
            .system-info { flex-direction: column; gap: 0.5rem; }
        }
    </style>
</head>
<body>
<?php
session_start();
$dir = __DIR__;
$_SESSION["directorio"] = $dir;

if (isset($_SESSION["id_usuario"])) {
    $nombre = $_SESSION["nombre_usuario"] ?? $_SESSION["id_usuario"];
    $perfil = $_SESSION["perfil"] ?? "";
    $id_red = $_SESSION["id_red"] ?? "Ninguna";
    $sesion = $_GET["sesion"] ?? session_id();
?>
    <header class="header">
        <div class="header-left">
            <img src="comun/rsc/imagenes/logo_web.png" alt="EMIOS" onerror="this.style.display='none'">
            <span>EMIOS</span>
        </div>
        <div class="header-right">
            <span class="user-badge"><?= htmlspecialchars($nombre) ?></span>
            <span style="color:#94a3b8;font-size:0.8rem;"><?= htmlspecialchars($perfil) ?></span>
            <a href="comun/src/login/logout.php" class="btn-logout">✕ Salir</a>
        </div>
    </header>

    <div class="main">
        <h1>Panel de Control</h1>
        <p class="sub">Bienvenido a EMIOS. Accede a los diferentes módulos del sistema.</p>

        <div class="cards">
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#administracion" class="card">
                <span class="icon">⚙️</span>
                <span class="label">Administración</span>
                <span class="desc">Gestiona usuarios, redes y configuración</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#monitorizacion" class="card">
                <span class="icon">📊</span>
                <span class="label">Monitorización</span>
                <span class="desc">Visualiza procesado y tiempos de ejecución</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#red" class="card">
                <span class="icon">🌐</span>
                <span class="label">Red</span>
                <span class="desc">Información y estado de la red</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#personal" class="card">
                <span class="icon">👤</span>
                <span class="label">Personal</span>
                <span class="desc">Tareas y configuración personal</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#sensores" class="card">
                <span class="icon">📡</span>
                <span class="label">Sensores</span>
                <span class="desc">Valores y estado de sensores</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#actuadores" class="card">
                <span class="icon">🔌</span>
                <span class="label">Actuadores</span>
                <span class="desc">Control de actuadores</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#proyectos" class="card">
                <span class="icon">📁</span>
                <span class="label">Proyectos</span>
                <span class="desc">Gestión de proyectos</span>
            </a>
            <a href="comun/index.php?sesion=<?= urlencode($sesion) ?>#smartmeter" class="card">
                <span class="icon">⚡</span>
                <span class="label">Smartmeter</span>
                <span class="desc">Facturas, tarifas y consumos</span>
            </a>
        </div>

        <div class="system-info">
            <span>Perfil: <strong><?= htmlspecialchars($perfil) ?></strong></span>
            <span>Red: <strong><?= htmlspecialchars($id_red) ?></strong></span>
            <span>EMIOS v5.4.0.6</span>
        </div>
    </div>
<?php } else { ?>
    <div class="main">
        <div class="login-prompt">
            <img src="comun/rsc/imagenes/logo_web.png" alt="EMIOS" style="height:64px;margin-bottom:1.5rem;" onerror="this.style.display='none'">
            <h1>EMIOS</h1>
            <p>Monitorización y telecontrol con análisis visual de datos<br>y motor de reglas en tiempo real.</p>
            <a href="comun/login.php" class="btn-login">Iniciar sesión</a>
        </div>
    </div>
<?php } ?>
    <div class="footer">
        EMIOS v5.4.0.6 r1 &copy; EnergyMinus
    </div>
</body>
</html>
