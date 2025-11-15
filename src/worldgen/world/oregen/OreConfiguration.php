<?php

declare(strict_types=1);

namespace worldgen\world\oregen;

use pocketmine\block\VanillaBlocks;

/**
 * Konfiguration für Erz-Generierung
 */
class OreConfiguration {
    
    /**
     * Gibt alle Erz-Konfigurationen zurück
     * 
     * @return array[] Array von Erz-Konfigurationen
     */
    public static function getOreConfigs(): array {
        return [
            // Coal Ore
            [
                'block' => VanillaBlocks::COAL_ORE(),
                'chance' => 0.25,
                'attempts' => 20,
                'veinSize' => 17,
                'minHeight' => 1,
                'maxHeight' => 256,
                'optimalMin' => 96,
                'optimalMax' => 96,
                'optimalMultiplier' => 3.0,
                'heightMultipliers' => [
                    0 => 0.5,
                    96 => 3.0,
                    136 => 2.0,
                    192 => 1.0,
                    256 => 0.3
                ]
            ],
            
            // Deepslate Coal
            [
                'block' => VanillaBlocks::DEEPSLATE_COAL_ORE(),
                'chance' => 0.15,
                'attempts' => 10,
                'veinSize' => 17,
                'minHeight' => -64,
                'maxHeight' => 0,
                'optimalMin' => -32,
                'optimalMax' => -16,
                'optimalMultiplier' => 2.0,
                'heightMultipliers' => []
            ],
            
            // Iron Ore
            [
                'block' => VanillaBlocks::IRON_ORE(),
                'chance' => 0.2,
                'attempts' => 18,
                'veinSize' => 9,
                'minHeight' => 1,
                'maxHeight' => 256,
                'optimalMin' => 16,
                'optimalMax' => 16,
                'optimalMultiplier' => 2.5,
                'heightMultipliers' => [
                    -24 => 0.3,
                    16 => 2.5,
                    48 => 1.2,
                    80 => 2.0,
                    128 => 1.0,
                    232 => 1.8,
                    256 => 1.5
                ]
            ],
            
            // Deepslate Iron
            [
                'block' => VanillaBlocks::DEEPSLATE_IRON_ORE(),
                'chance' => 0.18,
                'attempts' => 15,
                'veinSize' => 9,
                'minHeight' => -64,
                'maxHeight' => 0,
                'optimalMin' => -16,
                'optimalMax' => 0,
                'optimalMultiplier' => 2.0,
                'heightMultipliers' => []
            ],
            
            // Copper Ore
            [
                'block' => VanillaBlocks::COPPER_ORE(),
                'chance' => 0.18,
                'attempts' => 16,
                'veinSize' => 12,
                'minHeight' => -16,
                'maxHeight' => 112,
                'optimalMin' => 48,
                'optimalMax' => 48,
                'optimalMultiplier' => 2.8,
                'heightMultipliers' => [
                    -16 => 0.5,
                    48 => 2.8,
                    80 => 1.5,
                    112 => 0.4
                ]
            ],
            
            // Deepslate Copper
            [
                'block' => VanillaBlocks::DEEPSLATE_COPPER_ORE(),
                'chance' => 0.15,
                'attempts' => 12,
                'veinSize' => 12,
                'minHeight' => -64,
                'maxHeight' => 0,
                'optimalMin' => -32,
                'optimalMax' => -16,
                'optimalMultiplier' => 2.0,
                'heightMultipliers' => []
            ],
            
            // Gold Ore
            [
                'block' => VanillaBlocks::GOLD_ORE(),
                'chance' => 0.08,
                'attempts' => 8,
                'veinSize' => 9,
                'minHeight' => -64,
                'maxHeight' => 32,
                'optimalMin' => -16,
                'optimalMax' => -16,
                'optimalMultiplier' => 2.0,
                'heightMultipliers' => [
                    -64 => 1.5,
                    -16 => 2.0,
                    0 => 1.0,
                    32 => 0.3
                ]
            ],
            
            // Deepslate Gold
            [
                'block' => VanillaBlocks::DEEPSLATE_GOLD_ORE(),
                'chance' => 0.12,
                'attempts' => 10,
                'veinSize' => 9,
                'minHeight' => -64,
                'maxHeight' => -48,
                'optimalMin' => -64,
                'optimalMax' => -48,
                'optimalMultiplier' => 1.8,
                'heightMultipliers' => []
            ],
            
            // Redstone Ore
            [
                'block' => VanillaBlocks::REDSTONE_ORE(),
                'chance' => 0.12,
                'attempts' => 12,
                'veinSize' => 8,
                'minHeight' => -64,
                'maxHeight' => 16,
                'optimalMin' => -64,
                'optimalMax' => -32,
                'optimalMultiplier' => 2.5,
                'heightMultipliers' => [
                    -64 => 2.5,
                    -32 => 2.0,
                    -16 => 1.2,
                    16 => 0.2
                ]
            ],
            
            // Diamond Ore
            [
                'block' => VanillaBlocks::DIAMOND_ORE(),
                'chance' => 0.04,
                'attempts' => 7,
                'veinSize' => 8,
                'minHeight' => -64,
                'maxHeight' => 16,
                'optimalMin' => -64,
                'optimalMax' => -64,
                'optimalMultiplier' => 3.5,
                'heightMultipliers' => [
                    -64 => 3.5,
                    -48 => 2.8,
                    -32 => 1.8,
                    -16 => 1.0,
                    0 => 0.4,
                    16 => 0.1
                ]
            ],
            
            // Deepslate Diamond
            [
                'block' => VanillaBlocks::DEEPSLATE_DIAMOND_ORE(),
                'chance' => 0.05,
                'attempts' => 8,
                'veinSize' => 8,
                'minHeight' => -64,
                'maxHeight' => 0,
                'optimalMin' => -64,
                'optimalMax' => -48,
                'optimalMultiplier' => 3.0,
                'heightMultipliers' => [
                    -64 => 3.0,
                    -48 => 2.5,
                    -32 => 1.5,
                    -16 => 0.8,
                    0 => 0.3
                ]
            ],
            
            // Lapis Ore
            [
                'block' => VanillaBlocks::LAPIS_LAZULI_ORE(),
                'chance' => 0.06,
                'attempts' => 6,
                'veinSize' => 7,
                'minHeight' => -64,
                'maxHeight' => 64,
                'optimalMin' => 0,
                'optimalMax' => 0,
                'optimalMultiplier' => 3.0,
                'heightMultipliers' => [
                    -64 => 0.5,
                    -32 => 1.5,
                    0 => 3.0,
                    32 => 1.2,
                    64 => 0.3
                ]
            ],
            
            // Deepslate Lapis
            [
                'block' => VanillaBlocks::DEEPSLATE_LAPIS_LAZULI_ORE(),
                'chance' => 0.05,
                'attempts' => 5,
                'veinSize' => 7,
                'minHeight' => -64,
                'maxHeight' => 0,
                'optimalMin' => -32,
                'optimalMax' => 0,
                'optimalMultiplier' => 2.0,
                'heightMultipliers' => []
            ],
            
            // Emerald Ore
            [
                'block' => VanillaBlocks::EMERALD_ORE(),
                'chance' => 0.02,
                'attempts' => 4,
                'veinSize' => 3,
                'minHeight' => -16,
                'maxHeight' => 320,
                'optimalMin' => 128,
                'optimalMax' => 256,
                'optimalMultiplier' => 4.0,
                'heightMultipliers' => [
                    -16 => 0.1,
                    64 => 1.0,
                    128 => 3.0,
                    192 => 4.0,
                    256 => 3.5,
                    320 => 2.0
                ]
            ],
            
            // Deepslate Emerald
            [
                'block' => VanillaBlocks::DEEPSLATE_EMERALD_ORE(),
                'chance' => 0.015,
                'attempts' => 3,
                'veinSize' => 3,
                'minHeight' => -16,
                'maxHeight' => 0,
                'optimalMin' => -16,
                'optimalMax' => 0,
                'optimalMultiplier' => 1.5,
                'heightMultipliers' => []
            ],
            
            // Gravel
            [
                'block' => VanillaBlocks::GRAVEL(),
                'chance' => 0.15,
                'attempts' => 8,
                'veinSize' => 33,
                'minHeight' => -64,
                'maxHeight' => 320,
                'optimalMin' => -64,
                'optimalMax' => 0,
                'optimalMultiplier' => 1.5,
                'heightMultipliers' => []
            ],
            
            // Granite
            [
                'block' => VanillaBlocks::GRANITE(),
                'chance' => 0.12,
                'attempts' => 10,
                'veinSize' => 64,
                'minHeight' => -64,
                'maxHeight' => 80,
                'optimalMin' => 0,
                'optimalMax' => 60,
                'optimalMultiplier' => 1.5,
                'heightMultipliers' => []
            ],
            
            // Diorite
            [
                'block' => VanillaBlocks::DIORITE(),
                'chance' => 0.12,
                'attempts' => 10,
                'veinSize' => 64,
                'minHeight' => -64,
                'maxHeight' => 80,
                'optimalMin' => 0,
                'optimalMax' => 60,
                'optimalMultiplier' => 1.5,
                'heightMultipliers' => []
            ],
            
            // Andesite
            [
                'block' => VanillaBlocks::ANDESITE(),
                'chance' => 0.12,
                'attempts' => 10,
                'veinSize' => 64,
                'minHeight' => -64,
                'maxHeight' => 80,
                'optimalMin' => 0,
                'optimalMax' => 60,
                'optimalMultiplier' => 1.5,
                'heightMultipliers' => []
            ],
            
            // Dirt
            [
                'block' => VanillaBlocks::DIRT(),
                'chance' => 0.18,
                'attempts' => 12,
                'veinSize' => 33,
                'minHeight' => -64,
                'maxHeight' => 160,
                'optimalMin' => 0,
                'optimalMax' => 80,
                'optimalMultiplier' => 1.8,
                'heightMultipliers' => []
            ]
        ];
    }
}
