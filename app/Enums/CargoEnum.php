<?php

namespace App\Enums;

enum CargoEnum: int
{
    case ATENDENTE = 1;
    case TECNICO = 2;

    /**
     * Retorna o nome do cargo
     */
    public function label(): string
    {
        return match ($this) {
            self::ATENDENTE => 'Atendente',
            self::TECNICO => 'Técnico',
        };
    }
}
