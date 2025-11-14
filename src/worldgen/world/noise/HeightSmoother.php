<?php

declare(strict_types=1);

namespace worldgen\world\noise;

use pocketmine\world\ChunkManager;
use worldgen\world\noise\VoronoiCalculator;
use worldgen\world\noise\HeightCalculator;

/**
 * Glättet Höhen über Chunk-Grenzen hinweg mittels Gaußschem Blur
 */
class HeightSmoother {

    private VoronoiCalculator $voronoiCalculator;
    private HeightCalculator $heightCalculator;
    private int $biomeCount;
    private int $smoothingRadius;

    public function __construct(
        VoronoiCalculator $voronoiCalculator,
        HeightCalculator $heightCalculator,
        int $biomeCount,
        int $smoothingRadius = 3
    ) {
        $this->voronoiCalculator = $voronoiCalculator;
        $this->heightCalculator = $heightCalculator;
        $this->biomeCount = $biomeCount;
        $this->smoothingRadius = $smoothingRadius;
    }

    /**
     * Berechnet geglättete Höhe mit Cross-Border-Sampling
     */
    public function smoothHeight(int $worldX, int $worldZ, ChunkManager $world, int $chunkX, int $chunkZ): int {
        $heights = [];
        $radius = $this->smoothingRadius;

        // Sample in größerem Radius, auch über Chunk-Grenzen
        for ($dx = -$radius; $dx <= $radius; $dx++) {
            for ($dz = -$radius; $dz <= $radius; $dz++) {
                $sampleX = $worldX + $dx;
                $sampleZ = $worldZ + $dz;
                
                // Berechne Höhe für diese Position
                $voronoi = $this->voronoiCalculator->calculate($sampleX, $sampleZ, $this->biomeCount);
                $height = $this->heightCalculator->calculate(
                    $sampleX, 
                    $sampleZ, 
                    $voronoi['biomeIdx'], 
                    $voronoi['edgeDistance']
                );
                
                // Gaußsche Gewichtung basierend auf Distanz
                $dist = sqrt($dx * $dx + $dz * $dz);
                $sigma = $radius / 2.0;
                $weight = exp(-($dist * $dist) / (2.0 * $sigma * $sigma));
                
                $heights[] = ['height' => $height, 'weight' => $weight];
            }
        }

        // Gewichteter Durchschnitt
        $totalWeight = 0;
        $weightedSum = 0;
        
        foreach ($heights as $h) {
            $totalWeight += $h['weight'];
            $weightedSum += $h['height'] * $h['weight'];
        }

        if ($totalWeight == 0) {
            $voronoi = $this->voronoiCalculator->calculate($worldX, $worldZ, $this->biomeCount);
            return $this->heightCalculator->calculate(
                $worldX, 
                $worldZ, 
                $voronoi['biomeIdx'], 
                $voronoi['edgeDistance']
            );
        }

        return (int)round($weightedSum / $totalWeight);
    }
}
