<?php

declare(strict_types=1);

namespace worldgen\world\noise;

use pocketmine\world\generator\noise\Simplex;
use worldgen\world\biome\BiomeRegistry;

/**
 * Berechnet die Höhe des Terrains basierend auf Biom und Noise
 */
class HeightCalculator {

    private Simplex $primaryHeightNoise;
    private Simplex $secondaryHeightNoise;
    
    private float $primaryHeightOffsetAmplitude;
    private int $primaryHeightOffsetScale;
    private float $secondaryHeightOffsetAmplitude;
    private int $secondaryHeightOffsetScale;

    public function __construct(
        Simplex $primaryHeightNoise,
        Simplex $secondaryHeightNoise,
        float $primaryHeightOffsetAmplitude = 5.0,
        int $primaryHeightOffsetScale = 64,
        float $secondaryHeightOffsetAmplitude = 2.0,
        int $secondaryHeightOffsetScale = 16
    ) {
        $this->primaryHeightNoise = $primaryHeightNoise;
        $this->secondaryHeightNoise = $secondaryHeightNoise;
        $this->primaryHeightOffsetAmplitude = $primaryHeightOffsetAmplitude;
        $this->primaryHeightOffsetScale = $primaryHeightOffsetScale;
        $this->secondaryHeightOffsetAmplitude = $secondaryHeightOffsetAmplitude;
        $this->secondaryHeightOffsetScale = $secondaryHeightOffsetScale;
    }

    /**
     * Berechnet die Höhe für eine bestimmte Position
     */
    public function calculate(int $worldX, int $worldZ, int $biomeIdx, float $edgeDistance): int {
        $biome = BiomeRegistry::getBiome($biomeIdx);
        $biomeHeight = $biome ? $biome->getHeight() : 0;

        $smoothEdge = $this->smoothstep($edgeDistance);

        $primaryHeightOffset = $this->primaryHeightNoise->noise2D(
                $worldX / $this->primaryHeightOffsetScale + 200,
                $worldZ / $this->primaryHeightOffsetScale + 200
            ) * $this->primaryHeightOffsetAmplitude;

        $secondaryHeightOffset = $this->secondaryHeightNoise->noise2D(
                $worldX / $this->secondaryHeightOffsetScale,
                $worldZ / $this->secondaryHeightOffsetScale
            ) * $this->secondaryHeightOffsetAmplitude;

        $baseHeight = $biomeHeight * $smoothEdge;
        $noiseHeight = $primaryHeightOffset + $secondaryHeightOffset;
        
        $blendFactor = pow($smoothEdge, 1.5);
        $height = $baseHeight + $noiseHeight * (0.5 + $blendFactor * 0.5);

        return (int)round($height);
    }

    private function smoothstep(float $x): float {
        $x = max(0.0, min(1.0, $x));
        return $x * $x * (3.0 - 2.0 * $x);
    }
}
