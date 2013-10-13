;
jQuery(document).ready(function() {

    ;
    (function($, undefined) {

        $twitterhtml = $('.titter_ajax .tweets');
        
        setInterval(function() {
            TwitterAjax.request($twitterhtml);
        }, 20 * 1000);

    }).call(TwitterAjax, jQuery);

});




var TwitterAjax = TwitterAjax || {};

TwitterAjax.request = function($twitterhtml) {

    jQuery.post(
            'admin-ajax.php',
            {
                'action': 'request',
            },
            function(response) {

                var Tnew = jQuery(jQuery.parseHTML(response)).last().attr('class');
                var Tlength = $twitterhtml.find('li').length;
                var Tcount = 1;

                jQuery.each($twitterhtml.find('li'), function(key, tweet) {

                    if (jQuery(tweet).attr('class') == Tnew) {

                        Tcount = key + 1;


                    }

                });

                if (Tcount < Tlength) {

                    for (var i = Tlength - Tcount - 1 ; i >= 0; i--) {

                        //Static
                        //$twitterhtml.prepend(jQuery(jQuery.parseHTML(response)).eq(i));
                        //$twitterhtml.find('li').eq(Tlength).remove();

                        //Animated
                        $twitterhtml.find('li').eq(Tlength - 1).slideUp(500, function() {
                            jQuery(this).remove();
                        });
                        jQuery(jQuery.parseHTML(response)).eq(i).hide().prependTo($twitterhtml).slideDown(500);

                    }

                }

            });

}