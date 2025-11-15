<?php

declare(strict_types=1);

namespace worldgen\world\biome\biomes;

use pocketmine\block\SweetBerryBush;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use worldgen\world\biome\Biome;
use worldgen\world\biome\BiomeClimate;
use worldgen\world\biome\BiomeLayer;
use worldgen\world\decoration\BiomeDecorator;
use worldgen\world\decoration\features\FlowerFeature;
use worldgen\world\decoration\features\TreeFeature;
use worldgen\world\decoration\tree\SpruceTree;

class SprucePeaksBiome extends Biome {

    protected function initialize(): void {
        $this->name = "Spruce Peaks";
        $this->height = 80;
        $this->undergroundBlock = VanillaBlocks::STONE();
        $this->climate = BiomeClimate::cold(0.3);
        $this->vanillaBiomeId = BiomeIds::FROZEN_PEAKS;
        $this->sizeMultiplier = 1.5;
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
            treeType: new SpruceTree(6, 9),
            chance: 0.5,
            attemptsPerChunk: 7
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [VanillaBlocks::FERN()],
            chance: 0.2,
            attemptsPerChunk: 4,
            clusterSize: 3
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [VanillaBlocks::TALL_GRASS()],
            chance: 0.5,
            attemptsPerChunk: 9,
            clusterSize: 6
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [
                VanillaBlocks::BROWN_MUSHROOM(),
                VanillaBlocks::BROWN_MUSHROOM()
            ],
            chance: 0.15,
            attemptsPerChunk: 4,
            clusterSize: 3
        ));

        $decorator->addFeature(new FlowerFeature(
            flowers: [
                VanillaBlocks::SWEET_BERRY_BUSH()->setAge(SweetBerryBush::STAGE_MATURE)
            ],
            chance: 0.15,
            attemptsPerChunk: 4,
            clusterSize: 6
        ));

        return $decorator;
    }
}