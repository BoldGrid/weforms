<table class="form-table">

    <tr class="wpuf-schedule-entries">
        <th><?php esc_html_e( 'Schedule form', 'weforms' ); ?></th>
        <td>
            <label class="weforms-switch">
                <input type="checkbox" v-model="settings.schedule_form" :true-value="'true'" :false-value="'false'">
                <span class="switch-slider round"></span>
                <?php esc_html_e( 'Schedule form for a period', 'weforms' ); ?>
            </label>

            <p class="description">
                <?php esc_html_e( 'Schedule for a time period the form is active.', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-schedule-period" v-show="settings.schedule_form == 'true'">
        <th>&mdash; <?php esc_html_e( 'Schedule Period', 'weforms' ); ?></th>
        <td>

            <?php esc_html_e( 'From', 'weforms' ); ?>
            <datepicker v-model="settings.schedule_start"></datepicker>

            <?php esc_html_e( 'To', 'weforms' ); ?>
            <datepicker v-model="settings.schedule_end"></datepicker>
        </td>
    </tr>

    <tr class="wpuf-schedule-pending" v-show="settings.schedule_form == 'true'">
        <th>&mdash; <?php esc_html_e( 'Form Pending Message', 'weforms' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.sc_pending_message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-schedule-expired" v-show="settings.schedule_form == 'true'">
        <th>&mdash; <?php esc_html_e( 'Form Expired Message', 'weforms' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.sc_expired_message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-require-login">
        <th><?php esc_html_e( 'Require Login', 'weforms' ); ?></th>
        <td>
            <label class="weforms-switch">
                <input type="checkbox" v-model="settings.require_login" :true-value="'true'" :false-value="'false'">
                <span class="switch-slider round"></span>
                <?php esc_html_e( 'Require user to be logged in', 'weforms' ); ?>
            </label>
        </td>
    </tr>

    <tr class="wpuf-limit-message" v-show="settings.require_login == 'true'">
        <th>&mdash; <?php esc_html_e( 'Require Login Message', 'weforms' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.req_login_message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-limit-entries">
        <th><?php esc_html_e( 'Limit Entries', 'weforms' ); ?></th>
        <td>
            <label class="weforms-switch">
                <input type="checkbox" v-model="settings.limit_entries" :true-value="'true'" :false-value="'false'">
                <span class="switch-slider round"></span>
                <?php esc_html_e( 'Enable form entry limit', 'weforms' ); ?>
            </label>

            <p class="description">
                <?php esc_html_e( 'Limit the number of entries allowed for this form', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-number-entries" v-show="settings.limit_entries == 'true'">
        <th>&mdash; <?php esc_html_e( 'Number of Entries', 'weforms' ); ?></th>
        <td>
            <input type="number" value="" v-model="settings.limit_number">
        </td>
    </tr>

    <tr class="wpuf-limit-message" v-show="settings.limit_entries == 'true'">
        <th>&mdash; <?php esc_html_e( 'Limit Reached Message', 'weforms' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.limit_message"></textarea>
        </td>
    </tr>


</table>
