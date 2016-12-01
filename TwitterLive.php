<?php
/*
Plugin Name: Twitter Live
Plugin URI: http://zivny.eu
Description:
Author: Jaroslav Živný
Author URI: http://zivny.eu
Version: 0.2
*/
error_reporting(E_ALL); ini_set('display_errors', 1); 
if(!class_exists('JzTwitterLive')){

    define('TWITTER_LIVE_URI', plugins_url() . '/twitter-live');

    require_once 'OAuth/twitteroauth.php';
    require_once 'lib/Ajax.php';
    require_once 'lib/Options.php';
    require_once 'lib/Shortcode.php';
    
    class JzTwitterLive {
        
        
        
        public function __construct() {
            $this->load_scripts();
            $options = new JzTwitterOptions();
            $ajax = new JzTwitterAjax($options);
            new JzTwitterShortcode();
        }
        
           
        /* ================ LOAD SCRIPTS ================ */
        
        private function load_scripts() {
            wp_register_script('JzTwitterAjax', TWITTER_LIVE_URI . '/js/TwitterAjax.js', array('jquery'));
            wp_enqueue_script('JzTwitterAjax');
            wp_localize_script('JzTwitterAjax', "JzTwitterAjaxData", array('ajaxurl' => admin_url('admin-ajax.php')));
        }
             

    }

    $JzTwitterLive = new JzTwitterLive;
}
?>