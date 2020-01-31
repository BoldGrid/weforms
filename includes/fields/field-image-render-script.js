;(function($) {
    $(document).ready( function(){
        var uploader = new WPUF_Uploader('wpuf-<?php echo esc_attr( $unique_id ); ?>-pickfiles', 'wpuf-<?php echo esc_attr( $unique_id ); ?>-upload-container', <?php echo esc_attr( $field_settings['count'] ); ?>, '<?php echo esc_attr( $field_settings['name'] ); ?>', 'jpg,jpeg,gif,png,bmp', <?php echo  esc_attr($field_settings['max_size']) ?>);
        wpuf_plupload_items.push(uploader);
    });
})(jQuery);
