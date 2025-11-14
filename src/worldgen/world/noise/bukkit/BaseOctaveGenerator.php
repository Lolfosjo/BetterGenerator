<?php

/*
 *    $$$$$$$\                      $$\   $$\               $$\      $$\ $$\                     
 *    $$  __$$\                     $$ |  $$ |              $$$\    $$$ |\__|                         
 *    $$ |  $$ | $$$$$$\   $$$$$$\  $$ |$$$$$$\   $$\   $$\ $$$$\  $$$$ |$$\ $$$$$$$\   $$$$$$\        
 *    $$$$$$$\ |$$  __$$\ $$  __$$\ $$ |\_$$  _|  $$ |  $$ |$$\$$\$$ $$ |$$ |$$  __$$\ $$  __$$\ 
 *    $$  __$$\ $$$$$$$$ |$$$$$$$$ |$$ |  $$ |    $$ |  $$ |$$ \$$$  $$ |$$ |$$ |  $$ |$$$$$$$$ |
 *    $$ |  $$ |$$   ____|$$   ____|$$ |  $$ |$$\ $$ |  $$ |$$ |\$  /$$ |$$ |$$ |  $$ |$$   ____|        
 *    $$$$$$$  |\$$$$$$$\ \$$$$$$$\ $$ |  \$$$$  |\$$$$$$$ |$$ | \_/ $$ |$$ |$$ |  $$ |\$$$$$$$\       
 *    \_______/  \_______| \_______|\__|   \____/  \____$$ |\__|     \__|\__|\__|  \__| \_______|     
 *                                                $$\   $$ |                                                                    
 *                                                \$$$$$$  |                                                                    
 *                                                 \______/           
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * @author AyrzDev
 * @project BeeltyMine VanillaGenerator
 * @link https://dephts.com
 * 
 *                                   
 */

declare(strict_types=1);

namespace worldgen\world\noise\bukkit;

abstract class BaseOctaveGenerator
{

	public float $x_scale = 1.0;
	public float $y_scale = 1.0;
	public float $z_scale = 1.0;

	/**
	 * @param NoiseGenerator[] $octaves
	 */
	protected function __construct(
		protected array $octaves
	) {}

	/**
	 * Sets the scale used for all coordinates passed to this generator.
	 * <p>
	 * This is the equivalent to setting each coordinate to the specified
	 * value.
	 *
	 * @param float $scale New value to scale each coordinate by
	 */
	public function setScale(float $scale): void
	{
		$this->x_scale = $scale;
		$this->y_scale = $scale;
		$this->z_scale = $scale;
	}

	/**
	 * Gets a clone of the individual octaves used within this generator
	 *
	 * @return NoiseGenerator[] clone of the individual octaves
	 */
	public function getOctaves(): array
	{
		$octaves = [];
		foreach ($this->octaves as $key => $value) {
			$octaves[$key] = clone $value;
		}

		return $octaves;
	}
}
