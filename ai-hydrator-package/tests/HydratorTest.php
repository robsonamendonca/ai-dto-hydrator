<?php

// 1. Ativa a tipagem estrita para o arquivo de teste
declare(strict_types=1);

// 2. Carrega o autoloader gerado pelo Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Innovation\AIHydrator\AIHydrator;
use Innovation\AIHydrator\AIProviderInterface;

/**
 * Classe de exemplo (DTO) para testar se o Hydrator preenche os dados 
 * e converte os tipos primitivos corretamente.
 */
class PedidoTeste
{
    private string $cliente;
    private int $quantidade;
    private float $valorTotal;
    private \DateTime $dataCompra;
    private bool $pago;

    // Getters para validação no teste
    public function getCliente(): string { return $this->cliente; }
    public function getQuantidade(): int { return $this->quantidade; }
    public function getValorTotal(): float { return $this->valorTotal; }
    public function getDataCompra(): \DateTime { return $this->dataCompra; }
    public function isPago(): bool { return $this->pago; }
}

/**
 * Criação de um Mock (Simulador) do Provedor de IA.
 * Isso permite testar a lógica do Hydrator sem gastar créditos de API.
 */
class MockAIProvider implements AIProviderInterface
{
    public function extractData(string $text, array $schema): array
    {
        // Simula o JSON que a IA devolveria após ler o texto bruto
        return [
            'cliente' => 'Robson Antonio',
            'quantidade' => '5',          // Vem como string da IA, o Hydrator deve converter para int
            'valorTotal' => '249.90',     // Vem como string da IA, o Hydrator deve converter para float
            'dataCompra' => '2026-06-16', // O Hydrator deve transformar em objeto DateTime
            'pago' => 'true'              // O Hydrator deve transformar em boolean true
        ];
    }
}

// ==========================================
// EXECUÇÃO DO TESTE (Script de Assertions)
// ==========================================

echo "====== INICIANDO TESTE DO AI-HYDRATOR ======\n\n";

try {
    $mockProvider = new MockAIProvider();
    $hydrator = new AIHydrator($mockProvider);

    $textoBruto = "Texto fictício enviado para o Mock da IA";
    
    // Executa a hidratação da classe alvo
    /** @var PedidoTeste $pedido */
    $pedido = $hydrator->hydrate($textoBruto, PedidoTeste::class);

    echo "[-] Validando mapeamento de tipos...\n";

    // Asserts manuais para checar se a classe funcionou
    assert($pedido->getCliente() === 'Robson Antonio', 'Erro: Nome do cliente não bate.');
    assert($pedido->getQuantidade() === 5, 'Erro: Quantidade não foi convertida para int.');
    assert($pedido->getValorTotal() === 249.90, 'Erro: Valor total não foi convertido para float.');
    assert($pedido->getDataCompra() instanceof \DateTime, 'Erro: Data de compra não é um objeto DateTime.');
    assert($pedido->isPago() === true, 'Erro: Status de pagamento não foi convertido para boolean.');

    echo "\n[OK] SUCESSO! Todos os tipos foram mapeados e convertidos corretamente!\n";
    echo "============================================\n";

} catch (\Throwable $e) {
    echo "\n[FALHA] O teste falhou devido ao erro:\n";
    echo $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "============================================\n";
    exit(1);
}