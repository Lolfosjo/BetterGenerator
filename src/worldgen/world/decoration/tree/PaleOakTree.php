<?php

declare(strict_types=1);

namespace worldgen\world\decoration\tree;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Leaves;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function abs;
use function cos;
use function sin;

class PaleOakTree implements TreeType {

    private int $height;
    private Block $log_type;
    private Block $leaves_type;
    private array $overridables;

    public function __construct() {
        $this->log_type = VanillaBlocks::PALE_OAK_LOG();
        $this->leaves_type = VanillaBlocks::PALE_OAK_LEAVES();
        
        $this->overridables = [
            BlockTypeIds::AIR => true,
            BlockTypeIds::ACACIA_LEAVES => true,
            BlockTypeIds::BIRCH_LEAVES => true,
            BlockTypeIds::DARK_OAK_LEAVES => true,
            BlockTypeIds::JUNGLE_LEAVES => true,
            BlockTypeIds::OAK_LEAVES => true,
            BlockTypeIds::SPRUCE_LEAVES => true,
            BlockTypeIds::MANGROVE_LEAVES => true,
            BlockTypeIds::CHERRY_LEAVES => true,
            BlockTypeIds::GRASS => true,
            BlockTypeIds::DIRT => true,
            BlockTypeIds::ACACIA_WOOD => true,
            BlockTypeIds::BIRCH_WOOD => true,
            BlockTypeIds::DARK_OAK_WOOD => true,
            BlockTypeIds::JUNGLE_WOOD => true,
            BlockTypeIds::OAK_WOOD => true,
            BlockTypeIds::SPRUCE_WOOD => true,
            BlockTypeIds::MANGROVE_WOOD => true,
            BlockTypeIds::CHERRY_WOOD => true,
            BlockTypeIds::VINES => true,
        ];
    }

    public function setLogType(Block $log): void {
        $this->log_type = $log;
    }

    public function setLeavesType(Block $leaves): void {
        $this->leaves_type = $leaves;
    }

    private function canPlaceOn(Block $soil): bool {
        $id = $soil->getTypeId();
        return $id === BlockTypeIds::GRASS || 
               $id === BlockTypeIds::DIRT || 
               $id === BlockTypeIds::PODZOL;
    }

