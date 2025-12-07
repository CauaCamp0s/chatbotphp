<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido. Use POST.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['erro' => 'JSON inválido']);
    exit;
}

if (!isset($data['mensagem']) || empty(trim($data['mensagem']))) {
    http_response_code(400);
    echo json_encode(['erro' => 'Campo "mensagem" é obrigatório']);
    exit;
}

$mensagem = trim($data['mensagem']);

$apiKey = getenv('GEMINI_API_KEY');
$model = getenv('GEMINI_MODEL');
$apiVersion = 'v1';

if (file_exists('config.php')) {
    require_once 'config.php';
    if (empty($apiKey) && defined('GEMINI_API_KEY')) {
        $apiKey = GEMINI_API_KEY;
    }
    if (defined('GEMINI_MODEL')) {
        $model = GEMINI_MODEL;
    }
    if (defined('GEMINI_API_VERSION')) {
        $apiVersion = GEMINI_API_VERSION;
    }
}

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['erro' => 'API Key do Gemini não configurada. Configure a variável de ambiente GEMINI_API_KEY ou crie um arquivo config.php']);
    exit;
}

$instrucaoSistema = 'Você é um especialista exclusivo em futebol. Sua única função é responder sobre futebol e TUDO relacionado a futebol: times, jogadores, campeonatos, táticas, histórias, estatísticas, transferências, jogos, copas do mundo, ligas, regras, curiosidades e qualquer assunto relacionado ao esporte. Se a pergunta não for sobre futebol, recuse educadamente e informe que você só pode falar sobre futebol.';

$geminiUrl = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$model}:generateContent?key=" . urlencode($apiKey);

$payload = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                [
                    'text' => $instrucaoSistema
                ]
            ]
        ],
        [
            'role' => 'model',
            'parts' => [
                [
                    'text' => 'Entendido. Estou pronto para responder apenas sobre futebol e tudo relacionado ao esporte.'
                ]
            ]
        ],
        [
            'role' => 'user',
            'parts' => [
                [
                    'text' => $mensagem
                ]
            ]
        ]
    ]
];

$ch = curl_init($geminiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || !empty($curlError)) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao comunicar com a API do Gemini',
        'detalhes' => $curlError
    ]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    $errorData = json_decode($response, true);
    echo json_encode([
        'erro' => 'Erro na API do Gemini',
        'codigo' => $httpCode,
        'detalhes' => $errorData ?? $response
    ]);
    exit;
}

$geminiResponse = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['erro' => 'Resposta inválida da API do Gemini']);
    exit;
}

$respostaTexto = '';
if (isset($geminiResponse['candidates'][0]['content']['parts'][0]['text'])) {
    $respostaTexto = $geminiResponse['candidates'][0]['content']['parts'][0]['text'];
} else {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Formato de resposta inesperado da API do Gemini',
        'resposta' => $geminiResponse
    ]);
    exit;
}

echo json_encode([
    'mensagem' => $mensagem,
    'resposta' => $respostaTexto
], JSON_UNESCAPED_UNICODE);

