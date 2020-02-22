<?php

namespace GotifyNotifications;

class Gotify{

	private $server;
	private $port;
	private $apptoken;

	public function __construct($server, $port, $apptoken){
		# get config settings
		$this->server = $server;
		$this->port = $port;
		$this->apptoken = $apptoken;
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
                        echo "<strong>Your Message was Submitted</strong>";
                        break;
                    case "400":
                        echo "<strong>Bad Request</strong>";
                        break;
                    case "401":
                        echo "<strong>Unauthorized Error - Invalid Token</strong>";
                        break;
                    case "403":
                        echo "<strong>Forbidden</strong>";
                        break;
                    case "404":
                        echo "<strong>API URL Not Found</strong>";
                        break;
                    default:
                        echo "<strong>Hmm Something Went Wrong or HTTP Status Code is Missing</strong>";
                }

	}
}
