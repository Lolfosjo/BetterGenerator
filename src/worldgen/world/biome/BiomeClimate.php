<?php

declare(strict_types=1);

namespace worldgen\world\biome;

class BiomeClimate {

    private float $temperature;
    private float $humidity;
    private bool $rainy;
    private bool $snowy;

    /**
     * @param float $temperature Temperatur (0.0 = kalt, 1.0 = warm, >1.0 = heiß)
     *                          - < 0.15: Schnee möglich
     *                          - 0.15 - 0.95: Gemäßigt
     *                          - > 0.95: Heiß (z.B. Dschungel, Wüste)
     * @param float $humidity Luftfeuchtigkeit (0.0 = trocken, 1.0 = feucht)
     *                       - > 0.85: Sehr feucht (beeinflusst Vegetation)
     * @param bool $rainy Ob es in diesem Biome regnen kann
     * @param bool $snowy Ob es in diesem Biome schneien kann (überschreibt Temperatur-Check)
     */
    public function __construct(
        float $temperature = 0.5,
        float $humidity = 0.5,
        bool $rainy = true,
        bool $snowy = false
    ) {
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->rainy = $rainy;
        $this->snowy = $snowy;
    }

    /**
     * Factory: Kaltes Biome (Schnee)
     */
    public static function cold(float $humidity = 0.5): self {
        return new self(0.0, $humidity, true, true);
    }

    /**
     * Factory: Gemäßigtes Biome
     */
    public static function temperate(float $temperature = 0.5, float $humidity = 0.5): self {
        return new self($temperature, $humidity, true, false);
    }

    /**
     * Factory: Warmes/Heißes Biome
     */
    public static function hot(float $humidity = 0.3): self {
        return new self(1.5, $humidity, false, false);
    }

    /**
     * Factory: Wüsten-Biome (heiß und trocken)
     */
    public static function desert(): self {
        return new self(2.0, 0.0, false, false);
    }

    /**
     * Factory: Dschungel-Biome (heiß und feucht)
     */
    public static function jungle(): self {
        return new self(0.95, 0.9, true, false);
    }

    /**
     * Factory: Ozean-Biome
     */
    public static function ocean(float $temperature = 0.5): self {
        return new self($temperature, 1.0, true, false);
    }

    public function getTemperature(): float {
        return $this->temperature;
    }

    public function getHumidity(): float {
        return $this->humidity;
    }

    public function isRainy(): bool {
        return $this->rainy;
    }

    public function isSnowy(): bool {
        return $this->snowy;
    }

    /**
     * Prüft ob Temperatur für Schnee niedrig genug ist
     */
    public function isCold(): bool {
        return $this->temperature < 0.15;
    }

    /**
     * Prüft ob Biome sehr feucht ist
     */
    public function isWet(): bool {
        return $this->humidity > 0.85;
    }

    /**
     * Berechnet variierte Temperatur basierend auf Höhe
     * Höhere Y-Koordinaten = kälter
     */
    public function getVariatedTemperature(int $y): float {
        if ($y > 64) {
            // 0.05 Grad Abnahme pro 30 Blöcke über Y=64
            $heightModifier = ($y - 64) * 0.05 / 30.0;
            return max(0.0, $this->temperature - $heightModifier);
        }
        return $this->temperature;
    }

    /**
     * Prüft ob es bei gegebener Höhe schneien würde
     */
    public function shouldSnowAt(int $y): bool {
        if (!$this->rainy) {
            return false;
        }
        if ($this->snowy) {
            return true;
        }
        return $this->getVariatedTemperature($y) < 0.15;
    }

    /**
     * Prüft ob es bei gegebener Höhe regnen würde
     */
    public function shouldRainAt(int $y): bool {
        if (!$this->rainy) {
            return false;
        }
        return !$this->shouldSnowAt($y);
    }
}
