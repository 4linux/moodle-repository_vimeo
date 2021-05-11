<?php
use Vimeo\Vimeo;

require 'vendor/autoload.php';

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

    /** @var string $token */
    private $token;

    /** @var string $clientid */
    private $clientid;

    /** @var string $clientsecret  */
    private $clientsecret;

    /** @var Vimeo $vimeolib */
    private $vimeolib;

    /** @var string[] CUSTOM_OPTIONS  */
    const CUSTOM_OPTIONS = [
        'client_id',
        'client_secret',
        'access_token'
    ];

    /** @var string[] VIMEO_SCOPE */
    const VIMEO_SCOPE = [
        'public',
        'private',
        'video_files'
    ];

    /** @var string[] PREFERRED_VIDEO_TYPE */
    const PREFERRED_VIDEO_TYPE = [
      'video/mp4' => '.mp4'
    ];

    const VIDEO_QUALITIES = [
        'high' => 'hd',
        'medium' => 'sd',
        'low' => 'mobile'
    ];

    /** @var string VIMEO_HOST */
    const VIMEO_HOST = "https://api.vimeo.com";

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
        $this->clientid = $this->get_option('client_id');
        $this->clientsecret = $this->get_option('client_secret');

        $this->vimeolib = new Vimeo($this->clientid, $this->clientsecret);

        $this->vimeolib->setToken($this->token);

        $this->get_video_listing('/me/videos', []);
