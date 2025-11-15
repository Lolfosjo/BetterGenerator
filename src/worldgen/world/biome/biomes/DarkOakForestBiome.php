<?php

declare(strict_types=1);

namespace worldgen\world\biome\biomes;

use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\utils\Random;
use worldgen\world\biome\Biome;
use worldgen\world\biome\BiomeClimate;
use worldgen\world\biome\BiomeLayer;
use worldgen\world\decoration\BiomeDecorator;
use worldgen\world\decoration\features\FlowerFeature;
use worldgen\world\decoration\features\TreeFeature;
use worldgen\world\decoration\tree\DarkOakTree;
use worldgen\world\decoration\tree\OakTree;
use worldgen\world\decoration\tree\BirchTree;

class DarkOakForestBiome extends Biome {

    protected function initialize(): void {
        $this->name = "Dark Oak Forest";
        $this->height = 10;
        $this->undergroundBlock = VanillaBlocks::STONE();
        $this->climate = BiomeClimate::temperate(0.7, 0.8);
        $this->vanillaBiomeId = BiomeIds::ROOFED_FOREST;
        $this->sizeMultiplier = 1.0;
    }

    protected function configureLayers(): array {
        return [
            BiomeLayer::absolute(VanillaBlocks::SNOW(), 90),
            BiomeLayer::surface(VanillaBlocks::GRASS(), 1),
            BiomeLayer::relativeRange(VanillaBlocks::STONE(), 1, 3),
        ];
    }

    protected function configureDecorator(): BiomeDecorator {
        $decorator = new BiomeDecorator();

        $decorator->addFeature(new TreeFeature(
            treeType: new DarkOakTree(new Random()),
            chance: 0.7,
            attemptsPerChunk: 10
        ));

        $decorator->addFeature(new TreeFeature(
            treeType: new OakTree(new Random()),
            chance: 0.2,
            attemptsPerChunk: 2
        ));

        $decorator->addFeature(new TreeFeature(
            treeType: new BirchTree(new Random()),
            chance: 0.2,
            attemptsPerChunk: 2
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [VanillaBlocks::TALL_GRASS()],
            chance: 0.3,
            attemptsPerChunk: 5,
            clusterSize: 3
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

        $decorator->addFeature(new FlowerFeature(
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

        return $decorator;
    }
}