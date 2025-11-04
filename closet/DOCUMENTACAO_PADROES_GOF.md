_# Documentação: Aplicação de Padrões de Projeto (GoF) no Projeto Closet Fashion_



## 1. Introdução

Este documento detalha a implementação de cinco padrões de projeto do *Gang of Four (GoF)* no sistema Closet Fashion. O objetivo da refatoração foi melhorar a arquitetura do software, aumentando sua flexibilidade, manutenibilidade, e escalabilidade, conforme os requisitos solicitados.

Os padrões implementados foram:

1.  **Singleton:** Para garantir uma instância única da conexão de banco de dados.
2.  **Factory Method:** Para desacoplar a criação de diferentes tipos de itens.
3.  **Observer:** Para criar um sistema de notificações e eventos desacoplado.
4.  **Strategy:** Para permitir a combinação dinâmica de algoritmos de filtragem.
5.  **Decorator:** Para adicionar responsabilidades e características a objetos dinamicamente.

--- 

## 2. Padrão Singleton

O padrão **Singleton** foi aplicado para gerenciar a conexão com o banco de dados, garantindo que exista apenas uma única instância do objeto de conexão em toda a aplicação. Isso evita a sobrecarga de criar múltiplas conexões, otimiza o uso de recursos e centraliza o controle de transações e estatísticas.

> "O Singleton garante que uma classe tenha apenas uma instância e fornece um ponto de acesso global a ela." [1]

### 2.1. Implementação

Foi criada a classe `DatabaseConnection` que encapsula a lógica de conexão e garante a unicidade da instância.

**Localização:** `app/Patterns/Singleton/DatabaseConnection.php`

```php
<?php

namespace App\Patterns\Singleton;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

class DatabaseConnection
{
    private static ?self $instance = null;
    private Connection $connection;

    private function __construct()
    {
        $this->connection = DB::connection();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    // ... outros métodos para transações, queries e estatísticas

    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
```

### 2.2. Justificativa e Benefícios

- **Controle Centralizado:** Todas as interações com o banco de dados podem ser monitoradas e gerenciadas a partir de um único ponto.
- **Economia de Recursos:** Evita a criação de múltiplas conexões desnecessárias, o que é especialmente custoso em aplicações web.
- **Gerenciamento de Estado Global:** Facilita o controle de transações que podem abranger diferentes partes do sistema.

--- 

## 3. Padrão Factory Method

O **Factory Method** foi utilizado para lidar com a criação de diferentes tipos de itens do closet (roupas, calçados, acessórios, etc.). Este padrão define uma interface para criar um objeto, mas deixa as subclasses decidirem qual classe instanciar.

> "O Factory Method define uma interface para criar um objeto, mas permite que as subclasses alterem o tipo de objetos que serão criados." [1]

### 3.1. Implementação

Foi criada a `ItemFactory` que, com base na categoria do item, instancia a classe de tipo correspondente. Cada tipo de item (`ClothingItem`, `ShoeItem`, etc.) implementa a interface `ItemTypeInterface`.

**Localização:** `app/Patterns/Factory/ItemFactory.php`

```php
<?php

namespace App\Patterns\Factory;

class ItemFactory
{
    private static array $TYPE_MAPPING = [
        'camiseta' => ClothingItem::class,
        'tenis' => ShoeItem::class,
        'cinto' => AccessoryItem::class,
        // ... outros mapeamentos
    ];

    public static function createItemType(string $categoryName, array $itemData = []): ItemTypeInterface
    {
        $normalizedCategory = self::normalizeCategory($categoryName);
        $className = self::$TYPE_MAPPING[$normalizedCategory] ?? AccessoryItem::class;
        return new $className($itemData);
    }
}
```

**Interface e Classes de Tipo:**
- `app/Patterns/Factory/ItemTypeInterface.php`
- `app/Patterns/Factory/ItemTypes/ClothingItem.php`
- `app/Patterns/Factory/ItemTypes/ShoeItem.php`

### 3.2. Justificativa e Benefícios

- **Desacoplamento:** O código cliente não precisa saber qual classe de item concreta está sendo instanciada. Ele apenas solicita um tipo de item através da fábrica.
- **Extensibilidade:** Adicionar novos tipos de itens (ex: "Joias") torna-se simples: basta criar a nova classe e registrá-la na fábrica, sem alterar o código cliente.
- **Lógica Centralizada:** A lógica de decisão sobre qual objeto criar fica centralizada na fábrica, seguindo o Princípio da Responsabilidade Única.

--- 

## 4. Padrão Observer

O padrão **Observer** foi implementado para criar um sistema de eventos que permite que diferentes partes do sistema reajam a ações sem estarem diretamente acopladas. Por exemplo, quando um item é criado, um observador pode atualizar estatísticas, enquanto outro pode verificar o orçamento.

> "O Observer é um padrão de projeto comportamental que permite que você defina um mecanismo de assinatura para notificar múltiplos objetos sobre quaisquer eventos que aconteçam com o objeto que eles estão observando." [1]

### 4.1. Implementação

Foram criados um `EventSubject` (o objeto observado) e múltiplos `Observers` (os observadores). O `EventSubject` mantém uma lista de observadores e os notifica quando um evento ocorre.

**Localização:**
- `app/Patterns/Observer/EventSubject.php`
- `app/Patterns/Observer/ObserverInterface.php`
- `app/Patterns/Observer/Observers/ItemObserver.php`
- `app/Patterns/Observer/Observers/BudgetObserver.php`

