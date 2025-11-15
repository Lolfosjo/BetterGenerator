<?php

declare(strict_types=1);

namespace worldgen\world\biome;

use pocketmine\block\SweetBerryBush;
use pocketmine\block\VanillaBlocks;
use worldgen\world\blocks\ExtraVanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\utils\Random;
use worldgen\world\decoration\BiomeDecorator;
use worldgen\world\decoration\ores\OreDecorator;
use worldgen\world\decoration\features\FlowerFeature;
use worldgen\world\decoration\tree\SpruceTree;
use worldgen\world\decoration\features\TreeFeature;
use worldgen\world\decoration\tree\SwampTree;
use worldgen\world\decoration\tree\OakTree;
use worldgen\world\decoration\tree\BigOakTree;
use worldgen\world\decoration\tree\BirchTree;
use worldgen\world\decoration\tree\DarkOakTree;

class BiomeRegistry {

    /** @var Biome[] */
    private static array $biomes = [];

    /**
     * Registriert alle Standard-Biome
     */
    public static function registerDefaultBiomes(): void {

        // Plains
        $plainsDecorator = new BiomeDecorator();
        $plainsDecorator
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::DANDELION(),
                    VanillaBlocks::POPPY(),
                    VanillaBlocks::AZURE_BLUET(),
                    VanillaBlocks::OXEYE_DAISY()
                ],
                chance: 0.15,
                attemptsPerChunk: 8,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    ExtraVanillaBlocks::BUSH()
                ],
                chance: 0.2,
                attemptsPerChunk: 2,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    ExtraVanillaBlocks::FIREFLY_BUSH()
                ],
                chance: 0.4,
                attemptsPerChunk: 4,
                clusterSize: 3,
                onlyNearWater: true
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::TALL_GRASS()
                ],
                chance: 0.5,
                attemptsPerChunk: 12,
                clusterSize: 7
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::DOUBLE_TALLGRASS()
                ],
                chance: 0.2,
                attemptsPerChunk: 6,
                clusterSize: 4
            ));

        // Oak Forest
        $oakForestDecorator = new BiomeDecorator();
        $oakForestDecorator
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::DANDELION(),
                    VanillaBlocks::POPPY(),
                    VanillaBlocks::AZURE_BLUET(),
                    VanillaBlocks::OXEYE_DAISY()
                ],
                chance: 0.15,
                attemptsPerChunk: 8,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    ExtraVanillaBlocks::BUSH()
                ],
                chance: 0.2,
                attemptsPerChunk: 2,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    ExtraVanillaBlocks::FIREFLY_BUSH()
                ],
                chance: 0.4,
                attemptsPerChunk: 4,
                clusterSize: 3,
                onlyNearWater: true
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::TALL_GRASS()
                ],
                chance: 0.5,
                attemptsPerChunk: 12,
                clusterSize: 7
            ))
            ->addFeature(new TreeFeature(
                treeType: new OakTree(new Random()),
                chance: 0.5,
                attemptsPerChunk: 8
            ))
            ->addFeature(new TreeFeature(
                treeType: new BigOakTree(new Random()),
                chance: 0.18,
                attemptsPerChunk: 5
            ))
            ->addFeature(new TreeFeature(
                treeType: new BirchTree(new Random()),
                chance: 0.2,
                attemptsPerChunk: 6
            ));

        // Taiga
        $taigaDecorator = new BiomeDecorator();
        $taigaDecorator
            ->addFeature(new TreeFeature(
                treeType: new SpruceTree(6, 9),
                chance: 0.5,
                attemptsPerChunk: 7
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::FERN()
                ],
                chance: 0.2,
                attemptsPerChunk: 4,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::TALL_GRASS()
                ],
                chance: 0.5,
                attemptsPerChunk: 9,
                clusterSize: 6
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::BROWN_MUSHROOM(),
                    VanillaBlocks::BROWN_MUSHROOM()
                ],
                chance: 0.15,
                attemptsPerChunk: 4,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::SWEET_BERRY_BUSH()->setAge(SweetBerryBush::STAGE_MATURE)
                ],
                chance: 0.15,
                attemptsPerChunk: 4,
                clusterSize: 6
            ));
        
        // Swamp
        $swampDecorator = new BiomeDecorator();
        $swampDecorator
            ->addFeature(new TreeFeature(
                treeType: new SwampTree(),
                chance: 0.5,
                attemptsPerChunk: 7
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::BROWN_MUSHROOM(),
                    VanillaBlocks::RED_MUSHROOM()
                ],
                chance: 0.20,
                attemptsPerChunk: 3,
                clusterSize: 3
            ));

        // Dark Oak Forest
        $roofedforestDecorator = new BiomeDecorator();
        $roofedforestDecorator
            ->addFeature(new TreeFeature(
                treeType: new DarkOakTree(new Random()),
                chance: 0.7,
                attemptsPerChunk: 10
            ))
            ->addFeature(new TreeFeature(
                treeType: new OakTree(new Random()),
                chance: 0.2,
                attemptsPerChunk: 2
            ))
            ->addFeature(new TreeFeature(
                treeType: new BirchTree(new Random()),
                chance: 0.2,
                attemptsPerChunk: 2
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::TALL_GRASS()
                ],
                chance: 0.3,
                attemptsPerChunk: 5,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::BROWN_MUSHROOM(),
                    VanillaBlocks::RED_MUSHROOM()
                ],
                chance: 0.20,
                attemptsPerChunk: 3,
                clusterSize: 3
            ))
            ->addFeature(new FlowerFeature(
                flowers: [
                    VanillaBlocks::DANDELION(),
                    VanillaBlocks::POPPY(),
                    VanillaBlocks::AZURE_BLUET(),
                    VanillaBlocks::OXEYE_DAISY()
                ],
                chance: 0.15,
                attemptsPerChunk: 8,
                clusterSize: 3
            ));

        // BIOMES
        self::registerBiome(0, new Biome(
            name: "Plains",
            height: 20,
            layers: [
                BiomeLayer::absolute(VanillaBlocks::SNOW(), 120),
                BiomeLayer::surface(VanillaBlocks::GRASS(), 1),
                BiomeLayer::relativeRange(VanillaBlocks::STONE(), 1, 5),
            ],
            undergroundBlock: VanillaBlocks::STONE(),
            climate: BiomeClimate::temperate(0.6, 0.4),
            vanillaBiomeId: BiomeIds::PLAINS,
            decorator: $plainsDecorator,
            sizeMultiplier: 1.5
        ));

        self::registerBiome(1, new Biome(
            name: "Oak Forest",
            height: 25,
            layers: [
                BiomeLayer::absolute(VanillaBlocks::SNOW_LAYER(), 130),
                BiomeLayer::absolute(VanillaBlocks::GRASS(), 100, 129),
                BiomeLayer::surface(VanillaBlocks::GRASS(), 1),
                BiomeLayer::relativeRange(VanillaBlocks::STONE(), 1, 3),
            ],
            undergroundBlock: VanillaBlocks::STONE(),
            climate: BiomeClimate::temperate(0.7, 0.6),
            vanillaBiomeId: BiomeIds::PLAINS,
            decorator: $oakForestDecorator,
            sizeMultiplier: 0.9
        ));

        self::registerBiome(2, new Biome(
            name: "Swamp",
            height: 2,
            layers: [
                BiomeLayer::surface(VanillaBlocks::GRASS(), 2),
                BiomeLayer::relativeRange(VanillaBlocks::STONE(), 2, 4),
            ],
            undergroundBlock: VanillaBlocks::STONE(),
            climate: BiomeClimate::temperate(0.8, 0.3),
            vanillaBiomeId: BiomeIds::SWAMPLAND,
            decorator: $swampDecorator,
            sizeMultiplier: 0.4
        ));

        self::registerBiome(3, new Biome(
            name: "Spruce Peaks",
            height: 80,
            layers: [
                BiomeLayer::absolute(VanillaBlocks::SNOW(), 90),
                BiomeLayer::surface(VanillaBlocks::GRASS(), 1),
                BiomeLayer::relativeRange(VanillaBlocks::STONE(), 1, 3),
            ],
            undergroundBlock: VanillaBlocks::STONE(),
            climate: BiomeClimate::cold(0.3),
            vanillaBiomeId: BiomeIds::FROZEN_PEAKS,
            decorator: $taigaDecorator,
            sizeMultiplier: 1.5
        ));
        
        self::registerBiome(4, new Biome(
            name: "Dark Oak Forest",
            height: 10,
            layers: [
                BiomeLayer::absolute(VanillaBlocks::SNOW(), 90),
                BiomeLayer::surface(VanillaBlocks::GRASS(), 1),
                BiomeLayer::relativeRange(VanillaBlocks::STONE(), 1, 3),
            ],
            undergroundBlock: VanillaBlocks::STONE(),
            climate: BiomeClimate::temperate(0.7, 0.8),
            vanillaBiomeId: BiomeIds::ROOFED_FOREST,
            decorator: $roofedforestDecorator,
            sizeMultiplier: 1.0
        ));
    }

    public static function registerBiome(int $id, Biome $biome): void {
        self::$biomes[$id] = $biome;
    }

    public static function getBiome(int $id): ?Biome {
        return self::$biomes[$id] ?? null;
    }

    public static function getAllBiomes(): array {
        return self::$biomes;
    }

    public static function getBiomeCount(): int {
        return count(self::$biomes);
    }

    public static function reset(): void {
        self::$biomes = [];
    }
}
