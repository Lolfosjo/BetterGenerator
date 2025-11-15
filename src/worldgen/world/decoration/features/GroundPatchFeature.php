<?php

declare(strict_types=1);

namespace worldgen\world\decoration\features;

use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use worldgen\world\decoration\DecorationFeature;

/**
 * Platziert Ground Patches (z.B. Gras, Schmutz-Flecken, Kies)
 */
class GroundPatchFeature implements DecorationFeature {

    private Block $block;
    private int $radius;
    private float $chance;
    private int $attemptsPerChunk;
    private bool $replaceOnlyGrass;

    /**
     * @param Block $block Der Block für den Patch
     * @param int $radius Radius des Patches (1-5)
     * @param float $chance Spawn-Chance (0.0 - 1.0)
     * @param int $attemptsPerChunk Versuche pro Chunk
     * @param bool $replaceOnlyGrass Nur Gras ersetzen (true) oder auch andere Blöcke
     */
    public function __construct(
        Block $block,
        int $radius = 2,
        float $chance = 0.1,
        int $attemptsPerChunk = 3,
        bool $replaceOnlyGrass = true
    ) {
        $this->block = $block;
        $this->radius = $radius;
        $this->chance = $chance;
        $this->attemptsPerChunk = $attemptsPerChunk;
        $this->replaceOnlyGrass = $replaceOnlyGrass;
    }

    public function place(ChunkManager $world, Random $random, int $x, int $y, int $z): bool {
        if ($y < $world->getMinY() || $y >= $world->getMaxY() - 1) {
            return false;
        }

        $placed = false;

        for ($dx = -$this->radius; $dx <= $this->radius; $dx++) {
            for ($dz = -$this->radius; $dz <= $this->radius; $dz++) {
                $distance = sqrt($dx * $dx + $dz * $dz);
                if ($distance > $this->radius) {
                    continue;
                }

                if ($distance > $this->radius - 1 && $random->nextFloat() > 0.5) {
                    continue;
                }

                $px = $x + $dx;
                $pz = $z + $dz;

                $currentBlock = $world->getBlockAt($px, $y, $pz);
                
                if ($this->replaceOnlyGrass) {
                    if ($currentBlock->getTypeId() !== \pocketmine\block\VanillaBlocks::GRASS()->getTypeId()) {
                        continue;
                    }
                } else {
                    if (!$currentBlock->isSolid()) {
                        continue;
                    }
                }

                $world->setBlockAt($px, $y, $pz, $this->block);
                $placed = true;
            }
        }

        return $placed;
    }

    public function getChance(): float {
        return $this->chance;
    }

    public function getAttemptsPerChunk(): int {
        return $this->attemptsPerChunk;
    }
}
