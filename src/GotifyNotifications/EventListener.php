<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace GotifyNotifications;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;

class EventListener implements Listener{

	/** @var MainClass */
	private $plugin;

	/** record login times per loginid */
	private $logintimes = array();

	public function __construct(MainClass $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerRespawnEvent $event
	 *
	 * @priority        NORMAL
	 * @ignoreCancelled false
	 */

	public function onQuit(PlayerQuitEvent $event) : void{ 
		$timeonline = $this->logintimes[$event->getPlayer()->getDisplayName()]->diff(new \DateTime("now"));
		$this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " has logged out", "Time online: " . $timeonline->format('%i minutes %s seconds'));
		unset($this->logintimes[$event->getPlayer()->getDisplayName()]);
	}

	public function onJoin(PlayerJoinEvent $event) : void{ 
		switch ($event->getPlayer()->getGamemode()) {
    			case 0:
        			$gamemode = "Survival";
        			break;
    			case 1:
        			$gamemode = "Creative";
        			break;
    			case 2:
        			$gamemode = "Adventure";
        			break;
			case 3:
				$gamemode = "Spectator";
				break;
		}

		$this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " joined the game", "Gamemode: " . $gamemode);
	}

	public function onKick(PlayerKickEvent $event) : void{
		$this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " has was kicked", "IP:" . $event->getPlayer()->getAddress());
	}
	
        public function onLogin(PlayerLoginEvent $event) : void{
		$this->logintimes[$event->getPlayer()->getDisplayName()] = new \DateTime("now");
		$operatorcheck = ($event->getPlayer()->isOp() == 1 ? "True" : "False");
                $this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " has logged in", "[IP:" . $event->getPlayer()->getAddress() . "] [Ping:" . $event->getPlayer()->getPing() . "ms] [Is Op: " . $operatorcheck . "]");
        }

}
