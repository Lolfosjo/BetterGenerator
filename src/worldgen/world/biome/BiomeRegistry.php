<?php

declare(strict_types=1);

namespace worldgen\world\biome;

use worldgen\world\biome\biomes\PlainsBiome;
use worldgen\world\biome\biomes\OakForestBiome;
use worldgen\world\biome\biomes\SwampBiome;
use worldgen\world\biome\biomes\SprucePeaksBiome;
use worldgen\world\biome\biomes\DarkOakForestBiome;

class BiomeRegistry {

    /** @var Biome[] */
    private static array $biomes = [];

    public const PLAINS = 1;

    /**
     * Registriert alle Standard-Biome
     * Diese Methode lädt alle Biome aus den einzelnen Dateien
     */
    public static function registerDefaultBiomes(): void {
        self::registerBiomeFromClass(0, PlainsBiome::class);
        self::registerBiomeFromClass(1, OakForestBiome::class);
        self::registerBiomeFromClass(2, SwampBiome::class);
        self::registerBiomeFromClass(3, SprucePeaksBiome::class);
        self::registerBiomeFromClass(4, DarkOakForestBiome::class);
    }

    /**
     * Registriert ein Biome aus einer Klasse
     *
     * @param int $id Biome-ID
     * @param string $className Vollständiger Klassenname (mit Namespace)
     */
    public static function registerBiomeFromClass(int $id, string $className): void {
        if (!class_exists($className)) {
            throw new \RuntimeException("Biome class '$className' not found!");
        }

        if (!is_subclass_of($className, Biome::class)) {
            throw new \RuntimeException("Biome class '$className' must extend AbstractBiome!");
        }

        /** @var Biome $biomeInstance */
        $biomeInstance = new $className();

        self::$biomes[$id] = $biomeInstance;
    }

    /**
     * Registriert ein Biome direkt (für fortgeschrittene Verwendung)
     */
    public static function registerBiome(int $id, Biome $biome): void {
        self::$biomes[$id] = $biome;
    }

    /**
     * Gibt ein Biome anhand seiner ID zurück
     */
    public static function getBiome(int $id): ?Biome {
        return self::$biomes[$id] ?? null;
    }

    /**
     * Gibt alle registrierten Biome zurück
     * @return Biome[]
     */
    public static function getAllBiomes(): array {
        return self::$biomes;
    }

    /**
     * Gibt die Anzahl der registrierten Biome zurück
     */
    public static function getBiomeCount(): int {
        return count(self::$biomes);
    }

    /**
     * Setzt die Registry zurück (für Tests oder Reload)
     */
    public static function reset(): void {
        self::$biomes = [];
    }
}