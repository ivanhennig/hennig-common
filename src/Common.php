<?php

namespace Hennig\Common;

class Common
{
    /**
     * Receptcha validation
     *
     * @param $value
     * @throws \Exception
     */
    static public function validateCaptcha($value) {
        if (!$value) {
            throw new \Exception("Não foi possível validar o reCaptcha");
        }
        $recaptcha_verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, stream_context_create([
            "http" => [
                "method" => "POST",
                "content" => http_build_query([
                    "secret" => Config::get("recaptcha_secret"),
                    "response" => $value,
                    "remoteip" => $_SERVER["REMOTE_ADDR"]
                ])
            ]
        ]));
        $recaptcha_verify = @json_decode($recaptcha_verify, true);
        if (!$recaptcha_verify || !$recaptcha_verify["success"]) {
            throw new \Exception("Não foi possível validar o reCaptcha");
        }
    }

    /**
     * Send a notification to Slack
     *
     * @param $message
     * @param $channel
     * @return void
     * @throws \Exception
     */
    static public function slack($message, $channel)
    {
        $channels = Config::get('slack_channels', []);
        if (empty($channels) || empty($channels[$channel])) {
            return;
        }

        // Create a constant to store your Slack URL
        // Make your message
        $message = array('payload' => json_encode([
            'text' => strip_tags($message),
            'mrkdwn' => true
        ]));
        // Use curl to send your message
        $c = curl_init($channels[$channel]);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $message);
        curl_exec($c);
        curl_close($c);
    }
}