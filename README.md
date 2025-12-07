# Chatbot PHP com Google Gemini

API PHP simples que recebe mensagens via POST e as envia para a API do Google Gemini.

## Configuração

1. Obtenha uma API Key do Google Gemini em: https://makersuite.google.com/app/apikey

2. Configure a API Key de uma das seguintes formas:

   **Opção 1: Variável de ambiente**
   ```bash
   export GEMINI_API_KEY=sua_api_key_aqui
   ```

   **Opção 2: Arquivo de configuração**
   - Copie `config.example.php` para `config.php`
   - Adicione sua API Key no arquivo `config.php`

## Uso

### Enviar uma requisição POST

```bash
curl -X POST http://localhost/api.php \
  -H "Content-Type: application/json" \
  -d '{"mensagem": "Olá, como você está?"}'
```

### Resposta de sucesso

```json
{
  "mensagem": "Olá, como você está?",
  "resposta": "Olá! Estou bem, obrigado por perguntar..."
}
```

### Resposta de erro

```json
{
  "erro": "Mensagem de erro aqui"
}
```

## Requisitos

- PHP 7.4 ou superior
- Extensão cURL habilitada
- API Key do Google Gemini

## Estrutura de arquivos

- `api.php` - Endpoint principal da API
- `config.example.php` - Exemplo de arquivo de configuração
- `README.md` - Este arquivo

