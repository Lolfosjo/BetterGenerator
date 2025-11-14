<?php

declare(strict_types=1);

namespace worldgen\world\blocks;

use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\world\format\io\GlobalItemDataHandlers;

class BlockRegistry {

// public function onEnable() : void{

//	self::registerBlocks();
//	self::registerItems();
//	$this->getServer()->getAsyncPool()->addWorkerStartHook(function(int $worker) : void{
//		$this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask{
//			public function onRun() : void{
//				Main::registerBlocks();
//				Main::registerItems();
//			}
//		}, $worker);
//	});
//}

	public static function registerBlocks() : void{
		$fireflyBush = ExtraVanillaBlocks::FIREFLY_BUSH();
		    self::registerSimpleBlock(BlockTypeNames::FIREFLY_BUSH, $fireflyBush, ["firefly_bush"]);
		$bushi = ExtraVanillaBlocks::BUSH();
        self::registerSimpleBlock(BlockTypeNames::BUSH, $bushi, ["bushi"]);
		$mossCarpet = ExtraVanillaBlocks::MOSS_CARPET();
        self::registerSimpleBlock(BlockTypeNames::MOSS_CARPET, $mossCarpet, ["moss_carpet"]);
		$mossBlock = ExtraVanillaBlocks::MOSS_BLOCK();
        self::registerSimpleBlock(BlockTypeNames::MOSS_BLOCK, $mossBlock, ["moss_block"]);
	}

//	public static function registerItems() : void{
//	$item = ExtraVanillaItems::IRON_HORSE_ARMOR();
//	self::registerSimpleItem(ItemTypeNames::IRON_HORSE_ARMOR, $item, ["iron_horse_armor"]);
//	}

	/**
	 * @param string[] $stringToItemParserNames
	 */
	private static function registerSimpleBlock(string $id, Block $block, array $stringToItemParserNames) : void{
		RuntimeBlockStateRegistry::getInstance()->register($block);

		CreativeInventory::getInstance()->add($block->asItem());

		GlobalBlockStateHandlers::getDeserializer()->mapSimple($id, fn() => clone $block);
		GlobalBlockStateHandlers::getSerializer()->mapSimple($block, $id);

		foreach($stringToItemParserNames as $name){
			StringToItemParser::getInstance()->registerBlock($name, fn() => clone $block);
		}
	}

	/**
	 * @param string[] $stringToItemParserNames
	 */
	private static function registerSimpleItem(string $id, Item $item, array $stringToItemParserNames) : void{
		GlobalItemDataHandlers::getDeserializer()->map($id, fn() => clone $item);
		GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($id));

		CreativeInventory::getInstance()->add($item);

		foreach($stringToItemParserNames as $name){
			StringToItemParser::getInstance()->register($name, fn() => clone $item);
		}
	}
}
