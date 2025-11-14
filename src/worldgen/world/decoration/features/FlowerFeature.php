<?php

declare(strict_types=1);

namespace worldgen\world\decoration\features;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use worldgen\world\decoration\DecorationFeature;

class FlowerFeature implements DecorationFeature {

    /** @var Block[] */
    private array $flowers;
    private float $chance;
    private int $attemptsPerChunk;
    private int $clusterSize;
    private bool $onlyNearWater;

    /**
     * @param Block[] $flowers Array von möglichen Blumen-Blöcken
     * @param float $chance Spawn-Chance
     * @param int $attemptsPerChunk Versuche pro Chunk
     * @param int $clusterSize Größe der Blumen-Cluster (1 = einzelne Blumen)
     * @param bool $onlyNearWater Wenn true, wachsen Blumen nur direkt am Wasser
     */
    public function __construct(
        array $flowers,
        float $chance = 0.05,
        int $attemptsPerChunk = 5,
        int $clusterSize = 1,
        bool $onlyNearWater = false
    ) {
        $this->flowers = $flowers;
        $this->chance = $chance;
        $this->attemptsPerChunk = $attemptsPerChunk;
        $this->clusterSize = $clusterSize;
        $this->onlyNearWater = $onlyNearWater;
    }

    public function place(ChunkManager $world, Random $random, int $x, int $y, int $z): bool {
        if (empty($this->flowers)) {
            return false;
        }

        if ($y < $world->getMinY() || $y >= $world->getMaxY() - 1) {
            return false;
        }

        $groundBlock = $world->getBlockAt($x, $y, $z);
        $aboveBlock = $world->getBlockAt($x, $y + 1, $z);

        if (!$this->isValidGround($groundBlock) || $aboveBlock->getTypeId() !== VanillaBlocks::AIR()->getTypeId()) {
            return false;
        }

        if ($this->onlyNearWater && !$this->isNearWater($world, $x, $y, $z)) {
            return false;
        }

        $placed = false;

        for ($i = 0; $i < $this->clusterSize; $i++) {
            $offsetX = $random->nextRange(-2, 2);
            $offsetZ = $random->nextRange(-2, 2);
            
            $px = $x + $offsetX;
            $pz = $z + $offsetZ;

            $groundAtPos = $world->getBlockAt($px, $y, $pz);
            $aboveAtPos = $world->getBlockAt($px, $y + 1, $pz);

            if ($this->isValidGround($groundAtPos)
                && $aboveAtPos->getTypeId() === VanillaBlocks::AIR()->getTypeId()
                && (!$this->onlyNearWater || $this->isNearWater($world, $px, $y, $pz))
            ) {
                $flower = $this->flowers[$random->nextRange(0, count($this->flowers) - 1)];
                $world->setBlockAt($px, $y + 1, $pz, $flower);
                $placed = true;
            }
        }

        return $placed;
    }

    private function isValidGround(Block $block): bool {
        $typeId = $block->getTypeId();
        return $typeId === VanillaBlocks::GRASS()->getTypeId() ||
               $typeId === VanillaBlocks::DIRT()->getTypeId() ||
               $typeId === VanillaBlocks::PODZOL()->getTypeId();
    }

    private function isNearWater(ChunkManager $world, int $x, int $y, int $z): bool {
        for ($dx = -1; $dx <= 1; $dx++) {
            for ($dz = -1; $dz <= 1; $dz++) {
                $block = $world->getBlockAt($x + $dx, $y, $z + $dz);
                if ($block->getTypeId() === VanillaBlocks::WATER()->getTypeId()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getChance(): float {
        return $this->chance;
    }

    public function getAttemptsPerChunk(): int {
        return $this->attemptsPerChunk;
    }
}
