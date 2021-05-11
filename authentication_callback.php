<?php
use Vimeo\Vimeo;

require_once('vendor/autoload.php');
require('../../config.php');

$modulename = get_string('pluginname', 'repository_vimeo');

$PAGE->navbar->add($modulename);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('vimeoauthentication', 'repository_vimeo'));

$code = $_GET['code'];

if (empty($code)) {
    echo html_writer::span(get_string('cannotgeneratecode', 'repository_vimeo'));
    die;
}

$PAGE->set_url('/repository/vimeo/authentication_callback.php', array('code' => $code));

$clientid = get_config('vimeo', 'client_id');
$clientsecret = get_config('vimeo', 'client_secret');

$lib = new Vimeo($clientid, $clientsecret);

$redirecturi = $PAGE->url->get_host() . '/repository/vimeo/authentication_callback.php';

if (!stripos("http", $redirecturi) && !stripos("https", $redirecturi)) {
    $redirecturi = "http://" . $redirecturi;
}

$response = $lib->request('/oauth/access_token', [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirecturi
], 'POST');

if ($response['status'] >= 400) {
    echo html_writer::span(get_string('cannotgeneratetoken', 'repository_vimeo'));
    die;
}

set_config('access_token', $response['body']['access_token'], 'vimeo');
set_config('token_type', $response['body']['token_type'], 'vimeo');

echo html_writer::span(get_string('successfullyauthenticated', 'repository_vimeo'));
die;