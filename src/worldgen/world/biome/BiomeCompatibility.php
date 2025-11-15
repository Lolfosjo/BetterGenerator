<?php

declare(strict_types=1);

namespace worldgen\world\biome;

/**
 * Verwaltet Kompatibilitätsregeln zwischen Biomen
 * Definiert welche Biome nicht direkt nebeneinander spawnen dürfen
 */
class BiomeCompatibility {

    /**
     * @var array<int, array<int>> Speichert inkompatible Biome pro Biom-ID
     * Format: [biomeId => [inkompatibleBiomeId1, inkompatibleBiomeId2, ...]]
     */
    private static array $incompatibleBiomes = [];

    /**
     * @var array<int, array<int>> Speichert bevorzugte Nachbar-Biome pro Biom-ID
     * Format: [biomeId => [bevorzugteBiomeId1, bevorzugteBiomeId2, ...]]
     */
    private static array $preferredNeighbors = [];

    /**
     * Registriert Standard-Kompatibilitätsregeln
     */
    public static function registerDefaultRules(): void {
        // Swamp
        self::addIncompatibility(2, [3]); //Spruce Peaks
  
        // Spruce Peaks
        self::addIncompatibility(3, [2]); //Swamp
    }

    /**
     * Fügt eine Inkompatibilität zwischen zwei Biomen hinzu (bidirektional)
     */
    public static function addIncompatibility(int $biomeId, array $incompatibleBiomeIds): void {
        if (!isset(self::$incompatibleBiomes[$biomeId])) {
            self::$incompatibleBiomes[$biomeId] = [];
        }
        
        foreach ($incompatibleBiomeIds as $incompatibleId) {
            if (!in_array($incompatibleId, self::$incompatibleBiomes[$biomeId], true)) {
                self::$incompatibleBiomes[$biomeId][] = $incompatibleId;
            }
            
            // Bidirektional: Füge auch umgekehrte Regel hinzu
            if (!isset(self::$incompatibleBiomes[$incompatibleId])) {
                self::$incompatibleBiomes[$incompatibleId] = [];
            }
            if (!in_array($biomeId, self::$incompatibleBiomes[$incompatibleId], true)) {
                self::$incompatibleBiomes[$incompatibleId][] = $biomeId;
            }
        }
    }

    /**
     * Fügt bevorzugte Nachbar-Biome hinzu
     */
    public static function addPreferredNeighbors(int $biomeId, array $preferredBiomeIds): void {
        self::$preferredNeighbors[$biomeId] = $preferredBiomeIds;
    }

    /**
     * Prüft ob zwei Biome kompatibel sind (nebeneinander spawnen dürfen)
     */
    public static function areCompatible(int $biomeId1, int $biomeId2): bool {
        if (!isset(self::$incompatibleBiomes[$biomeId1])) {
            return true;
        }
        
        return !in_array($biomeId2, self::$incompatibleBiomes[$biomeId1], true);
    }

    /**
     * Gibt alle inkompatiblen Biome für ein bestimmtes Biom zurück
     */
    public static function getIncompatibleBiomes(int $biomeId): array {
        return self::$incompatibleBiomes[$biomeId] ?? [];
    }

    /**
     * Gibt bevorzugte Nachbar-Biome zurück
     */
    public static function getPreferredNeighbors(int $biomeId): array {
        return self::$preferredNeighbors[$biomeId] ?? [];
    }

    /**
     * Berechnet einen Kompatibilitäts-Score zwischen zwei Biomen
     * 1.0 = perfekt kompatibel/bevorzugt
     * 0.5 = neutral kompatibel
     * 0.0 = inkompatibel
     */
    public static function getCompatibilityScore(int $biomeId1, int $biomeId2): float {
        // Inkompatibel = 0
        if (!self::areCompatible($biomeId1, $biomeId2)) {
            return 0.0;
        }
        
        // Bevorzugte Nachbarn = 1.0
        if (isset(self::$preferredNeighbors[$biomeId1]) && 
            in_array($biomeId2, self::$preferredNeighbors[$biomeId1], true)) {
            return 1.0;
        }
        
        // Neutral kompatibel = 0.5
        return 0.5;
    }

    /**
     * Setzt alle Regeln zurück
     */
    public static function reset(): void {
        self::$incompatibleBiomes = [];
        self::$preferredNeighbors = [];
    }

    /**
     * Gibt alle Kompatibilitätsregeln zurück (für Debugging)
     */
    public static function getAllRules(): array {
        return [
            'incompatible' => self::$incompatibleBiomes,
            'preferred' => self::$preferredNeighbors
        ];
    }
}
