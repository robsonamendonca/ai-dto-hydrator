<?php

namespace Innovation\AIHydrator;

interface AIProviderInterface
{
    /**
     * Envia o texto bruto e o esquema esperado para a IA, retornando um array de dados.
     *
     * @param string $text Texto não estruturado (ex: um e-mail).
     * @param array $schema Estrutura de chaves e tipos esperados.
     * @return array Dados estruturados retornados pela IA.
     */
    public function extractData(string $text, array $schema): array;
}