<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * File called from Vimeo when the authentication has finished
 *
 * @package    repository_vimeo
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Vimeo\Vimeo;

require_once('vendor/autoload.php');
require('../../config.php');

$code = $_GET['code'];

if (empty($code)) {
    echo html_writer::span(get_string('cannotgeneratecode', 'repository_vimeo'));
    die;
}

require_login(null, false);

$PAGE->set_context(context_system::instance());

$PAGE->set_url('/repository/vimeo/authentication_callback.php', array('code' => $code));

$modulename = get_string('pluginname', 'repository_vimeo');

$PAGE->navbar->add($modulename);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('vimeoauthentication', 'repository_vimeo'));

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