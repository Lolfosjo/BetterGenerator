<?php

declare(strict_types=1);

namespace worldgen\world;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use worldgen\world\biome\BiomeRegistry;
use worldgen\world\noise\VoronoiCalculator;
use worldgen\world\noise\HeightSmoother;
use worldgen\world\oregen\OreConfiguration;

/**
 * Füllt Chunks mit Blöcken basierend auf Biom-Daten
 */
class ChunkPopulator {
    
    private VoronoiCalculator $voronoiCalculator;
    private HeightSmoother $heightSmoother;
    private int $biomeCount;
    private array $oreConfigs = [];
    
    public function __construct(
        VoronoiCalculator $voronoiCalculator,
        HeightSmoother $heightSmoother,
        int $biomeCount
    ) {
        $this->voronoiCalculator = $voronoiCalculator;
        $this->heightSmoother = $heightSmoother;
        $this->biomeCount = $biomeCount;
        $this->oreConfigs = OreConfiguration::getOreConfigs();
    }
    
    /**
     * Füllt einen Chunk mit Terrain-Blöcken
     */
    public function populate(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunk === null) {
            return;
        }
        
        for ($x = 0; $x < 16; $x++) {
            for ($z = 0; $z < 16; $z++) {
                $worldX = ($chunkX << 4) + $x;
                $worldZ = ($chunkZ << 4) + $z;
                
                // Smoothed height mit Chunk-übergreifendem Sampling
                $smoothedHeight = $this->heightSmoother->smoothHeight($worldX, $worldZ, $world, $chunkX, $chunkZ);
                
                // Hole Biom für Block-Typen
                $voronoi = $this->voronoiCalculator->calculate($worldX, $worldZ, $this->biomeCount);
                $biomeIdx = $voronoi['biomeIdx'];
                $maxY = 64 + $smoothedHeight;
                $biome = BiomeRegistry::getBiome($biomeIdx);
                
                if ($biome === null) {
                    continue;
                }
                
                // Setze Vanilla-Biome-ID
                $this->setBiomeIds($chunk, $x, $z, $world, $biome->getVanillaBiomeId());
                
                // Bedrock ZUERST setzen
                $this->placeBedrock($chunk, $x, $z);
                
                // Fülle Spalte mit Blöcken (ab Y=-63)
                $this->fillColumn($chunk, $x, $z, $maxY, $biome, $worldX, $worldZ);
                
                // Fülle Wasser
                $this->fillWater($chunk, $x, $z, $maxY, $biome);
            }
        }
        
