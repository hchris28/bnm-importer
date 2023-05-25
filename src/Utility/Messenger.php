<?php

namespace Bnm\Importer\Utility {
    class Messenger
    {
        function send_message($message)
        {
            $env = parse_ini_file(__DIR__ . '.env');
            
            $url = $env["SLACK_HOOK"];
            $useragent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
            $payload = 'payload={"channel": "#notification", "username": "webhookbot", "text": "' . $message . '", "icon_emoji": ":ghost:"}';
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //set our user agent
            curl_setopt($ch, CURLOPT_POST, TRUE); //set how many paramaters to post
            curl_setopt($ch, CURLOPT_URL, $url); //set the url we want to use
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            curl_exec($ch); //execute and get the results
            curl_close($ch);
        }
    }
}
