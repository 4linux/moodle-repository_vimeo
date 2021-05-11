<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin capabilities.
 *
 * @package repository_vimeo
 * @copyright 2017 Denis Ribeiro
 * @author Denis Ribeiro <dpr001@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Vimeo videos';
$string['access_token'] = 'Vimeo access token';
$string['clientid'] = 'Vimeo client id';
$string['clientsecret'] = 'Vimeo client secret';
$string['authenticatebutton'] = 'Authenticate';
$string['configplugin'] = 'Vimeo repository type configuration';
$string['vimeo:view'] = 'View the myplugin repository';
$string['authenticatebuttonhelper'] = 'Click the Authenticate button to generate the authentication token.
You will be directed to the Vimeo platform to allow access to the videos.';
$string['howauthenticate'] = "To perform authentication, follow these steps:
<ol>
    <li>Fill in the Client ID field (Can be obtained from your <a href='https://developer.vimeo.com/apps' target='_blank'> Vimeo application </a>);</li>
    <li>Fill in the Client secret field (Can be obtained from your <a href='https://developer.vimeo.com/apps' target='_blank'> Vimeo application </a>);</li>
    <li>Click save;</li>
    <li>Return to that setting; and</li>
    <li>Press the authenticate button.</li>
</ol> 
";
$string['cannotgeneratetoken'] ="Could not generate authentication token";
$string['cannotgeneratecode'] ="Unable to get authorization code";
$string['successfullyauthenticated'] = "Authentication succeeded";
$string['vimeoauthentication'] = "Vimeo authentication";
$string['sort'] = 'Sort';
$string['search'] = 'Search';
$string['searchby'] = 'Search by';
$string['direction'] = 'Direction';
$string['date'] = 'Date';
$string['duration'] = 'Duration';
$string['alphabetical'] = 'Alphabetical';
$string['default'] = 'Default';
$string['likes'] = 'Likes';
$string['modifiedtime'] = 'Modified time';
$string['plays'] = 'Plays';
$string['asc'] = 'Ascendant';
$string['desc'] = 'Descendant';