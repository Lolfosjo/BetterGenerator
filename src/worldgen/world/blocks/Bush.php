<?php

namespace worldgen\world\blocks;

use worldgen\world\blocks\traits\SolidBaseTrait;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Flowable;

class Bush extends Flowable {

    use SolidBaseTrait;

    public function __construct(BlockIdentifier $id, string $name){
        parent::__construct($id, $name, new BlockTypeInfo(BlockBreakInfo::instant()));
    }

    public function isSolid() : bool{
        return false;
    }
}
