<table class="form-table">

    <tr class="wpuf-schedule-entries">
        <th><?php _e( 'Schedule form', 'best-contact-form' ); ?></th>
        <td>
            <label>
                <input type="checkbox" v-model="settings.schedule_form" :true-value="'true'" :false-value="'false'">
                <?php _e( 'Schedule form for a period', 'best-contact-form' ); ?>
            </label>

            <p class="description">
                <?php _e( 'Schedule for a time period the form is active.', 'best-contact-form' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-schedule-period" v-show="settings.schedule_form == 'true'">
        <th>&mdash; <?php _e( 'Schedule Period', 'best-contact-form' ); ?></th>
        <td>

            <?php _e( 'From', 'best-contact-form' ); ?>
            <datepicker v-model="settings.schedule_start"></datepicker>

            <?php _e( 'To', 'best-contact-form' ); ?>
            <datepicker v-model="settings.schedule_end"></datepicker>
        </td>
    </tr>

    <tr class="wpuf-schedule-pending" v-show="settings.schedule_form == 'true'">
        <th>&mdash; <?php _e( 'Form Pending Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.sc_pending_message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-schedule-expired" v-show="settings.schedule_form == 'true'">
        <th>&mdash; <?php _e( 'Form Expired Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.sc_expired_message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-require-login">
        <th><?php _e( 'Require Login', 'best-contact-form' ); ?></th>
        <td>
            <label>
                <input type="checkbox" v-model="settings.require_login" :true-value="'true'" :false-value="'false'">
                <?php _e( 'Require user to be logged in', 'best-contact-form' ); ?>
            </label>
        </td>
    </tr>

    <tr class="wpuf-limit-message" v-show="settings.require_login == 'true'">
        <th>&mdash; <?php _e( 'Require Login Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.req_login_message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-limit-entries">
        <th><?php _e( 'Limit Entries', 'best-contact-form' ); ?></th>
        <td>
            <label>
                <input type="checkbox" v-model="settings.limit_entries" :true-value="'true'" :false-value="'false'">
                <?php _e( 'Enable form entry limit', 'best-contact-form' ); ?>
            </label>

            <p class="description">
                <?php _e( 'Limit the number of entries allowed for this form', 'best-contact-form' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-number-entries" v-show="settings.limit_entries == 'true'">
        <th>&mdash; <?php _e( 'Number of Entries', 'best-contact-form' ); ?></th>
        <td>
            <input type="number" value="" v-model="settings.limit_number">
        </td>
    </tr>

    <tr class="wpuf-limit-message" v-show="settings.limit_entries == 'true'">
        <th>&mdash; <?php _e( 'Limit Reached Message', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.limit_message"></textarea>
        </td>
    </tr>


</table>
