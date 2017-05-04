<?php
global $post;

$form_settings      = wpuf_get_form_settings( $post->ID );

$limit_entries      = isset( $form_settings['limit_entries'] ) ? $form_settings['limit_entries'] : '';
$limit_number       = isset( $form_settings['limit_number'] ) ? $form_settings['limit_number'] : '';
$limit_message      = isset( $form_settings['limit_message'] ) ? $form_settings['limit_message'] : '';

$require_login      = isset( $form_settings['require_login'] ) ? $form_settings['require_login'] : '';
$req_login_message  = isset( $form_settings['req_login_message'] ) ? $form_settings['req_login_message'] : '';

$schedule_form      = isset( $form_settings['schedule_form'] ) ? $form_settings['schedule_form'] : '';
$sc_pending_message = isset( $form_settings['sc_pending_message'] ) ? $form_settings['sc_pending_message'] : '';
$sc_expired_message = isset( $form_settings['sc_expired_message'] ) ? $form_settings['sc_expired_message'] : '';
$schedule_start     = isset( $form_settings['schedule_start'] ) ? $form_settings['schedule_start'] : '';
$schedule_end       = isset( $form_settings['schedule_end'] ) ? $form_settings['schedule_end'] : '';

?>
<table class="form-table">

    <tr class="wpuf-schedule-entries">
        <th><?php _e( 'Schedule form', 'best-contact-form' ); ?></th>
        <td>
            <label>
                <input type="hidden" name="wpuf_settings[schedule_form]" value="false">
                <input type="checkbox" name="wpuf_settings[schedule_form]" value="true"<?php checked( $schedule_form, 'true' ); ?>>
                <?php _e( 'Schedule form for a period', 'best-contact-form' ); ?>
            </label>

            <p class="description">
                <?php _e( 'Schedule for a time period the form is active.', 'best-contact-form' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-schedule-period show-if-scheduled">
        <th>&mdash; <?php _e( 'Schedule Period', 'best-contact-form' ); ?></th>
        <td>

            From
            <input type="text" class="wpuf-input-datetime" name="wpuf_settings[schedule_start]" value="<?php echo $schedule_start; ?>">

            To
            <input type="text" class="wpuf-input-datetime" name="wpuf_settings[schedule_end]" value="<?php echo $schedule_end; ?>">
        </td>
    </tr>

    <tr class="wpuf-schedule-pending show-if-scheduled">
        <th>&mdash; <?php _e( 'Form Pending Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[sc_pending_message]"><?php echo esc_textarea( $sc_pending_message ); ?></textarea>
        </td>
    </tr>

    <tr class="wpuf-schedule-expired show-if-scheduled">
        <th>&mdash; <?php _e( 'Form Expired Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[sc_expired_message]"><?php echo esc_textarea( $sc_expired_message ); ?></textarea>
        </td>
    </tr>

    <tr class="wpuf-require-login">
        <th><?php _e( 'Require Login', 'best-contact-form' ); ?></th>
        <td>
            <label>
                <input type="hidden" name="wpuf_settings[require_login]" value="false">
                <input type="checkbox" name="wpuf_settings[require_login]" value="true"<?php checked( $require_login, 'true' ); ?>>
                <?php _e( 'Require user to be logged in', 'best-contact-form' ); ?>
            </label>
        </td>
    </tr>

    <tr class="wpuf-limit-message show-if-require-login">
        <th>&mdash; <?php _e( 'Require Login Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[req_login_message]"><?php echo esc_textarea( $req_login_message ); ?></textarea>
        </td>
    </tr>

    <tr class="wpuf-limit-entries">
        <th><?php _e( 'Limit Entries', 'best-contact-form' ); ?></th>
        <td>
            <label>
                <input type="hidden" name="wpuf_settings[limit_entries]" value="false">
                <input type="checkbox" name="wpuf_settings[limit_entries]" value="true"<?php checked( $limit_entries, 'true' ); ?>>
                <?php _e( 'Enable form entry limit', 'best-contact-form' ); ?>
            </label>

            <p class="description">
                <?php _e( 'Limit the number of entries allowed for this form', 'best-contact-form' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-number-entries show-if-limit-entry">
        <th>&mdash; <?php _e( 'Number of Entries', 'best-contact-form' ); ?></th>
        <td>
            <input type="number" name="wpuf_settings[limit_number]" value="<?php echo $limit_number; ?>">
        </td>
    </tr>

    <tr class="wpuf-limit-message show-if-limit-entry">
        <th>&mdash; <?php _e( 'Limit Reached Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[limit_message]"><?php echo esc_textarea( $limit_message ); ?></textarea>
        </td>
    </tr>


</table>
