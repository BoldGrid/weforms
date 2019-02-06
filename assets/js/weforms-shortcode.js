/*jshint devel:true */
/*global send_to_editor */
/*global tb_remove */

jQuery(function($) {

    $('#weforms-form-insert').on('click', function(e) {
        e.preventDefault();

        var shortcode  = '';

        var form_id    = $('#weforms-form-select').val();
        shortcode     += '[weforms id="' + form_id + '"]';

        send_to_editor(shortcode);
        tb_remove();
    });
});
