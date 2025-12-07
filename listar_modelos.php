<?php
/**
 * Script para listar modelos disponíveis do Gemini
 * Execute: php listar_modelos.php
 */

require_once 'config.php';

$apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : getenv('GEMINI_API_KEY');

if (empty($apiKey)) {
    echo "Erro: API Key não configurada\n";
    exit(1);
}

// List models from v1beta
echo "Listando modelos da API v1beta...\n";
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . urlencode($apiKey);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['models'])) {
        echo "\nModelos disponíveis (v1beta):\n";
        foreach ($data['models'] as $model) {
            $name = $model['name'] ?? 'N/A';
            $displayName = $model['displayName'] ?? 'N/A';
            $supportedMethods = implode(', ', $model['supportedGenerationMethods'] ?? []);
            echo "- {$displayName} ({$name})\n";
            echo "  Métodos suportados: {$supportedMethods}\n\n";
        }
    }
} else {
    echo "Erro ao listar modelos v1beta: HTTP {$httpCode}\n";
    echo "Resposta: {$response}\n";
}

// List models from v1
echo "\n\nListando modelos da API v1...\n";
$url = "https://generativelanguage.googleapis.com/v1/models?key=" . urlencode($apiKey);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['models'])) {
        echo "\nModelos disponíveis (v1):\n";
        foreach ($data['models'] as $model) {
            $name = $model['name'] ?? 'N/A';
            $displayName = $model['displayName'] ?? 'N/A';
            $supportedMethods = implode(', ', $model['supportedGenerationMethods'] ?? []);
            echo "- {$displayName} ({$name})\n";
            echo "  Métodos suportados: {$supportedMethods}\n\n";
        }
    }
} else {
    echo "Erro ao listar modelos v1: HTTP {$httpCode}\n";
    echo "Resposta: {$response}\n";
}