//        die;
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
        global $PAGE;


        $client_id = get_config('vimeo', 'client_id');
        $client_secret = get_config('vimeo', 'client_secret');

        if (empty($client_id)) {
            $client_id = '';
        }

        if (empty($client_secret)) {
            $client_secret = '';
        }

        $mform->addElement('html', "<p>" . get_string('howauthenticate', 'repository_vimeo') . "</p>");

        $mform->addElement('text', 'client_id', get_string('clientid', 'repository_vimeo'), array(
            'value' => $client_id,
            'size' => '40'
        ));

        $mform->setType('client_id', PARAM_RAW_TRIMMED);
        $mform->addRule('client_id', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'client_secret', get_string('clientsecret', 'repository_vimeo'), array(
            'value' => $client_secret,
            'size' => '40'
        ));

        $mform->setType('client_secret', PARAM_RAW_TRIMMED);
        $mform->addRule('client_secret', get_string('required'), 'required', null, 'client');

        $url = self::generate_authorization_url();

        $mform->addElement('hidden', 'access_token', '');

        if (!empty(get_config('vimeo', 'client_id')) && !empty(get_config('client_secret'))) {
            $mform->addElement('html', "<p>" . get_string('authenticatebuttonhelper', 'repository_vimeo') . "</p>");

            $mform->addElement('button', 'authenticatebutton', get_string('authenticatebutton', 'repository_vimeo'));

            $PAGE->requires->js_call_amd('repository_vimeo/authenticate', 'init', [$url]);
        }

    }

    /**
     * Get Vimeo listing
     *
     * @param string $path
     * @param int $page
     * @return array
     */
    public function get_listing($path = '', $page = 1)
    {

        $filters = [
            'page' => $page
        ];

        return $this->get_video_listing('/me/videos', $filters);
    }

    /**
     * Search function
     *
     * @param string $text
     * @param int $page
     * @return array
     */
    public function search($text = '', $page = 1)
    {

        $sort = optional_param('repository_vimeo_select_sort', 'default', PARAM_TEXT);
        $direction = optional_param('repository_vimeo_select_direction', 'asc', PARAM_TEXT);

        if (in_array($sort, ['plays', 'likes'])){
            $direction = 'desc';
        }

        $filters = [
            'page' => $page,
            'query' => $text,
            'sort' => $sort,
            'direction' => $direction
        ];

        return $this->get_video_listing('/me/videos', $filters);
    }

    /**
     * Build search form
     *
     * @return string
     */
    public function print_search() {

        $html = [];

        // label search name
        $html[] = html_writer::tag('label', get_string('searchby', 'repository_vimeo'), [
            'for' => 'label_search_name',
            'class' => 'repository-vimeo-label'
        ]);

        $html[] = html_writer::empty_tag('br');

        // text field search name
        $html[] = html_writer::empty_tag('input', [
            'type' => 'text',
            'name' => 's',
            'value' => '',
            'title' => get_string('searchby', 'repository_vimeo'),
            'class' => 'repository-vimeo-input'
        ]);

        $html[] = html_writer::empty_tag('br');

        // Sort field

        $sortoptions = [
            [
                'value' => 'alphabetical',
                'label' => get_string('alphabetical', 'repository_vimeo')
            ],
            [
                'value' => 'date',
                'label' => get_string('date', 'repository_vimeo')
            ],
            [
                'value' => 'default',
                'label' => get_string('default', 'repository_vimeo'),
                'selected' => 'true'
            ],
            [
                'value' => 'duration',
                'label' => get_string('duration', 'repository_vimeo')
            ],
            [
                'value' => 'likes',
                'label' => get_string('likes', 'repository_vimeo')
            ],
            [
                'value' => 'modified_time',
                'label' => get_string('modifiedtime', 'repository_vimeo')
            ],
            [
                'value' => 'plays',
                'label' => get_string('plays', 'repository_vimeo')
            ]
        ];

        // label select sort
        $html[] = html_writer::tag('label', get_string('sort', 'repository_vimeo'), [
            'for' => 'repository_vimeo_select_sort',
            'class' => 'repository-vimeo-label'
        ]);

        $html[] = html_writer::start_tag('select', [
            'name' => 'repository_vimeo_select_sort',
            'value' => optional_param('repository_vimeo_select_sort', 'default', PARAM_TEXT),
            'title' => get_string('sort', 'repository_vimeo'),
            'class' => 'repository-vimeo-select'
        ]);

        foreach ($sortoptions as $option) {
            $optionparam = [
                'value' => $option['value']
            ];

            if (isset($option['selected'])) {
                $optionparam['selected'] = $option['selected'];
            }

            $html[] = html_writer::tag('option', $option['label'], $optionparam);
        }

        $html[] = html_writer::end_tag('select');

        // Direction field
        $directionoptions = [
            [
                'value' => 'asc',
                'label' => get_string('asc', 'repository_vimeo')
            ],
            [
                'value' => 'desc',
                'label' => get_string('desc', 'repository_vimeo')
            ]
        ];

        // label select sort
        $html[] = html_writer::tag('label', get_string('direction', 'repository_vimeo'), [
            'for' => 'repository_vimeo_select_direction',
            'class' => 'repository-vimeo-label repository-vimeo-label-inline'
        ]);

        $html[] = html_writer::start_tag('select', [
            'name' => 'repository_vimeo_select_direction',
            'value' => 'asc',
            'title' => get_string('direction', 'repository_vimeo'),
            'class' => 'repository-vimeo-select'
        ]);

        foreach ($directionoptions as $option) {
            $html[] = html_writer::tag('option', $option['label'], [
                'value' => $option['value']
            ]);
        }

        $html[] = html_writer::end_tag('select');

        $html[] = html_writer::empty_tag('br');

        // Submit button
        $html[] = html_writer::empty_tag('input', [
            'type' => 'submit',
            'name' => 'repository_vimeo_submit_button',
            'value' => get_string('search', 'repository_vimeo'),
            'title' => get_string('search', 'repository_vimeo'),
            'class' => 'repository-vimeo-button'
        ]);
        $html[] = html_writer::empty_tag('br');


        return join('', $html);
    }

