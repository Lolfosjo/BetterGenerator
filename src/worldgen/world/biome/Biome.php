<?php

declare(strict_types=1);

namespace worldgen\world\biome;

use pocketmine\block\Block;
use worldgen\world\decoration\BiomeDecorator;

class Biome {

    private string $name;
    private int $height;
    /** @var BiomeLayer[] */
    private array $layers;
    private Block $undergroundBlock;
    private BiomeClimate $climate;
    private ?int $vanillaBiomeId;
    private BiomeDecorator $decorator;
    private float $sizeMultiplier;

    /**
     * @param string $name Biome-Name
     * @param int $height Basis-Höhe des Biomes
     * @param BiomeLayer[] $layers Layer von oben nach unten (werden in Reihenfolge geprüft)
     * @param Block $undergroundBlock Block für tief unter der Oberfläche
     * @param BiomeClimate|null $climate Klima-Einstellungen (optional)
     * @param int|null $vanillaBiomeId Vanilla Minecraft Biome-ID für Grasfarbe (optional)
     * @param BiomeDecorator|null $decorator Decorator für Vegetationen/Features (optional)
     * @param float $sizeMultiplier Größen-Multiplikator (1.0 = normal, 0.5 = halb so groß, 2.0 = doppelt so groß)
     */
    public function __construct(
        string $name,
        int $height,
        array $layers,
        Block $undergroundBlock,
        ?BiomeClimate $climate = null,
        ?int $vanillaBiomeId = null,
        ?BiomeDecorator $decorator = null,
        float $sizeMultiplier = 1.0
    ) {
        $this->name = $name;
        $this->height = $height;
        $this->layers = $layers;
        $this->undergroundBlock = $undergroundBlock;
        $this->climate = $climate ?? BiomeClimate::temperate();
        $this->vanillaBiomeId = $vanillaBiomeId;
        $this->decorator = $decorator ?? new BiomeDecorator();
        $this->sizeMultiplier = max(0.1, min(5.0, $sizeMultiplier)); // Limit zwischen 0.1 und 5.0
    }

    public function getName(): string {
        return $this->name;
    }

    public function getHeight(): int {
        return $this->height;
    }

    /**
     * @return BiomeLayer[]
     */
    public function getLayers(): array {
        return $this->layers;
    }

    public function getUndergroundBlock(): Block {
        return $this->undergroundBlock;
    }

    public function getClimate(): BiomeClimate {
        return $this->climate;
    }

    public function getVanillaBiomeId(): ?int {
        return $this->vanillaBiomeId;
    }

    public function getDecorator(): BiomeDecorator {
        return $this->decorator;
    }

    /**
     * Gibt den Größen-Multiplikator zurück
     * Kleinere Werte = kleinere Biome, größere Werte = größere Biome
     */
    public function getSizeMultiplier(): float {
        return $this->sizeMultiplier;
    }

    /**
     * Gibt den passenden Block für eine gegebene absolute Y-Koordinate und Oberflächen-Y zurück
     */
    public function getBlockAt(int $y, int $surfaceY): Block {
        $relativeY = $y - $surfaceY;
        
        foreach ($this->layers as $layer) {
            if ($layer->matches($y, $surfaceY, $relativeY)) {
                return $layer->getBlock();
            }
        }
        
        return $this->undergroundBlock;
    }
}
