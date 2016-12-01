<?php
if(!class_exists('JzTwitterOptions')){
    
    class JzTwitterOptions{
        
        
        public function __construct(){
            add_action('admin_menu', array($this, 'add_options_page'));
            add_action('admin_init', array($this, 'register_settings'));
        }


        public function get_keys() {
            $api_keys = array();
            $api_keys['consumer_id'] = get_option('consumer_id');
            $api_keys['consumer_secret'] = get_option('consumer_secret');
            $api_keys['access_token'] = get_option('access_token');
            $api_keys['access_token_secret'] = get_option('access_token_secret');
            return $api_keys;
        }

        public function _getOption($namespace, $name) {
            return get_option($namespace . '_' . $name);
        }

        public function _setOption($namespace, $name, $value) {
            update_option($namespace . '_' . $name, $value);
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
            $api_keys = $this->get_keys();
            ?>

        <div class="wrap">
            <h2>Twitter Api keys</h2>

            <form method="post" action="options.php">
            <?php settings_fields('ta-settings-group'); ?>
                <?php //do_settings('baw-settings-group');  ?>
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row">Consumer key</th>
                    <td>
                        <input type="text" name="consumer_id" size="65" value="<?php echo $api_keys['consumer_id']; ?>" />
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row">Consumer secret</th>
                    <td>
                        <input type="text" name="consumer_secret" size="65" value="<?php echo $api_keys['consumer_secret']; ?>" />
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row">Access token</th>
                    <td>
                        <input type="text" name="access_token" size="65" value="<?php echo $api_keys['access_token']; ?>" />
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row">Access token secret</th>
                    <td>
                        <input type="text" name="access_token_secret" size="65" value="<?php echo $api_keys['access_token_secret']; ?>" />
                    </td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>



            



        </div>
        <?php
        }
        
    }
    
}