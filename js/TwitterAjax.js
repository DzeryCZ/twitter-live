"use strict";

jQuery(document).ready(function() {

    (function($, undefined) {

        var $twitterhtml = $('.titter_ajax .tweets');
        
        setInterval(function() {
            TwitterAjax.request($twitterhtml);
        }, 20 * 1000);

    }).call(TwitterAjax, jQuery);

});




var TwitterAjax = TwitterAjax || {};

TwitterAjax.request = function($twitterhtml) {

    jQuery.post(
            JzTwitterAjaxData['ajaxurl'], 
            {
                'action': 'jz_twitter_request',
            },
            function(response) {
                if(response != ""){
                    $twitterhtml.html(response);
                }
            });

}