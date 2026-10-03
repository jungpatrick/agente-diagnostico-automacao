<?php
// Proxy da YARA: repassa a requisição do navegador para o n8n,
// assim o endereço real do n8n não aparece na página.

// URL base do n8n definida em config.php (não versionado). Veja config.example.php
$configArquivo = __DIR__ . '/config.php';
if (!is_file($configArquivo)) {
    http_response_code(500);
    exit('config.php não encontrado');
}
require $configArquivo;

$DESTINO = rtrim(N8N_BASE_URL, '/') . '/webhook/agente-yara-auth';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$corpo = file_get_contents('php://input');
if (strlen($corpo) > 20000) {      // proteção contra envio de lixo
    http_response_code(413);
    exit;
}

set_time_limit(180);               // gerar o relatório leva 1-2 minutos

$ch = curl_init($DESTINO);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $corpo,
    CURLOPT_HTTPHEADER     => ['Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'application/json')],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT        => 180,
]);

$resposta = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$tipo     = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

header('Content-Type: application/json; charset=utf-8');

if ($resposta === false) {
    http_response_code(502);
    echo json_encode([
        'ok' => false,
        'mensagem' => 'Serviço indisponível no momento. Tente novamente em instantes.',
        'output' => 'Serviço indisponível no momento. Tente novamente em instantes.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code($status ?: 502);
if ($tipo) header('Content-Type: ' . $tipo);
echo $resposta;
