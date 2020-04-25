<?php

require_once('vendor/autoload.php');

// // 環境変数使用ライブラリ
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

$CLIENT_ID = getenv('CLIENT_ID');
$CLIENT_SECRET = getenv('CLIENT_SECRET');

$session = new SpotifyWebAPI\Session(
    "{$CLIENT_ID}",
    "{$CLIENT_SECRET}"
);
$api = new SpotifyWebAPI\SpotifyWebAPI();
$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();
$api->setAccessToken($accessToken);
