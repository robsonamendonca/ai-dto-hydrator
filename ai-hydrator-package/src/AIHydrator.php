<?php

namespace Innovation\AIHydrator;

use ReflectionClass;
use ReflectionProperty;
use Exception;

class AIHydrator
{
    public function __construct(
        private AIProviderInterface $provider
    ) {}

    /**
     * Transforma texto bruto em um objeto PHP tipado.
     *
     * @template T
     * @param string $text Texto não estruturado.
     * @param class-string<T> $targetClass O nome da classe que deseja instanciar.
     * @return T
     */
    public function hydrate(string $text, string $targetClass): object
    {
        if (!class_exists($targetClass)) {
            throw new Exception("A classe alvo '{$targetClass}' não existe.");
        }

        $reflection = new ReflectionClass($targetClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
        
        // 1. Mapeia as propriedades da classe e seus respectivos tipos
        $schema = [];
        foreach ($properties as $property) {
            $type = $property->getType();
            // Pega o nome do tipo (ex: string, int, float, bool)
            $schema[$property->getName()] = $type ? $type->getName() : 'string';
        }

        // 2. Solicita à IA que extraia os dados com base no esquema gerado
        $extractedData = $this->provider->extractData($text, $schema);

        // 3. Cria uma nova instância da classe alvo sem chamar o construtor (evita erros de parâmetros obrigatórios)
        $object = $reflection->newInstanceWithoutConstructor();

        // 4. Hidrata o objeto validando e convertendo os tipos básicos
        foreach ($properties as $property) {
            $name = $property->getName();
            
            if (isset($extractedData[$name])) {
                $value = $extractedData[$name];
                $property->setAccessible(true);

                // Sanitização e coerção nativa para blindar o PHP de quebras
                $expectedType = $schema[$name];
                $castedValue = $this->castValue($value, $expectedType);

                $property->setValue($object, $castedValue);
            }
        }

        return $object;
    }

    /**
     * Garante que o dado extraído pela IA seja convertido para o tipo correto do PHP.
     */
    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'string' => (string) $value,
            'DateTime', '\DateTime' => new \DateTime($value),
            default => $value, // Pode ser estendido para Enums ou sub-classes
        };
    }
}