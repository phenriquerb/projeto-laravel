<?php

namespace App\Enums;

enum StatusOrdemServicoEnum: string
{
    case ABERTA = 'aberta';
    case EM_ANALISE = 'em_analise';
    case AGUARDANDO_PECAS = 'aguardando_pecas';
    case EXECUCAO = 'execucao';
    case CONCLUIDA = 'concluida';
    case CANCELADA = 'cancelada';

    /**
     * Retorna o label do status
     */
    public function label(): string
    {
        return match ($this) {
            self::ABERTA => 'Aberta',
            self::EM_ANALISE => 'Em Análise',
            self::AGUARDANDO_PECAS => 'Aguardando Peças',
            self::EXECUCAO => 'Execução',
            self::CONCLUIDA => 'Concluída',
            self::CANCELADA => 'Cancelada',
        };
    }

    /**
     * Retorna todos os valores possíveis
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
