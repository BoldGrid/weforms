;(function($) {
    $.fn.extend({
        /**
         * Custom jQuery serialize wrapper.
         *
         * When WordPress 5.6 increased the jQuery version to 3.5.1, the serialize function changed. Instead of
         * sending spaces as "+", they are sent as "%20". This wrapper is for backwards compatibility.
         *
         * @todo This function is duplicated in both the frontend and backend. Need to have the code live in
         * just one location.
         *
         * @since 1.6.7
         */
        weSerialize: function() {
            return $( this ).serialize().replaceAll( '%20', '+' );
        },
    });
})(jQuery);