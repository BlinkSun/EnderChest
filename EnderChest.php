<?php

/*
__PocketMine Plugin__
name=EnderChest
description=I hope you know what's an EnderChest :P
version=1.0
author=BlinkSun
class=EnderChest
apiversion=11,12
*/

class EnderChest implements Plugin
{
	private $api;

	public function __construct(ServerAPI $api, $server = false)
	{
		$this->api = $api;	
	}

	public function init()
	{
		AchievementAPI::addAchievement("enderChest","Keep my stuffs anywhere !");
		$this->api->addHandler("player.block.touch", array($this, "eventHandle"), 50);
		$this->api->addHandler("player.block.break", array($this, "eventHandle"), 50);
		$this->api->addHandler("player.block.place", array($this, "eventHandle"), 50);
		$this->api->addHandler("player.container.slot", array($this, "eventHandle"), 50);
		$this->enderchests = new Config($this->api->plugin->configPath($this)."enderchests.yml", CONFIG_YAML);
	}
	
	public function eventHandle($data, $event) {
		
		switch ($event) {
		
			case "player.block.touch":
			
				$player = $data["player"];
				$target = $data["target"];
				$tile = $this->api->tile->get(new Position($target->x, $target->y, $target->z, $target->level));
				
				if(($target->getID() == 54) and ($target->level->getBlock(new Vector3($target->x, $target->y - 1, $target->z))->getID() == 87)) {
					if($this->enderchests->exists($target->x . "," . $target->y . "," . $target->z)) {
						if($this->enderchests->get($target->x . "," . $target->y . "," . $target->z) == $player->username) {
							
							$slots = $this->enderchests->get($player->username);
							
							foreach ($slots as $key => $slot) {
								$item = $this->api->block->getItem($slot[0], $slot[2], $slot[1]);
								$tile->setSlot($key,$item);
							}
							break;
							
						} else {
							$player->sendChat("[EnderChest] Sry, it's not your EnderChest!");
							return false;
						}
						break;
					
					} else {

						$player->sendChat("[EnderChest] This EnderChest is ownerless!");
						
						if($this->enderchests->exists($player->username)) {
							$slots = $this->enderchests->get($player->username);
							$player->sendChat("[EnderChest] Now it's your EnderChest!");
						} else {
							$slots = array();
							for($i = 0; $i < 27; $i++){
								$slots[] = array(AIR,0,0);
							}
							$player->sendChat("[EnderChest] You got your first EnderChest!");
							AchievementAPI::grantAchievement($player, "enderChest");
						}
						
						foreach ($slots as $key => $slot) {
							$item = $this->api->block->getItem($slot[0], $slot[2], $slot[1]);
							$tile->setSlot($key,$item);
						}
						
						$this->enderchests->set($target->x . "," . $target->y . "," . $target->z,$player->username);
						$this->enderchests->set($player->username,$slots);
						$this->enderchests->save();
						break;
					}
					break;
				}	
				break;
				
			case "player.block.break":
			
				$player = $data["player"];
				$target = $data["target"];
				$tile = $this->api->tile->get(new Position($target->x, $target->y, $target->z, $target->level));
				
				if(($target->getID() == 54) and ($target->level->getBlock(new Vector3($target->x, $target->y - 1, $target->z))->getID() == 87)) {
					if($this->enderchests->exists($target->x . "," . $target->y . "," . $target->z)) {
						if($this->enderchests->get($target->x . "," . $target->y . "," . $target->z) == $player->username) {
							
							$emptyslots = array();
							
							for($i = 0; $i < 27; $i++){
								$emptyslots[] = array(AIR,0,0);
							}
							foreach ($emptyslots as $key => $slot) {
								$item = $this->api->block->getItem($slot[0], $slot[2], $slot[1]);
								$tile->setSlot($key,$item);
							}
							
							$this->enderchests->remove($target->x . "," . $target->y . "," . $target->z);
							$this->enderchests->save();
							$player->sendChat("[EnderChest] You have broken an EnderChest!");
							break;
							
						} else {
							$player->sendChat("[EnderChest] Sry, it's not your EnderChest!");
							return false;
						}
						break;
					}
					break;
				}
				break;
				
			case "player.block.place":
			
				$player = $data["player"];
				$block = $data["block"];
				$item = $data["item"];
				
				if($item->getID() == 54) {
					if(($this->getSideChest($block) == false) and ($block->level->getBlock(new Vector3($block->x, $block->y - 1, $block->z))->getID() == 87)){

						$this->enderchests->set($block->x . "," . $block->y . "," . $block->z,$player->username);
						$this->enderchests->save();
									
						if(!$this->enderchests->exists($player->username)) {
						
							$emptyslots = array();	
							for($i = 0; $i < 27; $i++){
								$emptyslots[] = array(AIR,0,0);
							}
							
							$this->enderchests->set($player->username,$emptyslots);
							$this->enderchests->save();
							
							$player->sendChat("[EnderChest] You got your first EnderChest!");
							AchievementAPI::grantAchievement($player, "enderChest");
							break;
						}
						break;
						
					} elseif($this->getSideChest($block) !== false) {
					
						if(($this->getSideChest($block)->level->getBlock(new Vector3($this->getSideChest($block)->x, $this->getSideChest($block)->y - 1, $this->getSideChest($block)->z))->getID() == 87) or ($block->level->getBlock(new Vector3($block->x, $block->y - 1, $block->z))->getID() == 87)) {
							$player->sendChat("[EnderChest] You can't make big Enderchest!");
							return false;
						}
						break;
					}
					break;
				}
				break;
			
			case "player.container.slot":
				
				$tile = $data["tile"];
				$pslot = $data["slot"];
				$slot = $data["slotdata"];
				$item = $data["itemdata"];
				$player = $data["player"];

				if($tile->class == TILE_CHEST and $this->enderchests->exists($tile->x . "," . $tile->y . "," . $tile->z)){
					if($this->enderchests->get($tile->x . "," . $tile->y . "," . $tile->z) == $player->username) {
						if($item->getID() !== AIR and $slot->getID() == $item->getID()){
							if($slot->count < $item->count){
								if($player->removeItem($item->getID(), $item->getMetadata(), $item->count - $slot->count, false) === false){
									return false;
								}
							}elseif($slot->count > $item->count){
								$player->addItem($item->getID(), $item->getMetadata(), $slot->count - $item->count, false);
							}
						}else{
							if($player->removeItem($item->getID(), $item->getMetadata(), $item->count, false) === false){
								return false;
							}
							$player->addItem($slot->getID(), $slot->getMetadata(), $slot->count, false);
						}
						$tile->setSlot($pslot, $item);
						
						$slots = array();	
						for($i = 0; $i < 27; $i++){
							$slots[] = array($tile->getSlot($i)->getID(),$tile->getSlot($i)->count,$tile->getSlot($i)->getMetadata());
						}

						$this->enderchests->set($player->username,$slots);
						$this->enderchests->save();
						break;
					}
					break;
				}
				break;
		}
	}
	
	private function getSideChest($block)
	{
		$chest = $block->level->getBlock(new Vector3($block->x + 1, $block->y, $block->z));
		if ($chest->getID() === CHEST) return $chest;
		$chest = $block->level->getBlock(new Vector3($block->x - 1, $block->y, $block->z));
		if ($chest->getID() === CHEST) return $chest;
		$chest = $block->level->getBlock(new Vector3($block->x, $block->y, $block->z + 1));
		if ($chest->getID() === CHEST) return $chest;
		$chest = $block->level->getBlock(new Vector3($block->x, $block->y, $block->z - 1));
		if ($chest->getID() === CHEST) return $chest;
		return false;
	}

	public function __destruct()
	{
	}
}
