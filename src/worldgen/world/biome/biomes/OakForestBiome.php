<?php

declare(strict_types=1);

namespace worldgen\world\biome\biomes;

use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\utils\Random;
use worldgen\world\biome\Biome;
use worldgen\world\biome\BiomeClimate;
use worldgen\world\biome\BiomeLayer;
use worldgen\world\blocks\ExtraVanillaBlocks;
use worldgen\world\decoration\BiomeDecorator;
use worldgen\world\decoration\features\FlowerFeature;
use worldgen\world\decoration\features\TreeFeature;
use worldgen\world\decoration\tree\OakTree;
use worldgen\world\decoration\tree\BigOakTree;
use worldgen\world\decoration\tree\BirchTree;

class OakForestBiome extends Biome {

    protected function initialize(): void {
        $this->name = "Oak Forest";
        $this->height = 25;
        $this->undergroundBlock = VanillaBlocks::STONE();
        $this->climate = BiomeClimate::temperate(0.7, 0.6);
        $this->vanillaBiomeId = BiomeIds::PLAINS;
        $this->sizeMultiplier = 1.5;
    }

    protected function configureLayers(): array {
        return [
            BiomeLayer::absolute(VanillaBlocks::SNOW_LAYER(), 130),
            BiomeLayer::absolute(VanillaBlocks::GRASS(), 100, 129),
            BiomeLayer::surface(VanillaBlocks::GRASS(), 1),
            BiomeLayer::relativeRange(VanillaBlocks::STONE(), 1, 3),
        ];
    }

    protected function configureDecorator(): BiomeDecorator {
        $decorator = new BiomeDecorator();

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

        $decorator->addFeature(new FlowerFeature(
            flowers: [ExtraVanillaBlocks::BUSH()],
            chance: 0.2,
            attemptsPerChunk: 2,
            clusterSize: 3
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [ExtraVanillaBlocks::FIREFLY_BUSH()],
            chance: 0.4,
            attemptsPerChunk: 4,
            clusterSize: 3,
            onlyNearWater: true
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [VanillaBlocks::TALL_GRASS()],
            chance: 0.5,
            attemptsPerChunk: 12,
            clusterSize: 7
        ));

        $decorator->addFeature(new TreeFeature(
            treeType: new OakTree(new Random()),
            chance: 0.5,
            attemptsPerChunk: 8
        ));

        $decorator->addFeature(new TreeFeature(
            treeType: new BigOakTree(new Random()),
            chance: 0.18,
            attemptsPerChunk: 5
        ));

        $decorator->addFeature(new TreeFeature(
            treeType: new BirchTree(new Random()),
            chance: 0.2,
            attemptsPerChunk: 6
        ));

        return $decorator;
    }
}