<?php

declare(strict_types=1);

namespace worldgen\world;

use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use worldgen\world\biome\BiomeRegistry;
use worldgen\world\biome\BiomeCompatibility;
use worldgen\world\noise\VoronoiCalculator;
use worldgen\world\noise\HeightCalculator;
use worldgen\world\noise\HeightSmoother;
use worldgen\world\ChunkPopulator;

/**
 * Hauptklasse für die Voronoi-basierte Weltgenerierung
 * Delegiert die Arbeit an spezialisierte Komponenten
 */
class BetterGenerator extends Generator {

    private VoronoiCalculator $voronoiCalculator;
    private HeightCalculator $heightCalculator;
    private HeightSmoother $heightSmoother;
    private ChunkPopulator $chunkPopulator;

    public function __construct(int $seed, string $preset) {
        parent::__construct($seed, $preset);

        BiomeRegistry::registerDefaultBiomes();
        
        BiomeCompatibility::registerDefaultRules();

        $primaryEdgeNoise = new Simplex(new Random($seed), 4, 0.5, 1.0);
        $secondaryEdgeNoise = new Simplex(new Random($seed + 1), 4, 0.5, 1.0);
        $primaryHeightNoise = new Simplex(new Random($seed + 2), 4, 0.5, 1.0);
        $secondaryHeightNoise = new Simplex(new Random($seed + 3), 4, 0.5, 1.0);

        $this->voronoiCalculator = new VoronoiCalculator(
            primaryEdgeNoise: $primaryEdgeNoise,
            secondaryEdgeNoise: $secondaryEdgeNoise,
            scale: 8,
            primaryEdgeNoiseAmplitude: 0.4,
            primaryEdgeNoiseScale: 64,
            secondaryEdgeNoiseAmplitude: 0.2,
            secondaryEdgeNoiseScale: 8,
            enforceCompatibility: true,
            compatibilityPenalty: 2.5
        );

        $this->heightCalculator = new HeightCalculator(
            primaryHeightNoise: $primaryHeightNoise,
            secondaryHeightNoise: $secondaryHeightNoise,
            primaryHeightOffsetAmplitude: 5.0,
            primaryHeightOffsetScale: 64,
            secondaryHeightOffsetAmplitude: 2.0,
            secondaryHeightOffsetScale: 16
        );

        $biomeCount = BiomeRegistry::getBiomeCount();

        $this->heightSmoother = new HeightSmoother(
            voronoiCalculator: $this->voronoiCalculator,
            heightCalculator: $this->heightCalculator,
            biomeCount: $biomeCount,
            smoothingRadius: 3
        );

        $this->chunkPopulator = new ChunkPopulator(
            voronoiCalculator: $this->voronoiCalculator,
            heightSmoother: $this->heightSmoother,
            biomeCount: $biomeCount
        );
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $this->chunkPopulator->populate($world, $chunkX, $chunkZ);
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $random = new Random($this->seed ^ ($chunkX << 16) ^ $chunkZ);
        
        $heightMapCallback = function(int $worldX, int $worldZ) use ($world, $chunkX, $chunkZ): int {
            return 64 + $this->heightSmoother->smoothHeight($worldX, $worldZ, $world, $chunkX, $chunkZ);
        };

        $biomeCount = BiomeRegistry::getBiomeCount();
        $biomesInChunk = [];
        
        for ($x = 0; $x < 16; $x += 4) {
            for ($z = 0; $z < 16; $z += 4) {
                $worldX = ($chunkX << 4) + $x;
                $worldZ = ($chunkZ << 4) + $z;
                
                $voronoi = $this->voronoiCalculator->calculate($worldX, $worldZ, $biomeCount);
                $biomeIdx = $voronoi['biomeIdx'];
                
                if (!isset($biomesInChunk[$biomeIdx])) {
                    $biomesInChunk[$biomeIdx] = BiomeRegistry::getBiome($biomeIdx);
                }
            }
        }

        foreach ($biomesInChunk as $biome) {
            if ($biome !== null) {
                $biome->getDecorator()->decorate($world, $random, $chunkX, $chunkZ, $heightMapCallback);
            }
        }
    }
}
