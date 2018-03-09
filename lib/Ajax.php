<?php
if(!class_exists('JzTwitterAjax')){

    /**
     * Class JzTwitterAjax
     */
    class JzTwitterAjax{

        /** @var int  */
        private $_numOfTweets = 3;

        /** @var JzTwitterOptions */
        private $_options;

        /**
         * JzTwitterAjax constructor.
         * @param JzTwitterOptions $options
         */
        public function __construct(JzTwitterOptions $options){
            $this->_options = $options;
            add_action('wp_ajax_jz_twitter_request', array($this, 'request'));
        }

        /**
         * Ajax response
         */
        public function request() {
            $username = 'Favstar_Bot';
            $namespace = 'twitter_ajax_' . $username;
            
            $api_keys = $this->_options->get_keys();
            $twitter_feed = $this->_options->_getOption($namespace, 'rss_feed');
            if ($twitter_feed != null)
                $twitter_feed = unserialize($twitter_feed);
            
            $cache_time = $this->_options->_getOption($namespace, 'last_actualization');
            
            if ($cache_time == null || ( $cache_time + ( 30 ) ) < time() || $twitter_feed == null) {
                $connection = new TwitterOAuth($api_keys['consumer_id'], $api_keys['consumer_secret'], $api_keys['access_token'], $api_keys['access_token_secret']);
                $search_feed3 = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . esc_attr($username) . "&count=" . esc_attr($this->_numOfTweets);
                $reponse = $connection->get($search_feed3);
                
                if ($reponse instanceof WP_Error)
                    return null;
                
                if (isset($reponse->errors)) {
                    $feed['date'] = $reponse->errors[0]->code;
                    
                    switch ($reponse->errors[0]->code) {
                        case 32: $feed['description'] = 'Please check setting Twitter API in Settings -> Twitter Live';
                            break;
                        case 88: $feed['description'] = 'Rate limit exceeded.';
                            break;
                        case 215: $feed['description'] = 'Don`t you have set Twitter API in Settings -> Twitter Live';
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
                    $this->_options->_setOption($namespace, 'rss_feed', $twitter_parsed_data);
                    $this->_options->_setOption($namespace, 'last_actualization', time());
                }
            }
            
            $this->render($twitter_feed);
            
            die();
        }

        /**
         * @param array $twitterFeed
         */
        private function render(array $twitterFeed)
        {
            foreach ($twitterFeed as $tweet) {
                echo '<li class="' . esc_attr(str_replace(' ', '', $tweet['date'])) . '">' . esc_html($tweet['description']) . '</li>';
            }
        }
    }    
}