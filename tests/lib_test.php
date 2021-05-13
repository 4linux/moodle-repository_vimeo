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
 * Vimeo repository test case
 *
 * @package    repository_vimeo
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

/**
 * Vimeo repository test case
 *
 * @package    repository_vimeo
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_vimeo_lib_testcase extends advanced_testcase {

    /** @var repository_vimeo $repo */
    protected $repo;

    /**
     * Prepares things before this test case is initialised
     *
     * @return void
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require 'vendor/autoload.php';

        require_once($CFG->dirroot . '/repository/vimeo/vendor/autoload.php');
        require_once($CFG->dirroot . '/repository/vimeo/lib.php');
    }

    /**
     * Set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test the construction of the filter
     */
    public function test_build_video_filter() {
        $this->resetAfterTest();

        $repolib = new repository_vimeo(1);

        $filters = [
          'sort' => 'duration',
          'direction' => 'desc',
          'page' => 2,
          'perpage' => 20,
          'query' => 'text to search'
        ];

        $filterobj = $repolib->build_listing_filters($filters);

        $this->assertInstanceOf('stdClass', $filterobj);
        $this->assertEquals($filters['sort'], $filterobj->sort);
        $this->assertEquals($filters['direction'], $filterobj->direction);
        $this->assertEquals($filters['page'], $filterobj->page);
        $this->assertEquals($filters['perpage'], $filterobj->per_page);
        $this->assertEquals($filters['query'], $filterobj->query);
    }

    /**
     * Test the construction of the filter
     */
    public function test_get_high_quality_video() {
        $this->resetAfterTest();

        $repolib = new repository_vimeo(1);

        $files = [
            [
                'type' => 'video/mp4',
                'quality' => 'mobile',
                'width' => 480,
                'height' => 270,
                'link' => 'http://player.vimeo.com/external/123456789.mobile.mp4',
                'created_time' => '2015-08-10T17:47:04+00:00',
                'fps' => 25,
                'size' => 69739055,
                'md5' => 'e6caf9f1dd40c06cbcf60c2740b03154',
                'public_name' => 'Mobile SD',
                'size_short' => '66.51MB',
                'link_secure' => 'http://player.vimeo.com/external/123456789.mobile.mp4'
            ],
            [
                'type' => 'video/mp4',
                'quality' => 'hd',
                'width' => 1024,
                'height' => 576,
                'link' => 'http://player.vimeo.com/external/123456789.hd.mp4',
                'created_time' => '2015-08-10T17:47:04+00:00',
                'fps' => 25,
                'size' => 131930260,
                'md5' => 'e6caf9f1dd40c06cbcf60c2740b03154',
                'public_name' => 'HD 720p',
                'size_short' => '125.82MB',
                'link_secure' => 'http://player.vimeo.com/external/123456789.hd.mp4'
            ],
            [
                'type' => 'video/mp4',
                'quality' => 'sd',
                'width' => 640,
                'height' => 360,
                'link' => 'http://player.vimeo.com/external/123456789.sd.mp4',
                'created_time' => '2015-08-10T17:47:04+00:00',
                'fps' => 25,
                'size' => 82610619,
                'md5' => 'e6caf9f1dd40c06cbcf60c2740b03154',
                'public_name' => 'SD',
                'size_short' => '78.78MB',
                'link_secure' => 'http://player.vimeo.com/external/123456789.sd.mp4'
            ],
            [
                'type' => 'video/mp4',
                'quality' => 'hls',
                'link' => 'http://player.vimeo.com/external/123456789.m3u8',
                'created_time' => '2015-08-10T17:47:04+00:00',
                'fps' => 25,
                'size' => 69739055,
                'md5' => 'e6caf9f1dd40c06cbcf60c2740b03154',
                'public_name' => 'Mobile SD',
                'size_short' => '66.51MB',
                'link_secure' => 'http://player.vimeo.com/external/123456789.m3u8'
            ],
        ];

        $filteredfiles = array_filter($files, [$repolib, 'filter_type']);

        $this->assertNotEmpty(count($filteredfiles));

        foreach ($filteredfiles as $file) {
            $this->assertStringContainsString('.mp4', $file['link']);
        }

        $file = $repolib->get_video_by_quality($filteredfiles, 'high');

        $this->assertEquals($repolib::VIDEO_QUALITIES['high'], $file['quality']);

        $file = $repolib->get_video_by_quality($filteredfiles, 'medium');

        $this->assertEquals($repolib::VIDEO_QUALITIES['medium'], $file['quality']);

        $file = $repolib->get_video_by_quality($filteredfiles, 'low');

        $this->assertEquals($repolib::VIDEO_QUALITIES['low'], $file['quality']);

        unset($filteredfiles[1]);

        $file = $repolib->get_video_by_quality($filteredfiles, 'high');

        $this->assertEquals($repolib::VIDEO_QUALITIES['medium'], $file['quality']);

        $file = $repolib->process_video($files);

        $this->assertStringContainsString('.mp4', $file['link']);

        $this->assertEquals($repolib::VIDEO_QUALITIES['high'], $file['quality']);

    }

}
