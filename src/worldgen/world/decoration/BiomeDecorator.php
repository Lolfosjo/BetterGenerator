<?php

declare(strict_types=1);

namespace worldgen\world\decoration;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

/**
 * Verwaltet Dekorationen für ein Biom
 */
class BiomeDecorator {

    /** @var DecorationFeature[] */
    private array $features = [];

    /**
     * Fügt ein Decoration-Feature hinzu
     */
    public function addFeature(DecorationFeature $feature): self {
        $this->features[] = $feature;
        return $this;
    }

    /**
     * Dekoriert einen Chunk
     * 
     * @param ChunkManager $world Die Welt
     * @param Random $random Zufallsgenerator
     * @param int $chunkX Chunk X-Koordinate
     * @param int $chunkZ Chunk Z-Koordinate
     * @param callable $heightMapCallback Callback um Höhe für Position zu bekommen: fn(int $x, int $z): int
     */
    public function decorate(
        ChunkManager $world,
        Random $random,
        int $chunkX,
        int $chunkZ,
        callable $heightMapCallback
    ): void {
        foreach ($this->features as $feature) {
            $attempts = $feature->getAttemptsPerChunk();
            $chance = $feature->getChance();

            for ($attempt = 0; $attempt < $attempts; $attempt++) {
                // Prüfe Spawn-Chance
                if ($random->nextFloat() > $chance) {
                    continue;
                }

                // Zufällige Position im Chunk
                $x = ($chunkX << 4) + $random->nextRange(0, 15);
                $z = ($chunkZ << 4) + $random->nextRange(0, 15);

                // Hole Oberflächenhöhe für diese Position
                $y = $heightMapCallback($x, $z);

                // Versuche Feature zu platzieren
                $feature->place($world, $random, $x, $y, $z);
            }
        }
    }

    /**
     * Gibt alle Features zurück
     * @return DecorationFeature[]
     */
    public function getFeatures(): array {
        return $this->features;
    }

    /**
     * Entfernt alle Features
     */
    public function clearFeatures(): void {
        $this->features = [];
    }
}
