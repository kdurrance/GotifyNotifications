<?php

namespace kdurrance\GotifyNotifications;

class Gotify {
	private $server;
	private $port;
	private $apptoken;
	private $plugin;
	private $pool;

	public function __construct($server, $port, $apptoken, $plugin){
		# get config settings
		$this->server = $server;
		$this->port = $port;
		$this->apptoken = $apptoken;
		$this->plugin = $plugin;

		# create a thread  pool to manage the worker threads
		$this->pool = new \Pool(4);
	}

	public function __destruct(){
                # shutdown the thread pool
                $this->pool->shutdown();
        }

	public function pushmsg($title, $message){
               $data = [
                    "title"=> $title,
                    "message"=> $message,
                    "priority"=> 5,
                ];

                $data_string = json_encode($data);

		# submit a new task for the thread pool
		$this->pool->submit(new CurlWorker($this->server, $this->port, $this->apptoken, $data_string));
	}
}
