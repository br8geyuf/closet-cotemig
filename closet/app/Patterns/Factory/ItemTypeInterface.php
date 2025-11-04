<?php

namespace App\Patterns\Factory;

/**
 * Interface para diferentes tipos de itens do guarda-roupa
 * 
 * Define o contrato que todos os tipos de itens devem implementar,
 * permitindo comportamentos específicos para cada categoria.
 */
interface ItemTypeInterface
{
    /**
     * Obtém o tipo do item
     * 
     * @return string
     */
    public function getType(): string;

    /**
     * Obtém as características específicas do tipo
     * 
     * @return array
     */
    public function getCharacteristics(): array;

    /**
     * Valida se os dados são apropriados para este tipo
     * 
     * @param array $data
     * @return bool
     */
    public function validateData(array $data): bool;

    /**
     * Processa dados específicos do tipo antes de salvar
     * 
     * @param array $data
     * @return array
     */
    public function processData(array $data): array;

    /**
     * Obtém as regras de validação específicas do tipo
     * 
     * @return array
     */
    public function getValidationRules(): array;

    /**
     * Obtém sugestões de cuidados para este tipo de item
     * 
     * @return array
     */
    public function getCareInstructions(): array;

    /**
     * Calcula a durabilidade estimada baseada no tipo e condição
     * 
     * @param string $condition
     * @return int Durabilidade em meses
     */
    public function calculateDurability(string $condition): int;

    /**
     * Obtém as estações recomendadas para este tipo
     * 
     * @return array
     */
    public function getRecommendedSeasons(): array;
}
