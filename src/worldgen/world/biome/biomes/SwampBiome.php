<?php

declare(strict_types=1);

namespace worldgen\world\biome\biomes;

use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use worldgen\world\biome\Biome;
use worldgen\world\biome\BiomeClimate;
use worldgen\world\biome\BiomeLayer;
use worldgen\world\decoration\BiomeDecorator;
use worldgen\world\decoration\features\FlowerFeature;
use worldgen\world\decoration\features\TreeFeature;
use worldgen\world\decoration\tree\SwampTree;

class SwampBiome extends Biome {

    protected function initialize(): void {
        $this->name = "Swamp";
        $this->height = 2;
        $this->undergroundBlock = VanillaBlocks::STONE();
        $this->climate = BiomeClimate::temperate(0.8, 0.3);
        $this->vanillaBiomeId = BiomeIds::SWAMPLAND;
        $this->sizeMultiplier = 0.7;
    }

    protected function configureLayers(): array {
        return [
            BiomeLayer::surface(VanillaBlocks::GRASS(), 2),
            BiomeLayer::relativeRange(VanillaBlocks::STONE(), 2, 4),
        ];
    }

    protected function configureDecorator(): BiomeDecorator {
        $decorator = new BiomeDecorator();

        $decorator->addFeature(new TreeFeature(
            treeType: new SwampTree(),
            chance: 0.5,
            attemptsPerChunk: 7
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [
                VanillaBlocks::BROWN_MUSHROOM(),
                VanillaBlocks::RED_MUSHROOM()
            ],
            chance: 0.20,
            attemptsPerChunk: 3,
            clusterSize: 3
        ));

        return $decorator;
    }
}