        // Generiere Erze für den gesamten Chunk
        $this->generateOres($chunk, $chunkX, $chunkZ);
    }
    
    private function setBiomeIds(Chunk $chunk, int $x, int $z, ChunkManager $world, ?int $vanillaBiomeId): void {
        if ($vanillaBiomeId !== null) {
            for ($y = $world->getMinY(); $y < $world->getMaxY(); $y += 4) {
                $chunk->setBiomeId($x, $y, $z, $vanillaBiomeId);
            }
        } else {
            for ($y = $world->getMinY(); $y < $world->getMaxY(); $y += 4) {
                $chunk->setBiomeId($x, $y, $z, BiomeRegistry::PLAINS);
            }
        }
    }
    
    private function fillColumn(Chunk $chunk, int $x, int $z, int $maxY, $biome, int $worldX, int $worldZ): void {
        for ($y = -63; $y <= $maxY; $y++) {
            if ($y >= -64 && $y < 320) {
                $block = $biome->getBlockAt($y, $maxY);
                
                if ($y >= 0 && $y <= 5) {
                    $deepslateChance = 1.0 - ($y / 5.0);
                    
                    $hash = ($worldX * 374761393 + $worldZ * 668265263 + $y * 2147483647) & 0x7FFFFFFF;
                    $random = ($hash % 10000) / 10000.0;
                    
                    if ($random < $deepslateChance) {
                        if ($block->getTypeId() === VanillaBlocks::STONE()->getTypeId()) {
                            $block = VanillaBlocks::DEEPSLATE();
                        }
                    }
                } else if ($y < 0) {
                    if ($block->getTypeId() === VanillaBlocks::STONE()->getTypeId()) {
                        $block = VanillaBlocks::DEEPSLATE();
                    }
                }
                
                $chunk->setBlockStateId($x, $y, $z, $block->getStateId());
            }
        }
    }
    
    private function fillWater(Chunk $chunk, int $x, int $z, int $maxY, $biome): void {
        if ($maxY < 64) {
            for ($y = $maxY + 1; $y <= 64; $y++) {
                if ($y >= -64 && $y < 320) {
                    $block = $biome->getBlockAt($y, $maxY);
                    if ($block->getTypeId() === VanillaBlocks::STONE()->getTypeId()) {
                        $block = VanillaBlocks::WATER();
                    }
                    $chunk->setBlockStateId($x, $y, $z, $block->getStateId());
                }
            }
        }
    }
    
    private function placeBedrock(Chunk $chunk, int $x, int $z): void {
        $chunk->setBlockStateId($x, -64, $z, VanillaBlocks::BEDROCK()->getStateId());
    }
    
    private function generateOres(Chunk $chunk, int $chunkX, int $chunkZ): void {
        foreach ($this->oreConfigs as $config) {
            for ($attempt = 0; $attempt < $config['attempts']; $attempt++) {
                $x = $this->random($chunkX, $chunkZ, $attempt, 0) % 16;
                $z = $this->random($chunkX, $chunkZ, $attempt, 1) % 16;
                $y = $config['minHeight'] + ($this->random($chunkX, $chunkZ, $attempt, 2) % ($config['maxHeight'] - $config['minHeight'] + 1));
                
                $chance = $this->calculateChanceForHeight($y, $config);
                
                $randomValue = ($this->random($chunkX, $chunkZ, $attempt, 3) % 10000) / 10000.0;
                if ($randomValue < $chance) {
                    $this->placeOreVein($chunk, $x, $y, $z, $config['block'], $config['veinSize'], $chunkX, $chunkZ, $attempt);
                }
            }
        }
    }
    
    private function calculateChanceForHeight(int $y, array $config): float {
        $baseChance = $config['chance'];
        
        if ($y >= $config['optimalMin'] && $y <= $config['optimalMax']) {
            return $baseChance * $config['optimalMultiplier'];
        }
        
        if (!empty($config['heightMultipliers'])) {
            $multiplier = $this->interpolateHeightMultiplier($y, $config['heightMultipliers']);
            return $baseChance * $multiplier;
        }
        
        return $baseChance;
    }
    
    private function interpolateHeightMultiplier(int $y, array $heightMultipliers): float {
        ksort($heightMultipliers);
        $heights = array_keys($heightMultipliers);
        
        if (isset($heightMultipliers[$y])) {
            return $heightMultipliers[$y];
        }
        
        $lowerHeight = null;
        $upperHeight = null;
        
        foreach ($heights as $height) {
            if ($height < $y) {
                $lowerHeight = $height;
            } else if ($height > $y && $upperHeight === null) {
                $upperHeight = $height;
                break;
            }
        }
        
        if ($lowerHeight !== null && $upperHeight !== null) {
            $lowerMultiplier = $heightMultipliers[$lowerHeight];
            $upperMultiplier = $heightMultipliers[$upperHeight];
            $ratio = ($y - $lowerHeight) / ($upperHeight - $lowerHeight);
            return $lowerMultiplier + ($upperMultiplier - $lowerMultiplier) * $ratio;
        }
        
        if ($lowerHeight !== null) {
            return $heightMultipliers[$lowerHeight];
        }
        if ($upperHeight !== null) {
            return $heightMultipliers[$upperHeight];
        }
        
        return 1.0;
    }
    
    private function placeOreVein(Chunk $chunk, int $startX, int $startY, int $startZ, $oreBlock, int $veinSize, int $chunkX, int $chunkZ, int $attempt): void {
        $placed = 0;
        $maxAttempts = $veinSize * 3;
        
        for ($i = 0; $i < $maxAttempts && $placed < $veinSize; $i++) {
            $offsetX = ($this->random($chunkX, $chunkZ, $attempt, 4 + $i * 3) % 7) - 3;
            $offsetY = ($this->random($chunkX, $chunkZ, $attempt, 5 + $i * 3) % 7) - 3;
            $offsetZ = ($this->random($chunkX, $chunkZ, $attempt, 6 + $i * 3) % 7) - 3;
            
            $x = $startX + $offsetX;
            $y = $startY + $offsetY;
            $z = $startZ + $offsetZ;
            
            if ($x < 0 || $x >= 16 || $z < 0 || $z >= 16 || $y < -64 || $y >= 320) {
                continue;
            }
            
            $currentBlock = $chunk->getBlockStateId($x, $y, $z);
            $stoneId = VanillaBlocks::STONE()->getStateId();
            $deepslateId = VanillaBlocks::DEEPSLATE()->getStateId();
            
            if ($currentBlock === $stoneId || $currentBlock === $deepslateId) {
                $chunk->setBlockStateId($x, $y, $z, $oreBlock->getStateId());
                $placed++;
            }
        }
    }
    
    private function random(int $chunkX, int $chunkZ, int $attempt, int $salt): int {
        $hash = ($chunkX * 374761393 + $chunkZ * 668265263 + $attempt * 2147483647 + $salt * 1234567891) & 0x7FFFFFFF;
        return abs($hash);
    }
}
