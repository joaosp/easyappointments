<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">

    <title><?= lang('page_title') . ' ' . $company_name ?></title>

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/cookieconsent/cookieconsent.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/frontend.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/social-buttons/css/buttons-si.css') ?>">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">
</head>


<!--
    Instagram login
-->
<?php

/* Instagram App Client Id */
define('INSTAGRAM_CLIENT_ID', '94f7d5bb2a864fe399bf449b3bd9a30e');

/* Instagram App Client Secret */
define('INSTAGRAM_CLIENT_SECRET', 'c7d508eb8db84eca9771042cb91268e3');

/* Instagram App Redirect Url */
define('INSTAGRAM_REDIRECT_URI', 'http://127.0.0.1/bybso/');


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

        // Now that the user is logged in you may want to start some session variables

        $this->session->set_userdata('social_login_instagram', 1);
        $this->session->set_userdata('social_login_instagram_info', $user_info);

        // You may now want to redirect the user to the home page of your website
        header("Location: " . site_url('appointments/book/'));
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }
}


$instagram_login_url = 'https://api.instagram.com/oauth/authorize/?client_id=' . INSTAGRAM_CLIENT_ID . '&redirect_uri=' . urlencode(INSTAGRAM_REDIRECT_URI) . '&response_type=code&scope=basic';

?>

<body>
<div id="main" class="container">
    <div class="wrapper row">
        <div id="social-media-login" class="col-xs-12 col-md-4 col-md-offset-4">
            <div id="header">
                <span
                    id="company-name"><?= lang('welcome_social_login') ?></span>
            </div>

            <div class="button-frame">
                <div class="frame-container">
                    <div class="frame-content">
                        <div class="form-group">
                            <button class="btn-si btn-google form-control">Sign in with Google</button>
                            <button class="btn-si btn-facebook form-control">Sign in with Facebook</button>
                            <button onclick='location.href="<?= $instagram_login_url ?>"'
                                    class="btn-si btn-linkedin form-control">
                                Sign in
                                with
                                LinkedIn
                            </button>
                            <button onclick='location.href="<?= $instagram_login_url ?>"'
                                    class="btn-si btn-instagram form-control">
                                Sign in
                                with
                                Instagram
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>

<?php if ($display_cookie_notice === '1'): ?>
    <?php require 'cookie_notice_modal.php' ?>
<?php endif ?>

<?php if ($display_terms_and_conditions === '1'): ?>
    <?php require 'terms_and_conditions_modal.php' ?>
<?php endif ?>

<?php if ($display_privacy_policy === '1'): ?>
    <?php require 'privacy_policy_modal.php' ?>
<?php endif ?>

<script>
    var GlobalVariables = {
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode($date_format) ?>,
        timeFormat: <?= json_encode($time_format) ?>,
        displayCookieNotice: <?= json_encode($display_cookie_notice === '1') ?>,
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>
    };

    var EALang = <?= json_encode($this->lang->language) ?>;
    var availableLanguages = <?= json_encode($this->config->item('available_languages')) ?>;
</script>

<script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/cookieconsent/cookieconsent.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/datejs/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/frontend_book_api.js') ?>"></script>
<script src="<?= asset_url('assets/js/frontend_book.js') ?>"></script>

<script>
    $(document).ready(function () {
        FrontendBook.initialize(true, GlobalVariables.manageMode);
        GeneralFunctions.enableLanguageSelection($('#select-language'));
    });
</script>

<?php google_analytics_script(); ?>
</body>