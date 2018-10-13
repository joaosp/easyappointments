<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
you just got redirected

<?php

/* Instagram App Client Id */
define('INSTAGRAM_CLIENT_ID', '94f7d5bb2a864fe399bf449b3bd9a30e');

/* Instagram App Client Secret */
define('INSTAGRAM_CLIENT_SECRET', 'c7d508eb8db84eca9771042cb91268e3');

/* Instagram App Redirect Url */
define('INSTAGRAM_REDIRECT_URI', 'http://127.0.0.1/bybso/social');

function GetUserProfileInfo($access_token)
{
    $url = 'https://api.instagram.com/v1/users/self/?access_token=' . $access_token;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $data = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($data['meta']['code'] != 200 || $http_code != 200)
        throw new Exception('Error : Failed to get user information');

    return $data['data'];
}


function GetAccessToken($client_id, $redirect_uri, $client_secret, $code)
{
    $url = 'https://api.instagram.com/oauth/access_token';

    $curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code=' . $code . '&grant_type=authorization_code';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http_code != '200')
        throw new Exception('Error : Failed to receieve access token');

    return $data['access_token'];
}


if (isset($_GET['code'])) {
    try {

        // Get the access token
        $access_token = GetAccessToken(INSTAGRAM_CLIENT_ID, INSTAGRAM_REDIRECT_URI, INSTAGRAM_CLIENT_SECRET, $_GET['code']);

        // Get user information
        $user_info = GetUserProfileInfo($access_token);

        echo '<pre>';
        print_r($user_info);
        echo '</pre>';

        // Now that the user is logged in you may want to start some session variables

        /*$this->session->ser_userdata('social_login_instagram', 1);
        $this->session->ser_userdata('social_login_instagram_info', $user_info);*/
        $_SESSION['xpto'] = 'trust me';
        var_dump($_SESSION);

        // You may now want to redirect the user to the home page of your website
        //header('Location: /bybso/');
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }
}
?>


</body>
</html>