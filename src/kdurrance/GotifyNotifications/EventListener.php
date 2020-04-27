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

namespace kdurrance\GotifyNotifications;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\LowMemoryEvent;
use pocketmine\event\server\UpdateNotifyEvent;
use pocketmine\event\server\QueryRegenerateEvent;

class EventListener implements Listener{
	/** @var MainClass */
	private $plugin;
        private $sentQuery = false;

	/** record login times per loginid */
	private $logintimes = array();

	public function __construct(MainClass $plugin){
		$this->plugin = $plugin;
	}

        public function onQueryRegenerate(QueryRegenerateEvent $event) : void{
                if ($this->sentQuery == false) {
                    # get query information in a readable format
                    $servername = "Server name: ". $event->getServerName()."\r\n";
                    $maxplayers = "Max players: ". $event->getMaxPlayerCount()."\r\n";                    
                    $map = "Default world: ". $event->getWorld()."\r\n";

                    # get a list of enabled plugins
                    $pluginlist = "Enabled plugins:\r\n";
                  
                    foreach($event->getPlugins() as $plugin){
                         if($plugin->isEnabled()){
                                 $pluginlist .= " - ".$plugin->getDescription()->getFullName()."\r\n";
                         }
                    }

                    $this->plugin->notify->pushmsg("Server Query Information", $servername.$maxplayers.$map.$pluginlist);
                    $this->sentQuery = true;
                }
        }

	public function onGameModeChange(PlayerGameModeChangeEvent $event) : void{
                $this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " changed game mode", "Gamemode: " . $event->getPlayer()->getServer()->getGamemodeName($event->getNewGamemode()));
        }

	public function onUpdateAvailable(UpdateNotifyEvent $event) : void{
                $this->plugin->notify->pushmsg("Pocketmine update availabled", $event->getUpdater()->getUpdateInfo()["details_url"]);
        }

	public function onCommand(CommandEvent $event) : void{
		$this->plugin->notify->pushmsg($event->getSender()->getName() . " executed a command", $event->getCommand());
	}

	public function onLowMem(LowMemoryEvent $event) : void{
		$this->plugin->notify->pushmsg("Low Memory Warning", round($event->getMemory() / $event->getMemoryLimit() * 100, 2) . "% used\r\n" . $event->getMemory() . " used bytes\r\n" . $event->getMemoryLimit() . " total bytes");
        }

	public function onQuit(PlayerQuitEvent $event) : void{ 
		$timeonline = $this->logintimes[$event->getPlayer()->getDisplayName()]->diff(new \DateTime("now"));
		$this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " logged out", "Time online: " . $timeonline->format('%i minutes and %s seconds'));
		unset($this->logintimes[$event->getPlayer()->getDisplayName()]);
	}

	public function onJoin(PlayerJoinEvent $event) : void{ 
		$this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " joined the game", "Gamemode: " . $event->getPlayer()->getServer()->getGamemodeName($event->getPlayer()->getGamemode()));
	}

	public function onKick(PlayerKickEvent $event) : void{
		$this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " was kicked", "IP:" . $event->getPlayer()->getAddress());
	}
	
        public function onLogin(PlayerLoginEvent $event) : void{
		$this->logintimes[$event->getPlayer()->getDisplayName()] = new \DateTime("now");
		$operatorcheck = ($event->getPlayer()->isOp() == 1 ? "True" : "False");
                $this->plugin->notify->pushmsg($event->getPlayer()->getDisplayName() . " logged in", "IP: " . $event->getPlayer()->getAddress() . "\r\nPing: " . $event->getPlayer()->getPing() . "ms\r\nIs Op: " . $operatorcheck);
        }
}
