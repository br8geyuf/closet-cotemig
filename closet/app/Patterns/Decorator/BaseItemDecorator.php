<?php

namespace App\Patterns\Decorator;

/**
 * Decorador base para itens
 * 
 * Implementa a funcionalidade básica de decoração,
 * permitindo que decoradores específicos se concentrem
 * apenas em suas funcionalidades específicas.
 */
abstract class BaseItemDecorator implements ItemDecoratorInterface
{
    /**
     * Dados do item sendo decorado
     */
    protected array $itemData;

    /**
     * Próximo decorador na cadeia (se houver)
     */
    protected ?ItemDecoratorInterface $nextDecorator = null;

    /**
     * Construtor
     * 
     * @param array $itemData
     * @param ItemDecoratorInterface|null $nextDecorator
     */
    public function __construct(array $itemData, ?ItemDecoratorInterface $nextDecorator = null)
    {
        $this->itemData = $itemData;
        $this->nextDecorator = $nextDecorator;
    }

    /**
     * Obtém os dados do item, incluindo decorações
     * 
     * @return array
     */
    public function getData(): array
    {
        // Começa com os dados base
        $data = $this->itemData;

        // Aplica decorações do próximo decorador se existir
        if ($this->nextDecorator) {
            $data = array_merge($data, $this->nextDecorator->getData());
        }

        // Aplica as decorações deste decorador
        $additionalInfo = $this->getAdditionalInfo();
        if (!empty($additionalInfo)) {
            $data['decorations'] = $data['decorations'] ?? [];
            $data['decorations'][$this->getDecoratorName()] = $additionalInfo;
        }

        return $data;
    }

    /**
     * Obtém a prioridade padrão (pode ser sobrescrita)
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return 100; // Prioridade média
    }

    /**
     * Verifica se deve ser aplicado (implementação padrão)
     * 
     * @param array $itemData
     * @return bool
     */
    public function shouldApply(array $itemData): bool
    {
        return true; // Por padrão, aplica a todos os itens
    }

    /**
     * Define o próximo decorador na cadeia
     * 
     * @param ItemDecoratorInterface $decorator
     * @return void
     */
    public function setNextDecorator(ItemDecoratorInterface $decorator): void
    {
        $this->nextDecorator = $decorator;
    }

    /**
     * Obtém o próximo decorador na cadeia
     * 
     * @return ItemDecoratorInterface|null
     */
    public function getNextDecorator(): ?ItemDecoratorInterface
    {
        return $this->nextDecorator;
    }

    /**
     * Método abstrato que deve ser implementado pelos decoradores específicos
     * 
     * @return array
     */
    abstract public function getAdditionalInfo(): array;

    /**
     * Método abstrato que deve ser implementado pelos decoradores específicos
     * 
     * @return string
     */
    abstract public function getDecoratorName(): string;
}
