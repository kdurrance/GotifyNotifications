<?php

namespace kdurrance\GotifyNotifications;

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

		// create a multi-handle so we can do this asynchronously
		$mh = curl_multi_init();
		curl_multi_add_handle($mh,$ch);

		// execute async call
		do {
    			$status = curl_multi_exec($mh, $active);
    			if ($active) {
        			curl_multi_select($mh);
    			}
		} while ($active && $status == CURLM_OK);

		// get the response code
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_multi_remove_handle($mh, $ch);
                curl_multi_close($mh);

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
