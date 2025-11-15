<?php

declare(strict_types=1);

namespace worldgen\world\biome;

use pocketmine\block\Block;
use worldgen\world\decoration\BiomeDecorator;

/**
 * Abstrakte Basis-Klasse für alle Biome
 * Jedes Biome sollte von dieser Klasse erben und die abstrakten Methoden implementieren
 */
abstract class Biome {

    protected string $name;
    protected int $height;
    /** @var BiomeLayer[] */
    protected array $layers = [];
    protected Block $undergroundBlock;
    protected BiomeClimate $climate;
    protected ?int $vanillaBiomeId;
    protected BiomeDecorator $decorator;
    protected float $sizeMultiplier;

    /**
     * Initialisiert das Biome mit Standardwerten
     * Wird vom Konstruktor aufgerufen
     */
    abstract protected function initialize(): void;

    /**
     * Konfiguriert die Layer des Biomes
     * @return BiomeLayer[]
     */
    abstract protected function configureLayers(): array;

    /**
     * Konfiguriert den Decorator des Biomes
     */
    abstract protected function configureDecorator(): BiomeDecorator;

    public function __construct() {
        $this->decorator = new BiomeDecorator();
        $this->sizeMultiplier = 1.0;

        $this->initialize();

        $this->layers = $this->configureLayers();
        $this->decorator = $this->configureDecorator();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getHeight(): int {
        return $this->height;
    }

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

    public function getSizeMultiplier(): float {
        return $this->sizeMultiplier;
    }

    /**
     * Gibt den Block für eine bestimmte Y-Position zurück
     * Basierend auf den konfigurierten Layern des Biomes
     *
     * @param int $y Die absolute Y-Koordinate
     * @param int $surfaceY Die Y-Koordinate der Oberfläche
     * @return Block Der Block an dieser Position
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