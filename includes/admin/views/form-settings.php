<?php
global $post;

$form_settings  = wpuf_get_form_settings( $post->ID );

$redirect_to    = isset( $form_settings['redirect_to'] ) ? $form_settings['redirect_to'] : 'same';
$message        = isset( $form_settings['message'] ) ? $form_settings['message'] : __( 'Post saved', 'best-contact-form' );
$update_message = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Post updated successfully', 'best-contact-form' );
$page_id        = isset( $form_settings['page_id'] ) ? $form_settings['page_id'] : 0;
$url            = isset( $form_settings['url'] ) ? $form_settings['url'] : '';

$submit_text    = isset( $form_settings['submit_text'] ) ? $form_settings['submit_text'] : __( 'Submit', 'best-contact-form' );

?>
<table class="form-table">
    <tr class="wpuf-redirect-to">
        <th><?php _e( 'Redirect To', 'best-contact-form' ); ?></th>
        <td>
            <select name="wpuf_settings[redirect_to]">
                <?php
                $redirect_options = array(
                    'same' => __( 'Same Page', 'best-contact-form' ),
                    'page' => __( 'To a page', 'best-contact-form' ),
                    'url'  => __( 'To a custom URL', 'best-contact-form' )
                );

                foreach ($redirect_options as $to => $label) {
                    printf('<option value="%s"%s>%s</option>', $to, selected( $redirect_to, $to, false ), $label );
                }
                ?>
            </select>
            <p class="description">
                <?php _e( 'After successfull submit, where the page will redirect to', $domain = 'default' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-same-page">
        <th><?php _e( 'Message to show', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[message]"><?php echo esc_textarea( $message ); ?></textarea>
        </td>
    </tr>

    <tr class="wpuf-page-id">
        <th><?php _e( 'Page', 'best-contact-form' ); ?></th>
        <td>
            <select name="wpuf_settings[page_id]">
                <?php
                $pages = get_posts(  array( 'numberposts' => -1, 'post_type' => 'page') );

                foreach ($pages as $page) {
                    printf('<option value="%s"%s>%s</option>', $page->ID, selected( $page_id, $page->ID, false ), esc_attr( $page->post_title ) );
                }
                ?>
            </select>
        </td>
    </tr>

    <tr class="wpuf-url">
        <th><?php _e( 'Custom URL', 'best-contact-form' ); ?></th>
        <td>
            <input type="url" name="wpuf_settings[url]" value="<?php echo esc_attr( $url ); ?>">
        </td>
    </tr>

    <tr class="wpuf-submit-text">
        <th><?php _e( 'Submit Button text', 'best-contact-form' ); ?></th>
        <td>
            <input type="text" name="wpuf_settings[submit_text]" value="<?php echo esc_attr( $submit_text ); ?>">
        </td>
    </tr>
</table>