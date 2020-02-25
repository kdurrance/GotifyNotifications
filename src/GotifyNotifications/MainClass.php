<?php

declare(strict_types=1);

namespace GotifyNotifications;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

class MainClass extends PluginBase{

	public $notify;
	/** @var Config */ 	
	private $config; 	
	private $server;
	private $apptoken;
	private $port; 
	/** @var Config */

	public function onLoad() : void{

		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}
	
		# load config from config.yml
		$this->saveDefaultConfig();	
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->server = $this->config->get("server");
		$this->apptoken = $this->config->get("apptoken");
		$this->port = $this->config->get("port");

		$this->notify = new Gotify($this->server, $this->port, $this->apptoken, $this);
	}

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getLogger()->info(TextFormat::DARK_GREEN . "Gotify enabled [server:" . $this->server . "] [port:" . $this->port . "] [apptoken:" . $this->apptoken . "]");
		$this->notify->pushmsg("Notifications enabled", $this->getServer()->getName()." (Minecraft ".$this->getServer()->getVersion().", Pocketmine ".$this->getServer()->getPocketMineVersion().")");
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . "Gotify disabled!");
		$this->notify->pushmsg("Notifications disabled", $this->getServer()->getName()." (Minecraft ".$this->getServer()->getVersion().", Pocketmine ".$this->getServer()->getPocketMineVersion().")");
	}

}
