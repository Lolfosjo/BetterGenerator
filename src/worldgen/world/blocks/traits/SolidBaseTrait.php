<?php

namespace worldgen\world\blocks\traits;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

trait SolidBaseTrait {

    protected array $allowedBaseBlocks = [
        BlockTypeIds::GRASS,
        BlockTypeIds::DIRT,
        BlockTypeIds::PODZOL
    ];

    public function place(
        BlockTransaction $tx,
        Item $item,
        Block $blockReplace,
        Block $blockClicked,
        int $face,
        Vector3 $clickVector,
        ?Player $player = null
    ): bool {
        $blockBelow = $blockReplace->getPosition()->getWorld()->getBlock($blockReplace->getPosition()->down());

        if (!in_array($blockBelow->getTypeId(), $this->allowedBaseBlocks, true)) {
            return false;
        }

        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}
