<?php

declare(strict_types=1);

namespace worldgen\world\decoration\tree;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

/**
 * Interface für verschiedene Baum-Typen
 */
interface TreeType {

    /**
     * Generiert einen Baum an der angegebenen Position
     *
     * @param ChunkManager $world Die Welt
     * @param Random $random Zufallsgenerator
     * @param int $x Stamm X-Position
     * @param int $y Stamm Basis Y-Position
     * @param int $z Stamm Z-Position
     * @return bool True wenn erfolgreich generiert
     */
    public function generate(ChunkManager $world, Random $random, int $x, int $y, int $z): bool;
}