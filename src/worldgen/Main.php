<?php

declare(strict_types=1);

namespace worldgen;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;
use worldgen\world\BetterGenerator;
use worldgen\world\BetterGeneratorCaves;
use worldgen\world\blocks\BlockRegistry;
use pocketmine\scheduler\AsyncTask;

class Main extends PluginBase {

    protected function onEnable(): void {
        GeneratorManager::getInstance()->addGenerator(BetterGenerator::class, "better_generator", fn() => null);
        GeneratorManager::getInstance()->addGenerator(BetterGeneratorCaves::class, "better_generator_caves", fn() => null);

        BlockRegistry::registerBlocks();

        $this->getServer()->getAsyncPool()->addWorkerStartHook(function(int $worker) : void {
            $this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask {
                public function onRun() : void {
                    BlockRegistry::registerBlocks();
                 // BlockRegistry::registerItems();
                }
            }, $worker);
        });
        
    }

}
