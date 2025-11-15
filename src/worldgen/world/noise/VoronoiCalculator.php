<?php

declare(strict_types=1);

namespace worldgen\world\noise;

use pocketmine\world\generator\noise\Simplex;
use worldgen\world\biome\BiomeRegistry;
use worldgen\world\biome\BiomeCompatibility;

/**
 * Berechnet Voronoi-Diagramme für die Biom-Verteilung
 * Berücksichtigt Biom-Größen und Kompatibilitätsregeln
 */
class VoronoiCalculator {

    private Simplex $primaryEdgeNoise;
    private Simplex $secondaryEdgeNoise;
    
    private int $scale;
    private float $primaryEdgeNoiseAmplitude;
    private int $primaryEdgeNoiseScale;
    private float $secondaryEdgeNoiseAmplitude;
    private int $secondaryEdgeNoiseScale;
    
    private bool $enforceCompatibility;
    private float $compatibilityPenalty;

    public function __construct(
        Simplex $primaryEdgeNoise,
        Simplex $secondaryEdgeNoise,
        int $scale = 12,
        float $primaryEdgeNoiseAmplitude = 0.4,
        int $primaryEdgeNoiseScale = 64,
        float $secondaryEdgeNoiseAmplitude = 0.2,
        int $secondaryEdgeNoiseScale = 8,
        bool $enforceCompatibility = true,
        float $compatibilityPenalty = 2.0
    ) {
        $this->primaryEdgeNoise = $primaryEdgeNoise;
        $this->secondaryEdgeNoise = $secondaryEdgeNoise;
        $this->scale = $scale;
        $this->primaryEdgeNoiseAmplitude = $primaryEdgeNoiseAmplitude;
        $this->primaryEdgeNoiseScale = $primaryEdgeNoiseScale;
        $this->secondaryEdgeNoiseAmplitude = $secondaryEdgeNoiseAmplitude;
        $this->secondaryEdgeNoiseScale = $secondaryEdgeNoiseScale;
        $this->enforceCompatibility = $enforceCompatibility;
        $this->compatibilityPenalty = $compatibilityPenalty;
    }

    /**
     * Berechnet Voronoi-Daten für eine bestimmte Position
     * Berücksichtigt Biom-Größe und Kompatibilität
     * 
     * @return array{biomeIdx: int, edgeDistance: float}
     */
    public function calculate(int $worldX, int $worldZ, int $biomeCount): array {
        $primaryEdgeNoise = $this->primaryEdgeNoise->noise2D(
                $worldX / $this->primaryEdgeNoiseScale + 100,
                $worldZ / $this->primaryEdgeNoiseScale + 100
            ) * $this->primaryEdgeNoiseAmplitude;

        $secondaryEdgeNoise = $this->secondaryEdgeNoise->noise2D(
                $worldX / $this->secondaryEdgeNoiseScale + 100,
                $worldZ / $this->secondaryEdgeNoiseScale + 100
            ) * $this->secondaryEdgeNoiseAmplitude;

        $points = $this->getRandomPoints($worldX, $worldZ);

        $n1 = 999.0;
        $n2 = 999.0;
        $closestBiomeIdx = 0;
        $secondClosestBiomeIdx = 0;

        foreach ($points as $point) {
            $biome = BiomeRegistry::getBiome($point['biomeIdx']);
            $sizeMultiplier = $biome ? $biome->getSizeMultiplier() : 1.0;

            $scaledPx = $worldX / (16.0 * $this->scale * $sizeMultiplier);
            $scaledPz = $worldZ / (16.0 * $this->scale * $sizeMultiplier);
            
            $scaledPointX = $point['x'] / $sizeMultiplier;
            $scaledPointZ = $point['z'] / $sizeMultiplier;

            $pxFrac = $scaledPx - floor($scaledPx) - 0.5;
            $pzFrac = $scaledPz - floor($scaledPz) - 0.5;
            $edgeFactor = min(0.5 - abs($pxFrac), 0.5 - abs($pzFrac));
            $offset = ($primaryEdgeNoise + $secondaryEdgeNoise) * $edgeFactor;

            $adjustedPx = $scaledPx + $offset;
            $adjustedPz = $scaledPz + $offset;

            $dx = $scaledPointX - $adjustedPx;
            $dz = $scaledPointZ - $adjustedPz;
            $d2 = $dx * $dx + $dz * $dz;

            if ($this->enforceCompatibility && $n1 < 999.0) {
                $currentClosestBiome = $closestBiomeIdx;
                $candidateBiome = $point['biomeIdx'];
                
                if (!BiomeCompatibility::areCompatible($currentClosestBiome, $candidateBiome)) {
                    $d2 *= $this->compatibilityPenalty;
                }
            }

            if ($d2 < $n1) {
                $n2 = $n1;
                $secondClosestBiomeIdx = $closestBiomeIdx;
                $n1 = $d2;
                $closestBiomeIdx = $point['biomeIdx'];
            } elseif ($d2 < $n2) {
                $n2 = $d2;
                $secondClosestBiomeIdx = $point['biomeIdx'];
            }
        }

        if ($this->enforceCompatibility && 
            !BiomeCompatibility::areCompatible($closestBiomeIdx, $secondClosestBiomeIdx)) {

            $closestBiomeIdx = $this->findCompatibleAlternative(
                $worldX, 
                $worldZ, 
                $closestBiomeIdx, 
                $secondClosestBiomeIdx,
                $points
            );
        }

        $edgeDistance = abs(sqrt($n2) - sqrt($n1));

        return [
            'biomeIdx' => $closestBiomeIdx,
            'edgeDistance' => $edgeDistance
        ];
    }

