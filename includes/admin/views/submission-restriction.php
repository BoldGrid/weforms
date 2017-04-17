<?php
global $post;

$form_settings     = wpuf_get_form_settings( $post->ID );

$limit_entries     = isset( $form_settings['limit_entries'] ) ? $form_settings['limit_entries'] : '';
$limit_number      = isset( $form_settings['limit_number'] ) ? $form_settings['limit_number'] : '';
$limit_message     = isset( $form_settings['limit_message'] ) ? $form_settings['limit_message'] : '';

$require_login     = isset( $form_settings['require_login'] ) ? $form_settings['require_login'] : '';
$req_login_message = isset( $form_settings['req_login_message'] ) ? $form_settings['req_login_message'] : '';

?>
<table class="form-table">
    <tr class="wpuf-limit-entries">
        <th><?php _e( 'Limit Entries', 'wpuf-contact-form' ); ?></th>
        <td>
            <label>
                <input type="hidden" name="wpuf_settings[limit_entries]" value="false">
                <input type="checkbox" name="wpuf_settings[limit_entries]" value="true"<?php checked( $limit_entries, 'true' ); ?>>
                <?php _e( 'Enable form entry limit', 'wpuf-contact-form' ); ?>
            </label>

            <p class="description">
                <?php _e( 'Limit the number of entries allowed for this form', 'wpuf-contact-form' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-number-entries show-if-limit-entry">
        <th>&mdash; <?php _e( 'Number of Entries', 'wpuf-contact-form' ); ?></th>
        <td>
            <input type="number" name="wpuf_settings[limit_number]" value="<?php echo $limit_number; ?>">
        </td>
    </tr>

    <tr class="wpuf-limit-message show-if-limit-entry">
        <th>&mdash; <?php _e( 'Limit Reached Message', 'wpuf-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[limit_message]"><?php echo esc_textarea( $limit_message ); ?></textarea>
        </td>
    </tr>

    <tr class="wpuf-require-login">
        <th><?php _e( 'Require Login', 'wpuf-contact-form' ); ?></th>
        <td>
            <label>
                <input type="hidden" name="wpuf_settings[require_login]" value="false">
                <input type="checkbox" name="wpuf_settings[require_login]" value="true"<?php checked( $require_login, 'true' ); ?>>
                <?php _e( 'Require user to be logged in', 'wpuf-contact-form' ); ?>
            </label>
        </td>
    </tr>

    <tr class="wpuf-limit-message show-if-require-login">
        <th>&mdash; <?php _e( 'Require Login Message', 'wpuf-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[req_login_message]"><?php echo esc_textarea( $req_login_message ); ?></textarea>
        </td>
    </tr>
</table>
