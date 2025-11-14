<?php

declare(strict_types=1);

namespace worldgen\world\decoration;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

/**
 * Basis-Interface für alle Dekorations-Features
 */
interface DecorationFeature {

    /**
     * Versucht das Feature an einer bestimmten Position zu platzieren
     * 
     * @param ChunkManager $world Die Welt
     * @param Random $random Zufallsgenerator
     * @param int $x Welt X-Koordinate
     * @param int $y Welt Y-Koordinate (meist Oberflächenhöhe)
     * @param int $z Welt Z-Koordinate
     * @return bool True wenn erfolgreich platziert
     */
    public function place(ChunkManager $world, Random $random, int $x, int $y, int $z): bool;

    /**
     * Gibt die Chance zurück, dass dieses Feature spawnt (0.0 - 1.0)
     * z.B. 0.01 = 1% Chance pro Versuch
     */
    public function getChance(): float;

    /**
     * Gibt zurück wie oft pro Chunk versucht werden soll, dieses Feature zu platzieren
     */
    public function getAttemptsPerChunk(): int;
}