```php
<?php

namespace App\Patterns\Observer;

class EventSubject
{
    private array $observers = [];

    public function attach(ObserverInterface $observer): void
    {
        // ...
    }

    public function detach(ObserverInterface $observer): void
    {
        // ...
    }

    public function notify(string $event, array $data): void
    {
        foreach ($this->observers as $observer) {
            if (in_array($event, $observer->getInterestedEvents())) {
                $observer->update($event, $data);
            }
        }
    }
}
```

### 4.2. Justificativa e Benefícios

- **Baixo Acoplamento:** O *Subject* não conhece os detalhes dos *Observers*, apenas que eles implementam uma interface comum. Isso permite adicionar ou remover observadores sem alterar o *Subject*.
- **Reutilização:** Observadores podem ser reutilizados em diferentes contextos ou notificados por diferentes *Subjects*.
- **Dinamismo:** Observadores podem ser adicionados ou removidos em tempo de execução.

--- 

## 5. Padrão Strategy

O padrão **Strategy** foi usado para implementar um sistema de filtros de itens. Ele permite definir uma família de algoritmos (estratégias de filtro), encapsular cada um deles e torná-los intercambiáveis. Isso permite que o algoritmo de filtragem varie independentemente dos clientes que o utilizam.

> "O Strategy é um padrão de projeto comportamental que permite que você defina uma família de algoritmos, coloque cada um deles em uma classe separada, e faça com que seus objetos sejam intercambiáveis." [1]

### 5.1. Implementação

Foi criado um `FilterContext` que gerencia e aplica as estratégias de filtro. Cada estratégia (`CategoryFilter`, `ColorFilter`, etc.) implementa a interface `FilterStrategyInterface` e contém a lógica para aplicar um filtro específico a uma query do Eloquent.

**Localização:**
- `app/Patterns/Strategy/FilterContext.php`
- `app/Patterns/Strategy/FilterStrategyInterface.php`
- `app/Patterns/Strategy/Filters/CategoryFilter.php`
- `app/Patterns/Strategy/Filters/ColorFilter.php`

```php
<?php

namespace App\Patterns\Strategy;

class FilterContext
{
    private array $strategies = [];
    private array $activeFilters = [];

    public function addFilter(string $strategyName, $value): self
    {
        // ...
    }

    public function applyFilters(Builder $query): Builder
    {
        foreach ($this->activeFilters as $strategyName => $value) {
            if (isset($this->strategies[$strategyName])) {
                $strategy = $this->strategies[$strategyName];
                $query = $strategy->apply($query, $value);
            }
        }
        return $query;
    }
}
```

### 5.2. Justificativa e Benefícios

- **Flexibilidade:** Permite adicionar, remover ou combinar filtros dinamicamente em tempo de execução.
- **Código Limpo:** Evita condicionais complexas (`if/else` ou `switch`) no código cliente para selecionar o algoritmo de filtro.
- **Extensibilidade:** Novos filtros podem ser adicionados criando novas classes de estratégia, sem modificar o contexto ou o cliente.

--- 

## 6. Padrão Decorator

O padrão **Decorator** foi utilizado para adicionar funcionalidades e informações extras aos objetos de item de forma dinâmica, sem alterar sua estrutura. Por exemplo, um item pode ser "decorado" com informações de favoritismo, promoções ativas ou estatísticas de uso.

> "O Decorator é um padrão de projeto estrutural que permite que você acople novos comportamentos a objetos ao colocá-los dentro de invólucros de objetos especiais que contêm os comportamentos." [1]

### 6.1. Implementação

Foi criado um `ItemDecoratorManager` que gerencia a aplicação de múltiplos decoradores a um array de dados de um item. Cada decorador (`FavoriteDecorator`, `PromotionDecorator`, etc.) estende uma classe base e adiciona um conjunto específico de informações.

**Localização:**
- `app/Patterns/Decorator/ItemDecoratorManager.php`
- `app/Patterns/Decorator/ItemDecoratorInterface.php`
- `app/Patterns/Decorator/Decorators/FavoriteDecorator.php`
- `app/Patterns/Decorator/Decorators/PromotionDecorator.php`

```php
<?php

namespace App\Patterns\Decorator;

class ItemDecoratorManager
{
    public function decorateItem(array $itemData): array
    {
        $decoratedData = $itemData;

        foreach ($this->decorators as $decoratorName => $className) {
            $instance = new $className($decoratedData);
            if ($instance->shouldApply($decoratedData)) {
                $decoratedData = array_merge($decoratedData, $instance->getData());
            }
        }

        return $decoratedData;
    }
}
```

### 6.2. Justificativa e Benefícios

- **Flexibilidade:** Permite adicionar ou remover responsabilidades de um objeto em tempo de execução, o que é mais flexível que herança.
- **Evita Explosão de Subclasses:** Previne a criação de um grande número de subclasses para cada combinação de funcionalidades.
- **Responsabilidade Única:** Cada decorador foca em uma única funcionalidade, tornando o código mais limpo e fácil de manter.

--- 

## 7. Conclusão

A aplicação estratégica dos padrões de projeto GoF transformou a base de código do Closet Fashion, tornando-a mais robusta, flexível e preparada para futuras expansões. A implementação seguiu as melhores práticas, garantindo que cada padrão fosse aplicado em um contexto apropriado, agregando valor real à arquitetura do sistema.

## 8. Referências

[1] Gamma, E., Helm, R., Johnson, R., & Vlissides, J. (1994). *Design Patterns: Elements of Reusable Object-Oriented Software*. Addison-Wesley.

