<?php
/*
  Plugin Name: Twitter Live
  Plugin URI: http://zivny.eu
  Description:
  Author: Jaroslav Živný
  Author URI: http://zivny.eu
  Version: 0.1
 */

define('TWITTER_AJAX_URI', plugins_url() . '/twitter-live');
require_once 'OAuth/twitteroauth.php';

class twitterAjax {

    private $_api_keys = array();
    private $_numOfTweets = 3;

    public function __construct() {
        $this->get_keys();
        $this->load_scripts();
        add_action('wp_ajax_request', array($this, 'request'));
        add_action('admin_menu', array($this, 'add_options_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    private function get_keys() {
        $this->_api_keys['consumer_id'] = get_option('consumer_id');
        $this->_api_keys['consumer_secret'] = get_option('consumer_secret');
        $this->_api_keys['access_token'] = get_option('access_token');
        $this->_api_keys['access_token_secret'] = get_option('access_token_secret');
    }

    private function _getOption($namespace, $name) {
        return get_option($namespace . '_' . $name);
    }

    private function _setOption($namespace, $name, $value) {
        update_option($namespace . '_' . $name, $value);
    }

    /* ================ LOAD SCRIPTS ================ */

    private function load_scripts() {
        wp_register_script('TwitterAjax', TWITTER_AJAX_URI . '/js/TwitterAjax.js', array('jquery'));
        wp_enqueue_script('TwitterAjax');
    }

    /* ================ AJAX REQUEST ================ */

    public function request() {
        $username = 'Favstar_Bot';
        $namespace = 'twitter_ajax_' . $username;

        $twitter_feed = $this->_getOption($namespace, 'rss_feed');
        if ($twitter_feed != null)
            $twitter_feed = unserialize($twitter_feed);

        $cache_time = $this->_getOption($namespace, 'last_actualization');

        if ($cache_time == null || ( $cache_time + ( 30 ) ) < time() || $twitter_feed == null) {

            $connection = new TwitterOAuth($this->_api_keys['consumer_id'], $this->_api_keys['consumer_secret'], $this->_api_keys['access_token'], $this->_api_keys['access_token_secret']);
            $search_feed3 = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $username . "&count=" . $this->_numOfTweets;
            $reponse = $connection->get($search_feed3);

            if ($reponse instanceof WP_Error)
                return null;

            if (isset($reponse->errors)) {
                
                $feed['date'] = $reponse->errors[0]->code;
                
                switch ($reponse->errors[0]->code) {
                    case 32: $feed['description'] = 'Please check setting Twitter API in Theme Options -> Advanced?';
                        break;
                    case 88: $feed['description'] = 'Rate limit exceeded, please check "Actualize every X minutes" item in Twitter J&W Widget. Recommended value is 60.';
                        break;
                    case 215: $feed['description'] = 'Don`t you have set Twitter API in Theme Options -> Advanced?';
                        break;
                    default: $feed['description'] = 'Your user name is probably wrong<br>Please check it';
                        break;
                }
                
                $twitter_feed[] = $feed;
                
            } else {

                $twitter_parsed_data = array();

                foreach ($reponse as $i => $tweet) {
                    $twitter_parsed_data[$i]['description'] = $tweet->text;
                    $twitter_parsed_data[$i]['date'] = $tweet->created_at;
                }

                $twitter_feed = $twitter_parsed_data;
                $twitter_parsed_data = serialize($twitter_parsed_data);
                $this->_setOption($namespace, 'rss_feed', $twitter_parsed_data);
                $this->_setOption($namespace, 'last_actualization', time());
                
            }
        }

        foreach ((array) $twitter_feed as $tweet) {
            echo '<li class="' . str_replace(' ', '', $tweet['date']) . '">' . $tweet['description'] . '</li>';
        }

        die();
    }

    /* ================ SETTINGS ================ */

    public function add_options_page() {
        add_options_page('Twitter Live', 'Twitter Live', 'edit_plugins', 'twitter_ajax', array($this, 'settings_page'));
    }

    function register_settings() {
        //register our settings
        register_setting('ta-settings-group', 'consumer_id');
        register_setting('ta-settings-group', 'consumer_secret');
        register_setting('ta-settings-group', 'access_token');
        register_setting('ta-settings-group', 'access_token_secret');
    }

    public function settings_page() {
        ?>

        <div class="wrap">
            <h2>Twitter Api keys</h2>

            <form method="post" action="options.php">
                <?php settings_fields('ta-settings-group'); ?>
                <?php //do_settings('baw-settings-group');  ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Consumer key</th>
                        <td><input type="text" name="consumer_id" size="65" value="<?php echo $this->_api_keys['consumer_id']; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Consumer secret</th>
                        <td><input type="text" name="consumer_secret" size="65" value="<?php echo $this->_api_keys['consumer_secret']; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Access token</th>
                        <td><input type="text" name="access_token" size="65" value="<?php echo $this->_api_keys['access_token']; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Access token secret</th>
                        <td><input type="text" name="access_token_secret" size="65" value="<?php echo $this->_api_keys['access_token_secret']; ?>" /></td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>



            <div class="titter_ajax">
                <ul class="tweets">

                    <?php $this->request(3); ?>

                </ul>
            </div>



        </div>
        <?php
    }

}

$twitterAjax = new twitterAjax;
?>
