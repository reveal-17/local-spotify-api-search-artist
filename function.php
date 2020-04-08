<?php
function spotify() {
    require_once('vendor/autoload.php');
    $session = new SpotifyWebAPI\Session(
        '5076a3bdf7bc4e8f8eb0e9b15134295f',
        '1e08120c75754a8f8de076cf79de318b'
    );
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $session->requestCredentialsToken();
    $accessToken = $session->getAccessToken();
    $api->setAccessToken($accessToken);
}
