<?php
declare(strict_types=1);

namespace worldgen\world\blocks;

use worldgen\world\blocks\traits\SolidBaseTrait;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\Flowable;

class FireflyBush extends Flowable {

    use SolidBaseTrait;

    public function __construct(BlockIdentifier $id, string $name = "Firefly Bush"){
        parent::__construct($id, $name, new BlockTypeInfo(BlockBreakInfo::instant()));
    }

    public function isSolid() : bool{
        return false;
    }
}
