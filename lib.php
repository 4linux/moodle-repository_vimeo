<?php
use Vimeo\Vimeo;

require 'vendor/autoload.php';
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

class repository_vimeo extends repository
{

    private $token;

    private $client_id;

    private $client_secret;

    /**
     * Vimeo plugin constructor
     *
     * @param int $repositoryid            
     * @param object $context            
     * @param array $options            
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array())
    {
        parent::__construct($repositoryid, $context, $options);
        
        $this->token = $this->get_option('access_token');
        
        // Without an API key, don't show this repo to users as its useless without it.
        if (empty($this->token)) {
            $this->disabled = true;
        }
    }

    /**
     * Add plugin settings input to Moodle form.
     *
     * @param object $mform            
     * @param string $classname            
     */
    public static function type_config_form($mform, $classname = 'repository')
    {
        // parent::type_config_form($mform, $classname);
        $token = get_config('vimeo', 'access_token');
        $client_id = get_config('vimeo', 'client_id');
        $client_secret = get_config('vimeo', 'client_secret');
        
        if (empty($token)) {
            $token = '';
        }
        
        if (empty($client_id)) {
            $client_id = '';
        }
        
        if (empty($client_secret)) {
            $client_secret = '';
        }
        
        $mform->addElement('text', 'access_token', get_string('access_token', 'repository_vimeo'), array(
            'value' => $token,
            'size' => '40'
        ));
        
        $mform->setType('access_token', PARAM_RAW_TRIMMED);
        $mform->addRule('access_token', get_string('required'), 'required', null, 'client');
    }

    public function get_listing($path = '', $page = '')
    {
        $lib = new Vimeo($this->client_id, $this->client_secret);
        
        $lib->setToken($this->token);
        
        $response = $lib->request('/me/videos', array(
            'per_page' => 2
        ), 'GET');
        
        $list['list'] = [];
        
        // /videos/224317761
        
        foreach ($response['body']['data'] as $video) {
            $uri = str_replace('videos', 'video', $video['uri']);
            
            $url = 'http://vimeo.com/api/v2' . $uri . '.json';
            
            $dataVideo = file_get_contents($url);
            
            $dataVideo = json_decode($dataVideo, true);
            
            $list['list'][] = [
                'title' => $video['name'] . '.mp4',
                'source' => $video['link'],
                'thumbnail' => $dataVideo[0]['thumbnail_medium'],
                'thumbnail_width' => 150,
                'thumbnail_height' => 150
            
            ];
        }
        
        return $list;
    }

    /**
     * file types supported by vimeo plugin
     *
     * @return array
     */
    public function supported_filetypes()
    {
        return array(
            'video'
        );
    }

    /**
     * Vimeo plugin only return external links
     *
     * @return int
     */
    public function supported_returntypes()
    {
        return FILE_EXTERNAL;
    }

    /**
     * Get access_token from config table.
     *
     * @param string $config            
     * @return mixed
     */
    public function get_option($config = '')
    {
        if ($config === 'access_token') {
            return trim(get_config('vimeo', 'access_token'));
        } else {
            $options['access_token'] = trim(get_config('vimeo', 'access_token'));
        }
        
        return parent::get_option($config);
    }

    /**
     * Save access_token in config table.
     *
     * @param array $options            
     * @return boolean
     */
    public function set_option($options = array())
    {
        if (! empty($options['access_token'])) {
            set_config('access_token', trim($options['access_token']), 'vimeo');
        }
        unset($options['access_token']);
        return parent::set_option($options);
    }

    /**
     * Names of the plugin settings
     *
     * @return array
     */
    public static function get_type_option_names()
    {
        return array(
            'access_token',
            'pluginname'
        );
    }
}