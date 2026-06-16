<?php

namespace Innovation\AIHydrator;

class OpenAIProvider implements AIProviderInterface
{
    public function __construct(
        private string $apiKey,
        private string $model = 'gpt-4o-mini'
    ) {}

    public function extractData(string $text, array $schema): array
    {
        // Transforma o array do esquema em uma string legível para a IA
        $jsonSchema = json_encode($schema);

        $prompt = "Analise o seguinte texto e extraia as informações estritamente no formato JSON solicitado.\n\n";
        $prompt .= "Esquema JSON esperado (Chave => Tipo):\n{$jsonSchema}\n\n";
        $prompt .= "Texto para análise:\n\"{$text}\"";

        // Aqui você faria a requisição cURL nativa para a OpenAI (sem usar SDKs externos para manter a classe leve)
        // [Código de envio HTTP cURL omitido para brevidade]
        
        // Simulação de retorno da IA interpretando o texto:
        return [
            'cliente' => 'Robson',
            'quantidade' => 3,
            'valorTotal' => 150.00,
            'dataCompra' => '2026-06-14' // A classe AIHydrator vai converter isso em um objeto DateTime!
        ];
    }
}