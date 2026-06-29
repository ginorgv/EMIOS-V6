<?php
// Endpoint de healthcheck para Railway
// Devuelve un 200 OK sin depender de sesión ni base de datos
header('Content-Type: application/json');
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'service' => 'emios-web'
]);
