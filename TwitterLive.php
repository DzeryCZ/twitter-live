<?php
/*
Plugin Name: Twitter Live
Plugin URI: http://zivny.eu
Description:
Author: Jaroslav Živný
Author URI: http://zivny.eu
Version: 0.2
*/

if(!class_exists('JzTwitterLive')){

    define('TWITTER_LIVE_URI', plugins_url() . '/twitter-live');

    require_once 'OAuth/twitteroauth.php';
    require_once 'lib/Ajax.php';
    require_once 'lib/Options.php';
    require_once 'lib/Shortcode.php';

    /**
     * Class JzTwitterLive
     */
    class JzTwitterLive {

        /**
         * JzTwitterLive constructor.
         */
        public function __construct() {
            $this->load_scripts();
            $options = new JzTwitterOptions();
            $ajax = new JzTwitterAjax($options);
            new JzTwitterShortcode();
        }

        /**
         * load_scripts
         */
        private function load_scripts() {
            wp_register_script('JzTwitterAjax', TWITTER_LIVE_URI . '/js/TwitterAjax.js', array('jquery'));
            wp_enqueue_script('JzTwitterAjax');
            wp_localize_script('JzTwitterAjax', "JzTwitterAjaxData", array('ajaxurl' => admin_url('admin-ajax.php')));
        }
    }

    // Start the app
    $JzTwitterLive = new JzTwitterLive;
}
?>