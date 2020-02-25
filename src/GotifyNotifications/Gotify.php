<?php

namespace GotifyNotifications;

use pocketmine\utils\TextFormat;

class Gotify{
	private $server;
	private $port;
	private $apptoken;
	private $plugin;

	public function __construct($server, $port, $apptoken, $plugin){
		# get config settings
		$this->server = $server;
		$this->port = $port;
		$this->apptoken = $apptoken;
		$this->plugin = $plugin;
	}

	public function pushmsg($title, $message){
               $data = [
                    "title"=> $title,
                    "message"=> $message,
                    "priority"=> 5,
                ];

                $data_string = json_encode($data);

                $url = "http://" . $this->server . ":" . $this->port . "/message?token=" . $this->apptoken; 
		
                $headers = [
                    "Content-Type: application/json; charset=utf-8"
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

                $result = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close ($ch);

                switch ($code) {
                    case "200":
                        # Success! Log nothing.
                        break;
                    case "400":
                        $this->plugin->getLogger()->info(TextFormat::DARK_RED . "400:Bad Request");
                        break;
                    case "401":
			$this->plugin->getLogger()->info(TextFormat::DARK_RED . "401:Unauthorized Error - Invalid Token");
                        break;
                    case "403":
			$this->plugin->getLogger()->info(TextFormat::DARK_RED . "403:Forbidden");
                        break;
                    case "404":
			$this->plugin->getLogger()->info(TextFormat::DARK_RED . "404:API URL Not Found");
                        break;
                    default:
			$this->plugin->getLogger()->info(TextFormat::DARK_RED . "HTTP Connection Failed");
                }
	}
}
