<?php

declare(strict_types=1);

namespace worldgen\world\biome;

use pocketmine\block\Block;

class BiomeLayer {

    private Block $block;
    private string $type;
    private ?int $absoluteMinY;
    private ?int $absoluteMaxY;
    private ?int $relativeMinY;
    private ?int $relativeMaxY;
    private int $thickness;

    /**
     * Erstellt einen neuen Biome-Layer
     * 
     * @param Block $block Der Block-Typ für diesen Layer
     * @param string $type Layer-Typ:
     *   - "absolute": Basiert auf absoluter Y-Koordinate (z.B. "Schnee ab Y=120")
     *   - "relative_top": Basiert auf Distanz von Oberfläche nach unten (z.B. "oberste 3 Blöcke")
     *   - "relative_range": Basiert auf Distanz-Bereich von Oberfläche
     * @param int|null $absoluteMinY Minimale absolute Y-Koordinate (für type="absolute")
     * @param int|null $absoluteMaxY Maximale absolute Y-Koordinate (für type="absolute")
     * @param int|null $relativeMinY Minimale relative Y-Distanz zur Oberfläche (für type="relative_*")
     * @param int|null $relativeMaxY Maximale relative Y-Distanz zur Oberfläche (für type="relative_*")
     * @param int $thickness Dicke des Layers (für type="relative_top")
     */
    public function __construct(
        Block $block,
        string $type = "relative_top",
        ?int $absoluteMinY = null,
        ?int $absoluteMaxY = null,
        ?int $relativeMinY = null,
        ?int $relativeMaxY = null,
        int $thickness = 1
    ) {
        $this->block = $block;
        $this->type = $type;
        $this->absoluteMinY = $absoluteMinY;
        $this->absoluteMaxY = $absoluteMaxY;
        $this->relativeMinY = $relativeMinY;
        $this->relativeMaxY = $relativeMaxY;
        $this->thickness = $thickness;
    }

    /**
     * Factory method: Erstellt einen Layer basierend auf absoluter Höhe
     * Beispiel: Schnee ab Y=120
     */
    public static function absolute(Block $block, ?int $minY = null, ?int $maxY = null): self {
        return new self($block, "absolute", $minY, $maxY);
    }

    /**
     * Factory method: Erstellt einen Layer für die obersten N Blöcke
     * Beispiel: Oberste 1 Block = Gras
     */
    public static function surface(Block $block, int $thickness = 1): self {
        return new self($block, "relative_top", null, null, null, null, $thickness);
    }

    /**
     * Factory method: Erstellt einen Layer in einem relativen Bereich
     * Beispiel: 1-4 Blöcke unter Oberfläche = Dirt
     */
    public static function relativeRange(Block $block, int $minDepth, int $maxDepth): self {
        return new self($block, "relative_range", null, null, $minDepth, $maxDepth);
    }

    public function getBlock(): Block {
        return $this->block;
    }

    /**
     * Prüft ob dieser Layer für die gegebene Position zutrifft
     */
    public function matches(int $absoluteY, int $surfaceY, int $relativeY): bool {
        switch ($this->type) {
            case "absolute":
                // Prüfe absolute Y-Koordinate
                if ($this->absoluteMinY !== null && $absoluteY < $this->absoluteMinY) {
                    return false;
                }
                if ($this->absoluteMaxY !== null && $absoluteY > $this->absoluteMaxY) {
                    return false;
                }
                // Muss auch an der Oberfläche oder darüber sein
                return $relativeY >= 0;

            case "relative_top":
                // Prüfe ob innerhalb der thickness von der Oberfläche
                return $relativeY >= 0 && $relativeY < $this->thickness;

            case "relative_range":
                // Prüfe ob innerhalb des relativen Bereichs (negativ = unter Oberfläche)
                $depth = -$relativeY;
                if ($this->relativeMinY !== null && $depth < $this->relativeMinY) {
                    return false;
                }
                if ($this->relativeMaxY !== null && $depth > $this->relativeMaxY) {
                    return false;
                }
                return true;

            default:
                return false;
        }
    }
}
