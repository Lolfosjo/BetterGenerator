<?php

declare(strict_types=1);

namespace worldgen\world\decoration\features;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use worldgen\world\decoration\DecorationFeature;
use worldgen\world\decoration\tree\TreeType;

/**
 * Platziert Bäume
 */
class TreeFeature implements DecorationFeature {

    private TreeType $treeType;
    private float $chance;
    private int $attemptsPerChunk;

    public function __construct(
        TreeType $treeType,
        float $chance = 0.02,
        int $attemptsPerChunk = 3
    ) {
        $this->treeType = $treeType;
        $this->chance = $chance;
        $this->attemptsPerChunk = $attemptsPerChunk;
    }

    public function place(ChunkManager $world, Random $random, int $x, int $y, int $z): bool {
        if ($y < $world->getMinY() || $y >= $world->getMaxY() - 10) {
            return false;
        }

        $groundBlock = $world->getBlockAt($x, $y, $z);
        $aboveBlock = $world->getBlockAt($x, $y + 1, $z);

        if (!$this->isValidGround($groundBlock) || $aboveBlock->getTypeId() !== VanillaBlocks::AIR()->getTypeId()) {
            return false;
        }

        return $this->treeType->generate($world, $random, $x, $y + 1, $z);
    }

    private function isValidGround(\pocketmine\block\Block $block): bool {
        $typeId = $block->getTypeId();
        return $typeId === VanillaBlocks::GRASS()->getTypeId() ||
               $typeId === VanillaBlocks::DIRT()->getTypeId() ||
               $typeId === VanillaBlocks::PODZOL()->getTypeId();
    }

    public function getChance(): float {
        return $this->chance;
    }

    public function getAttemptsPerChunk(): int {
        return $this->attemptsPerChunk;
    }
}
