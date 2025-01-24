<?php
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('/var/www/html/credentials.json');
$client->setScopes(Google\Service\Calendar::CALENDAR_EVENTS);
$client->setRedirectUri('http://localhost:8080');

$authUrl = $client->createAuthUrl();
echo "認証URL: <a href='$authUrl' target='_blank'>$authUrl</a>";