//    /**
//     * file types supported by vimeo plugin
//     *
//     * @return array
//     */
//    public function supported_filetypes()
//    {
//        return array(
//            'video'
//        );
//    }
//
//    /**
//     * Vimeo plugin only return external links
//     *
//     * @return int
//     */
//    public function supported_returntypes()
//    {
//        return FILE_EXTERNAL;
//    }

    /**
     * Get custom options from config table.
     *
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '')
    {

        if (in_array($config, self::CUSTOM_OPTIONS)) {
            return trim(get_config('vimeo', $config));
        }

        return parent::get_option($config);
    }

    /**
     * Save custom options in config table.
     *
     * @param array $options
     * @return boolean
     */
    public function set_option($options = array())
    {

        foreach(self::CUSTOM_OPTIONS as $customconfig) {

            if (! empty($options[$customconfig])) {
                set_config($customconfig, trim($options[$customconfig]), 'vimeo');
            }

            unset($options[$customconfig]);

        }

        return parent::set_option($options);
    }

    /**
     * Names of the plugin settings
     *
     * @return array
     */
    public static function get_type_option_names()
    {

        return array_merge([
            'pluginname'
        ], self::CUSTOM_OPTIONS);
    }

    /**
     * Generate authorization URL
     *
     * @return string
     */
    private static function generate_authorization_url() {

        global $PAGE;

        $client_id = get_config('vimeo', 'client_id');

        $redirecturi = $PAGE->url->get_host() . '/repository/vimeo/authentication_callback.php';

        if (!stripos("http", $redirecturi) && !stripos("https", $redirecturi)) {
            $redirecturi = "http://" . $redirecturi;
        }

        $scope = join("+", self::VIMEO_SCOPE);

        return self::VIMEO_HOST . "/oauth/authorize?response_type=code&client_id={$client_id}&redirect_uri={$redirecturi}&scope={$scope}";
    }

    /**
     * Retrieve a list of videos from Vimeo
     *
     * @param $path
     * @param $filters
     * @param false $nosearch
     * @return array
     */
    private function get_video_listing($path, $filters, $nosearch = false) {

        $params = $this->build_listing_filters($filters);

        $response = $this->vimeolib->request($path, (array) $params, 'GET');

        $options = new stdClass();

        $options->list = [];
        $options->dynload = true;
        $options->norefresh = true;
        $options->nologin = true;
        $options->nosearch = $nosearch;
        $options->page = $response['body']['page'];
        $options->pages = $response['body']['total'] / $params->per_page;

        foreach ($response['body']['data'] as $video) {
            $uri = str_replace('videos', 'video', $video['uri']);

            $url = 'http://vimeo.com/api/v2' . $uri . '.json';

            $dataVideo = file_get_contents($url);

            $dataVideo = json_decode($dataVideo, true);

            $thumbnail = $dataVideo[0]['thumbnail_medium'] ?: end($video['pictures']['sizes'])['link'];

            $videosrc = $this->process_video($video['files']);

            $options->list[] = [
                'title' => $video['name'],
                'source' => $videosrc['link_secure'],
                'thumbnail' => $thumbnail,
                'thumbnail_width' => 175,
                'thumbnail_height' => 125
            ];
        }

        return (array) $options;
    }

    /**
     * Build filter params to request
     *
     * @param $filters
     * @return stdClass
     */
    private function build_listing_filters($filters) {
        $options = new stdClass();

        $options->sort = $filters['sort'] ?: 'date';
        $options->direction = $filters['direction'] ?: 'asc';
        $options->page = $filters['page'] ?: 1;
        $options->per_page = $filters['perpage'] ?: 12;
        $options->query = $filters['query'] ?: '';

        return $options;
    }

    /**
     * Filter accept files
     *
     * @param $file
     * @return bool
     */
    private function filter_type($file) {

        if (!in_array($file['type'], array_keys(self::PREFERRED_VIDEO_TYPE))) {
            return false;
        }

        $hasfound = false;

        foreach (self::PREFERRED_VIDEO_TYPE as $extension) {
            if (stripos($file['link_secure'], $extension) !== false) {
                $hasfound = true;
            }
        }

        return $hasfound;
    }

    /**
     * Look for the higher file quality
     *
     * @param $files
     * @param string $quality
     * @return array
     */
    private function get_video_by_quality($files, $quality = 'high') {
        $file = array_filter($files, function ($file) use ($quality) {
            return $file['quality'] === self::VIDEO_QUALITIES[$quality];
        });

        if (!count($file)) {
            $keys = array_keys(self::VIDEO_QUALITIES);

            $index = array_search($quality, $keys);

            if (array_key_exists($keys[$index + 1], self::VIDEO_QUALITIES)) {
                $file = [$this->get_video_by_quality($files, $keys[$index + 1])];
            } else {
                $file = [$files[0]];
            }
        }

        return array_shift($file);
    }

    /**
     * Look for accepted file with high quality
     *
     * @param array $files
     * @return array
     */
    private function process_video($files) {

        $filteredfiles = array_filter($files, [$this, 'filter_type']);

        return $this->get_video_by_quality($filteredfiles);

    }

}