    /**
     * Findet ein kompatibles alternatives Biom wenn die nächsten zwei inkompatibel sind
     */
    private function findCompatibleAlternative(
        int $worldX,
        int $worldZ,
        int $primaryBiome,
        int $secondaryBiome,
        array $points
    ): int {
        $preferredNeighbors = BiomeCompatibility::getPreferredNeighbors($primaryBiome);
        
        if (!empty($preferredNeighbors)) {
            foreach ($points as $point) {
                if (in_array($point['biomeIdx'], $preferredNeighbors, true)) {
                    return $point['biomeIdx'];
                }
            }
        }

        foreach ($points as $point) {
            if ($point['biomeIdx'] !== $secondaryBiome && 
                BiomeCompatibility::areCompatible($primaryBiome, $point['biomeIdx'])) {
                return $point['biomeIdx'];
            }
        }

        return $primaryBiome;
    }

    private function hash2D(float $x, float $z): array {
        $p3x = fmod($x * 0.1031, 1.0);
        $p3y = fmod($z * 0.1030, 1.0);
        $p3z = fmod($x * 0.0973, 1.0);

        $p3d = $p3x * ($p3y + 33.33) + $p3y * ($p3z + 33.33) + $p3z * ($p3x + 33.33);
        $p3x += $p3d;
        $p3y += $p3d;
        $p3z += $p3d;

        $rx = fmod(($p3x + $p3y) * $p3z, 1.0);
        $rz = fmod(($p3x + $p3z) * $p3y, 1.0);

        return [$rx, $rz];
    }

    private function getRandomPoints(float $worldX, float $worldZ): array {
        $points = [];
        $baseScale = 16.0 * $this->scale;
        
        for ($i = 0; $i < 9; $i++) {
            $xOffset = ($i % 3) - 1;
            $zOffset = floor($i / 3) - 1;

            $gridX = floor($worldX / $baseScale) + $xOffset;
            $gridZ = floor($worldZ / $baseScale) + $zOffset;

            [$rx, $rz] = $this->hash2D($gridX, $gridZ);
            
            $biomeIdx = (int)floor($rx * BiomeRegistry::getBiomeCount());

            $points[] = [
                'x' => $gridX + $rx,
                'z' => $gridZ + $rz,
                'biomeIdx' => $biomeIdx
            ];
        }

        return $points;
    }
    
    /**
     * Aktiviert/Deaktiviert Kompatibilitätsprüfung
     */
    public function setEnforceCompatibility(bool $enforce): void {
        $this->enforceCompatibility = $enforce;
    }
    
    /**
     * Setzt den Penalty-Faktor für inkompatible Biome
     */
    public function setCompatibilityPenalty(float $penalty): void {
        $this->compatibilityPenalty = max(1.0, $penalty);
    }
}
