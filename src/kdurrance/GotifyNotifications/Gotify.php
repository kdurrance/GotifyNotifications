<?php

namespace kdurrance\GotifyNotifications;

class Gotify {
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

                # kick off the worker thread to execute curl and send the payload to Gotify
                $worker = new CurlWorker($this->server, $this->port, $this->apptoken, $data_string);
                $worker ->start();
	}
}
