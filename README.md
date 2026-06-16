# AI-Driven DTO Hydrator & Context Mapper

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue.svg)](https://packagist.org/packages/robsonamendonca/ai-dto-hydrator)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

An innovative, lightweight, and framework-agnostic PHP 8+ library designed to bridge the gap between unstructured AI responses and PHP strict typing. 

Using PHP's native **Reflection API**, this package automatically generates a type-safe schema from any Data Transfer Object (DTO) or class, prompts an AI provider to extract data from raw text, and safely hydrates the object while enforcing native PHP types (`int`, `float`, `bool`, `string`, `DateTime`).

---

## 💡 Why is it Innovative?

1. **Strict Type Enforcement:** Most AI packages only return raw strings or loose JSON. This library uses reflection to inspect your target class properties and dynamically forces the AI to output schema-matching data types.
2. **Native Type Coercion & Safety:** It shields your application from `TypeError` exceptions. The engine sanitizes and casts data before injecting it into private or protected properties.
3. **Zero Dependencies:** It does not rely on heavy frameworks like Laravel or Symfony. It can be dropped into any legacy or modern PHP project.
4. **Provider Agnostic:** Easily switch between OpenAI, Gemini, Claude, or local LLMs (like Ollama) by implementing a simple interface.

---

## 📂 Installation & Structure

Clone the repository or download the package structure:

```text
ai-hydrator-package/
├── src/
│   ├── AIHydrator.php
│   ├── AIProviderInterface.php
│   └── OpenAIProvider.php
├── tests/
│   └── HydratorTest.php
├── composer.json
└── README.md

```

If using Composer, ensure your autoloader is updated:

```bash
composer dump-autoload

```

---

## 🚀 Quick Start / Usage Example

### 1. Define your Target Class (DTO)

Create any standard PHP class using native type-hinting:

```php
class OrderDTO
{
    private string $customerName;
    private int $quantity;
    private float $totalPrice;
    private \DateTime $purchaseDate;
    private bool $isPaid;

    // Getters
    public function getCustomerName(): string { return $this->customerName; }
    public function getQuantity(): int { return $this->quantity; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getPurchaseDate(): \DateTime { return $this->purchaseDate; }
    public function isPaid(): bool { return $this->isPaid; }
}

```

### 2. Hydrate the Object from Unstructured Text

```php
use Innovation\AIHydrator\AIHydrator;
use Innovation\AIHydrator\OpenAIProvider;

// 1. Initialize the AI Provider (or any custom provider)
$provider = new OpenAIProvider('your-api-key-here');
$hydrator = new AIHydrator($provider);

// 2. Raw unstructured text (e.g., from an email or chat webhook)
$rawEmailText = "Hey team, I'm Robson. I just transferred 249.90 via Pix yesterday for those 5 t-shirts. Please confirm order.";

// 3. Magic happens here: Turn text into a strictly-typed PHP Object
/** @var OrderDTO $order */
$order = $hydrator->hydrate($rawEmailText, OrderDTO::class);

// 4. Use your object safely with full IDE autocomplete support
echo $order->getCustomerName();       // Output: Robson
echo $order->getQuantity();           // Output: 5 (as an integer)
echo $order->getPurchaseDate()->format('Y-m-d'); // Output: 2026-06-15 (as DateTime)

```

---

## 🛠️ Testing

The package includes a built-in test suite that simulates an AI response using a manual Mock, allowing you to verify the reflection engine and type coercion without spending API credits.

To run the test localy, execute:

```bash
php -d assert.active=1 tests/HydratorTest.php

```

---

## 📄 License

This project is open-source and licensed under the MIT License. Feel free to use, modify, and share.

---

*Submitted to the PHP Innovation Award on PHPClasses.org.*