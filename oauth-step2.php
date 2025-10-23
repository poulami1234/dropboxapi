<?php
// This page is called by Dropbox OAuth process after the user has authorized the app

// If the user authorizes the app, Dropbox returns a "code" in a url parameter
$code = $_GET['code'];

// If the user does not authorizes the app, Dropbox return an error,
// which we will display to the user, and then we terminate the script
if (empty($code)) {
    $error = $_GET['error'];
    $error_description = $_GET['error_description'];

    if (!empty($error)) {
        echo $error . ': ' . $error_description;
    }
    exit;
}



// If we get the "code" we submit it to a Dropbox url, which in return will give us
// an "access token" and a "refresh token"

$client_id = '470pnwinqhhtass';
$secret = '4hpvfwro8xapf1n';

$redirect_url = 'http://localhost/dropbox-api/oauth-step2.php';
$url = 'https://api.dropbox.com/oauth2/token';

$data = array(
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $redirect_url
);

$query_string = http_build_query($data);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $secret);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

// The response is in json format
$json = json_decode($response);


// If the response could not be decoded as json
if (is_null($json)) {
    echo $response;
    exit;
}

// If the response contains an error message ... with an "error_summary" field
if (!empty($json->error->{".tag"})) {
    echo $json->error->{".tag"} . (!empty($json->error_summary) ? ': ' . $json->error_summary : '');
    exit;
}

// If the response contains an error message ... with an "error_message" field
if (!empty($json->error)) {
    echo $json->error . (!empty($json->error_description) ? ': ' . $json->error_description : '');
    exit;
}

// If the response doesn't contain the "access token"
if (empty($json->access_token)) {
    echo 'Unknown error: ' . $response;
    exit;
}



// Then we save the response data (access token, refresh token) in a database

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'meero';

$mysqli = new mysqli($host, $user, $pass, $db);

$sql = 'insert into dropbox_settings (access_token, expires_in, token_type, scope, refresh_token, account_id, uid)
values (
    "' . $mysqli->real_escape_string($json->access_token) . '",
    "' . intval($json->expires_in) . '",
    "' . $mysqli->real_escape_string($json->token_type) . '",
    "' . $mysqli->real_escape_string($json->scope) . '",
    "' . $mysqli->real_escape_string($json->refresh_token) . '",
    "' . $mysqli->real_escape_string($json->account_id) . '",
    "' . intval($json->uid) . '"
)';

$mysqli->query($sql) or die($mysqli->error);

$mysqli->close();

header('Location: oauth-step3.php');