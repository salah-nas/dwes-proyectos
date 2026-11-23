<?php

namespace App\Service;

use App\Entity\Combate;
use App\Entity\Pokemon;

class CombateService
{
    /** Devuelve Combate::VICTORIA, DERROTA o EMPATE desde el punto de vista del jugador. */
    public function resolver(Pokemon $jugador, Pokemon $rival): string
    {
        $poderJugador = $this->poder($jugador);
        $poderRival = $this->poder($rival);

        return match (true) {
            $poderJugador > $poderRival => Combate::VICTORIA,
            $poderJugador < $poderRival => Combate::DERROTA,
            default => Combate::EMPATE,
        };
    }

    /** Estadísticas ponderadas con un pequeño factor de azar (±15 %). */
    private function poder(Pokemon $p): float
    {
        $base = $p->getAtaque() + $p->getDefensa() * 0.6 + $p->getVelocidad() * 0.4 + $p->getHp() * 0.5;

        return $base * (random_int(85, 115) / 100);
    }
}
