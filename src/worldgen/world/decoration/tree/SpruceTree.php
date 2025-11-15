<?php

declare(strict_types=1);

namespace worldgen\world\decoration\tree;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function abs;

/**
 * Generiert große Redwood/Fichten-Bäume (große Tannen)
 * Basierend auf dem Original PocketMine SpruceTree Code
 */
class SpruceTree implements TreeType {

    private int $minHeight;
    private int $maxHeight;

    public function __construct(int $minHeight = 6, int $maxHeight = 10) {
        $this->minHeight = $minHeight;
        $this->maxHeight = $maxHeight;
    }

    public function generate(ChunkManager $world, Random $random, int $x, int $y, int $z): bool {
        $height = $random->nextRange($this->minHeight, $this->maxHeight);

        $topSize = $height - (1 + $random->nextRange(0, 2));
        $maxRadius = 2 + $random->nextRange(0, 2);

        if ($y + $height >= $world->getMaxY() - 3) {
            return false;
        }

        if (!$this->canPlace($world, $x, $y, $z, $height, $topSize, $maxRadius)) {
            return false;
        }

        $groundBlock = $world->getBlockAt($x, $y - 1, $z);
        if ($groundBlock->getTypeId() === VanillaBlocks::GRASS()->getTypeId()) {
            $world->setBlockAt($x, $y - 1, $z, VanillaBlocks::DIRT());
        }

        $trunkHeight = $height - $random->nextRange(0, 3);
        $this->generateTrunk($world, $x, $y, $z, $trunkHeight);

        $this->generateCrown($world, $x, $y, $z, $height, $topSize, $maxRadius, $random);

        return true;
    }

    private function canPlace(
        ChunkManager $world,
        int $baseX,
        int $baseY,
        int $baseZ,
        int $height,
        int $topSize,
        int $maxRadius
    ): bool {
        $groundBlock = $world->getBlockAt($baseX, $baseY - 1, $baseZ);
        $groundTypeId = $groundBlock->getTypeId();
        if ($groundTypeId !== VanillaBlocks::GRASS()->getTypeId() &&
            $groundTypeId !== VanillaBlocks::DIRT()->getTypeId() &&
            $groundTypeId !== VanillaBlocks::PODZOL()->getTypeId()) {
            return false;
        }

        for ($y = $baseY; $y <= $baseY + $height; ++$y) {
            if ($y < $world->getMinY() || $y >= $world->getMaxY()) {
                return false;
            }

            $block = $world->getBlockAt($baseX, $y, $baseZ);
            if (!$this->isReplaceable($block->getTypeId())) {
                return false;
            }
        }

        $radius = 0;
        for ($yy = 0; $yy <= $topSize; ++$yy) {
            $yyy = $baseY + $height - $yy;

            for ($xx = $baseX - $radius; $xx <= $baseX + $radius; ++$xx) {
                for ($zz = $baseZ - $radius; $zz <= $baseZ + $radius; ++$zz) {
                    if ($yyy >= $world->getMinY() && $yyy < $world->getMaxY()) {
                        $block = $world->getBlockAt($xx, $yyy, $zz);
                        $typeId = $block->getTypeId();

                        if (!$this->isReplaceable($typeId)) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }

            if ($radius < $maxRadius) {
                ++$radius;
            }
        }

        return true;
    }

    private function isReplaceable(int $typeId): bool {
        return $typeId === VanillaBlocks::AIR()->getTypeId() ||
            $typeId === VanillaBlocks::OAK_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::SPRUCE_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::BIRCH_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::JUNGLE_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::ACACIA_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::DARK_OAK_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::AZALEA_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::FLOWERING_AZALEA_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::MANGROVE_LEAVES()->getTypeId() ||
            $typeId === VanillaBlocks::CHERRY_LEAVES()->getTypeId();
    }

    private function generateTrunk(
        ChunkManager $world,
        int $x,
        int $y,
        int $z,
        int $trunkHeight
    ): void {
        for ($i = 0; $i < $trunkHeight; ++$i) {
            $block = $world->getBlockAt($x, $y + $i, $z);
            if ($this->isReplaceable($block->getTypeId())) {
                $world->setBlockAt($x, $y + $i, $z, VanillaBlocks::SPRUCE_LOG());
            }
        }
    }

    private function generateCrown(
        ChunkManager $world,
        int $x,
        int $y,
        int $z,
        int $height,
        int $topSize,
        int $maxRadius,
        Random $random
    ): void {
        $radius = $random->nextRange(0, 2);
        $maxR = 1;
        $minR = 0;

        for ($yy = 0; $yy <= $topSize; ++$yy) {
            $yyy = $y + $height - $yy;

            for ($xx = $x - $radius; $xx <= $x + $radius; ++$xx) {
                $xOff = abs($xx - $x);

                for ($zz = $z - $radius; $zz <= $z + $radius; ++$zz) {
                    $zOff = abs($zz - $z);

                    if ($xOff === $radius && $zOff === $radius && $radius > 0) {
                        continue;
                    }

                    $block = $world->getBlockAt($xx, $yyy, $zz);
                    if (!$block->isSolid()) {
                        $world->setBlockAt($xx, $yyy, $zz, VanillaBlocks::SPRUCE_LEAVES());
                    }
                }
            }

            if ($radius >= $maxR) {
                $radius = $minR;
                $minR = 1;
                if (++$maxR > $maxRadius) {
                    $maxR = $maxRadius;
                }
            } else {
                ++$radius;
            }
        }
    }
}