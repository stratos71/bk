<?php

namespace App;

use Google_Clien;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;

class Gmail
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Mi aplicación');
        $this->client->setScopes(Google_Service_Gmail::MAIL_GOOGLE_COM);
        $this->client->setAuthConfig(config_path('gmail.php'));
        $this->service = new Google_Service_Gmail($this->client);
    }

    public function send($to, $subject, $body)
    {
        $message = new Google_Service_Gmail_Message();
        $message->setRaw(
            base64_encode(
                "To: {$to}\r\n" .
                "Subject: {$subject}\r\n" .
                "Content-Type: text/plain; charset=UTF-8\r\n" .
                "\r\n" .
                "{$body}\r\n"
            )
        );
        $this->service->users_messages->send('me', $message);
    }
}