<?php
if(!class_exists('JzTwitterShortcode')){
    
    class JzTwitterShortcode{
        
        
        public function __construct(){
            add_shortcode('twitter_live', array($this, 'do_twitter_live'));
        }


        public function do_twitter_live($atts){
            $atts = shortcode_atts( array(
                'user' => '',
                'tweets' => '3'
            ), $atts, 'twitter_live' );

            $out = "";

            $out .= '<div class="titter_ajax">';
            $out .= '<ul class="tweets">';
            
            $out .= '</ul>';
            $out .= '</div>';

            return $out;
        }
    }    
}