    private function cannotGenerateAt(int $source_x, int $source_y, int $source_z, ChunkManager $world): bool {
        for ($x = 0; $x <= 1; ++$x) {
            for ($z = 0; $z <= 1; ++$z) {
                if ($source_y < 1 || $source_y + $this->height > $world->getMaxY()) {
                    return true;
                }
                
                $soil = $world->getBlockAt($source_x + $x, $source_y - 1, $source_z + $z);
                if (!$this->canPlaceOn($soil)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function generate(ChunkManager $world, Random $random, int $source_x, int $source_y, int $source_z): bool {
        $this->height = $random->nextBoundedInt(2) + $random->nextBoundedInt(3) + 6;
        
        if ($this->cannotGenerateAt($source_x, $source_y, $source_z, $world)) {
            return false;
        }
      
        $d = $random->nextFloat() * M_PI * 2.0;
        $dx = (int)(cos($d) + 1.5) - 1;
        $dz = (int)(sin($d) + 1.5) - 1;
        
        if (abs($dx) > 0 && abs($dz) > 0) {
            if ($random->nextBoolean()) {
                $dx = 0;
            } else {
                $dz = 0;
            }
        }
        
        $twist_height = $this->height - $random->nextBoundedInt(4);
        $twist_count = $random->nextBoundedInt(3);
        $center_x = $source_x;
        $center_z = $source_z;
        $trunk_top_y = 0;

        for ($y = 0; $y < $this->height; ++$y) {
            if ($twist_count > 0 && $y >= $twist_height) {
                $center_x += $dx;
                $center_z += $dz;
                --$twist_count;
            }

            $material = $world->getBlockAt($center_x, $source_y + $y, $center_z);
            if ($material->getTypeId() !== BlockTypeIds::AIR && !($material instanceof Leaves)) {
                continue;
            }
            
            $trunk_top_y = $source_y + $y;
            
            $world->setBlockAt($center_x, $source_y + $y, $center_z, $this->log_type);
            $world->setBlockAt($center_x, $source_y + $y, $center_z + 1, $this->log_type);
            $world->setBlockAt($center_x + 1, $source_y + $y, $center_z, $this->log_type);
            $world->setBlockAt($center_x + 1, $source_y + $y, $center_z + 1, $this->log_type);
        }

        for ($x = -2; $x <= 0; ++$x) {
            for ($z = -2; $z <= 0; ++$z) {
                if (($x !== -1 || $z !== -2) && ($x > -2 || $z > -1)) {
                    $this->setLeaves($center_x + $x, $trunk_top_y + 1, $center_z + $z, $world);
                    $this->setLeaves(1 + $center_x - $x, $trunk_top_y + 1, $center_z + $z, $world);
                    $this->setLeaves($center_x + $x, $trunk_top_y + 1, 1 + $center_z - $z, $world);
                    $this->setLeaves(1 + $center_x - $x, $trunk_top_y + 1, 1 + $center_z - $z, $world);
                }
                $this->setLeaves($center_x + $x, $trunk_top_y - 1, $center_z + $z, $world);
                $this->setLeaves(1 + $center_x - $x, $trunk_top_y - 1, $center_z + $z, $world);
                $this->setLeaves($center_x + $x, $trunk_top_y - 1, 1 + $center_z - $z, $world);
                $this->setLeaves(1 + $center_x - $x, $trunk_top_y - 1, 1 + $center_z - $z, $world);
            }
        }

        for ($x = -3; $x <= 4; ++$x) {
            for ($z = -3; $z <= 4; ++$z) {
                if (abs($x) < 3 || abs($z) < 3) {
                    $this->setLeaves($center_x + $x, $trunk_top_y, $center_z + $z, $world);
                }
            }
        }

        for ($x = -1; $x <= 2; ++$x) {
            for ($z = -1; $z <= 2; ++$z) {
                if (($x !== -1 && $z !== -1 && $x !== 2 && $z !== 2) || $random->nextBoundedInt(3) !== 0) {
                    continue;
                }
                
                for ($y = 0; $y < $random->nextBoundedInt(3) + 2; ++$y) {
                    $material = $world->getBlockAt($source_x + $x, $trunk_top_y - $y - 1, $source_z + $z);
                    if ($material->getTypeId() === BlockTypeIds::AIR || $material instanceof Leaves) {
                        $world->setBlockAt($source_x + $x, $trunk_top_y - $y - 1, $source_z + $z, $this->log_type);
                    }
                }

              for ($i = -1; $i <= 1; ++$i) {
                    for ($j = -1; $j <= 1; ++$j) {
                        $this->setLeaves($center_x + $x + $i, $trunk_top_y, $center_z + $z + $j, $world);
                    }
                }
                
                for ($i = -2; $i <= 2; ++$i) {
                    for ($j = -2; $j <= 2; ++$j) {
                        if (abs($i) < 2 || abs($j) < 2) {
                            $this->setLeaves($center_x + $x + $i, $trunk_top_y - 1, $center_z + $z + $j, $world);
                        }
                    }
                }
            }
        }

        if ($random->nextBoundedInt(2) === 0) {
            $this->setLeaves($center_x, $trunk_top_y + 2, $center_z, $world);
            $this->setLeaves($center_x + 1, $trunk_top_y + 2, $center_z, $world);
            $this->setLeaves($center_x + 1, $trunk_top_y + 2, $center_z + 1, $world);
            $this->setLeaves($center_x, $trunk_top_y + 2, $center_z + 1, $world);
        }

        $dirt = VanillaBlocks::DIRT();
        $world->setBlockAt($source_x, $source_y - 1, $source_z, $dirt);
        $world->setBlockAt($source_x, $source_y - 1, $source_z + 1, $dirt);
        $world->setBlockAt($source_x + 1, $source_y - 1, $source_z, $dirt);
        $world->setBlockAt($source_x + 1, $source_y - 1, $source_z + 1, $dirt);
        
        return true;
    }

    private function setLeaves(int $x, int $y, int $z, ChunkManager $world): void {
        if ($world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::AIR) {
            $world->setBlockAt($x, $y, $z, $this->leaves_type);
        }
    }
}
