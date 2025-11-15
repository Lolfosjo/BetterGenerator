<?php

declare(strict_types=1);

namespace worldgen\world\blocks;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Opaque;
use pocketmine\utils\CloningRegistryTrait;
use worldgen\world\blocks\Bush;
use worldgen\world\blocks\FireflyBush;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static \pmmp\RegisterBlockDemoPM5\blocks\FireflyBush FIREFLY_BUSH()
 * @method static \pmmp\RegisterBlockDemoPM5\blocks\Bush BUSH()
 * @method static \pocketmine\block\Opaque MOSS_CARPET()
 * @method static \pocketmine\block\Opaque MOSS_BLOCK()
 */
final class ExtraVanillaBlocks{
    use CloningRegistryTrait;

    private function __construct(){
    }

    protected static function register(string $name, Block $block) : void{
        self::_registryRegister($name, $block);
    }

    /**
     * @return Block[]
     * @phpstan-return array<string, Block>
     */
    public static function getAll() : array {
        /** @var Block[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup() : void{

        $fireflyBushTypeId = BlockTypeIds::newId();
        self::register("firefly_bush", new FireflyBush(new BlockIdentifier($fireflyBushTypeId), "Firefly Bush"));

        $bushTypeId = BlockTypeIds::newId();
        self::register("bush", new Bush(new BlockIdentifier($bushTypeId), "Bush"));

        $mossCarpetTypeId = BlockTypeIds::newId();
        self::register("moss_carpet", new Opaque(new BlockIdentifier($mossCarpetTypeId), "Moss Carpet", new BlockTypeInfo(BlockBreakInfo::instant())));

        $mossBlockTypeId = BlockTypeIds::newId();
        self::register("moss_block", new Opaque(new BlockIdentifier($mossBlockTypeId), "Moss Block", new BlockTypeInfo(BlockBreakInfo::instant())));

    }
}
