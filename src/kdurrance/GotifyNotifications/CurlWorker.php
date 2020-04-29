<?php

namespace kdurrance\GotifyNotifications;

use pocketmine\utils\TextFormat;

class CurlWorker extends \Thread {
       private $server;
       private $port;
       private $apptoken;
       private $jsondata;

       public function __construct($server, $port, $apptoken, $jsondata) {
                $this->server = $server;
                $this->port = $port;
                $this->apptoken = $apptoken;
                $this->jsondata = $jsondata;
       }

       public function run() {
                $url = "http://" . $this->server . ":" . $this->port . "/message?token=" . $this->apptoken;

                $headers = [
                    "Content-Type: application/json; charset=utf-8"
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsondata);

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
       }

}

