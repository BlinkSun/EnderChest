<?php

/*
__PocketMine Plugin__
name=EnderChest
description=I hope you know what's an EnderChest :P
version=0.3
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
		$this->api->addHandler("player.block.touch", array($this, "eventHandle"), 50);
		$this->api->addHandler("player.block.break", array($this, "eventHandle"), 50);
		$this->api->addHandler("player.block.place", array($this, "eventHandle"), 50);
		$this->api->addHandler("player.container.slot", array($this, "eventHandle"), 50);
		$this->enderchests = new Config($this->api->plugin->configPath($this)."enderchests.yml", CONFIG_YAML);
	}

	
	public function eventHandle($data, $event) {
		switch ($event) {
			case "player.block.touch":
				/*if (($tile = $this->api->tile->get(new Position($data['target']->x, $data['target']->y, $data['target']->z, $data['target']->level))) === false) break;
				switch ($tile->class) {
				case TILE_CHEST:
				switch ($data['type']) {
				case "place":*/
				if($data["target"]->getID() == 54) {
					if($data["target"]->level->getBlock(new Vector3($data["target"]->x, $data["target"]->y - 1, $data["target"]->z))->getID() == 87) {
						if($this->enderchests->exists($data["target"]->x . "," . $data["target"]->y . "," . $data["target"]->z)) {
							$slots = $this->enderchests->get($data["player"]->username);
							$tile = $this->api->tile->get(new Position($data['target']->x, $data['target']->y, $data['target']->z, $data['target']->level));
							foreach ($slots as $key => $slot) {
								$item = $this->api->block->getItem($slot[0], $slot[2], $slot[1]);
								$tile->setSlot($key,$item);
							}
						} else { 
							$this->enderchests->set($data["target"]->x . "," . $data["target"]->y . "," . $data["target"]->z,$data["player"]->username);
							$this->enderchests->save();
						}
					}
				}
				break;
			case "player.container.slot":
				if($data["tile"]->class == TILE_CHEST) {
					if($this->enderchests->exists($data["tile"]->x . "," . $data["tile"]->y . "," . $data["tile"]->z)) {

						$getenderslots = array();	
						for($i = 0; $i < 27; $i++){
							$getenderslots[] = array($data["tile"]->getSlot($i)->getID(),$data["tile"]->getSlot($i)->count,$data["tile"]->getSlot($i)->getMetadata());
						}
						$this->enderchests->set($data["player"]->username,$getenderslots);
						$this->enderchests->save();

					}
				}
				break;
			case "player.block.break":
				if($data["target"]->getID() == 54) {
					if($data["target"]->level->getBlock(new Vector3($data["target"]->x, $data["target"]->y - 1, $data["target"]->z))->getID() == 87) {
						if($this->enderchests->exists($data["target"]->x . "," . $data["target"]->y . "," . $data["target"]->z)) {
							$emptyslots = array();	
							for($i = 0; $i < 27; $i++){
								$emptyslots[] = array(AIR,0,0);
							}
							$tile = $this->api->tile->get(new Position($data['target']->x, $data['target']->y, $data['target']->z, $data['target']->level));
							foreach ($emptyslots as $key => $slot) {
								$item = $this->api->block->getItem($slot[0], $slot[2], $slot[1]);
								$tile->setSlot($key,$item);
							}
						}
					}
				}
				break;
			case "player.block.place":
				if($data["item"]->getID() == 54) {
					if($data["block"]->level->getBlock(new Vector3($data["block"]->x, $data["block"]->y - 1, $data["block"]->z))->getID() == 87) {
						$this->enderchests->set($data["block"]->x . "," . $data["block"]->y . "," . $data["block"]->z,$data["player"]->username);
						$this->enderchests->save();
						if($this->enderchests->exists($data["player"]->username)) {
						}else{
							$emptyslots = array();	
							for($i = 0; $i < 27; $i++){
								$emptyslots[] = array(AIR,0,0);
							}
							$this->enderchests->set($data["player"]->username,$emptyslots);
							$this->enderchests->save();
							$data["player"]->sendChat("[EnderChest] You have created your EnderChest successfully !");
						}
					}
				}
				break;
		}
	}
	
	/*Function that I ll work with for paired chests issue
	
	private function getSideChest($data)
	{
		$item = $data->level->getBlock(new Vector3($data->x + 1, $data->y, $data->z));
		if ($item->getID() === CHEST) return $item;
		$item = $data->level->getBlock(new Vector3($data->x - 1, $data->y, $data->z));
		if ($item->getID() === CHEST) return $item;
		$item = $data->level->getBlock(new Vector3($data->x, $data->y, $data->z + 1));
		if ($item->getID() === CHEST) return $item;
		$item = $data->level->getBlock(new Vector3($data->x, $data->y, $data->z - 1));
		if ($item->getID() === CHEST) return $item;
		return false;
	}*/

	public function __destruct()
	{
	}
}
