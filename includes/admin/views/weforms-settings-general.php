<h3 class="hndle"><?php esc_html_e( 'General Settings', 'weforms' ); ?></h3>

<div class="inside">
    <p class="help">
        <?php esc_html_e( 'For better email deliverability choose a email provider that will ensure the email reaches your inbox, as well as reducing your server load.', 'weforms' ); ?>
    </p>

    <table class="form-table">
        <tr>
            <th><?php esc_html_e( 'Send Email Via', 'weforms' ); ?></th>
            <td>
                <select v-model="settings.email_gateway">
                    <option value="wordpress"><?php esc_html_e( 'WordPress', 'weforms' ); ?></option>
                    <option value="sendgrid" :disabled="is_pro ? false : true"><?php esc_html_e( 'SendGrid', 'weforms' ); ?> <span v-if="!is_pro">(<?php esc_html_e( 'Premium', 'weforms' ); ?>)</span></option>
                    <option value="mailgun" :disabled="is_pro ? false : true"><?php esc_html_e( 'Mailgun', 'weforms' ); ?> <span v-if="!is_pro">(<?php esc_html_e( 'Premium', 'weforms' ); ?>)</span></option>
                    <option value="sparkpost" :disabled="is_pro ? false : true"><?php esc_html_e( 'SparkPost', 'weforms' ); ?> <span v-if="!is_pro">(<?php esc_html_e( 'Premium', 'weforms' ); ?>)</span></option>
                </select>
            </td>
        </tr>
        <template v-if="is_pro">
            <tr v-if="settings.email_gateway == 'sendgrid'">
                <th><?php esc_html_e( 'SendGrid API Key', 'weforms' ); ?></th>
                <td>
                    <input type="text" v-model="settings.gateways.sendgrid" class="regular-text">

                    <p class="description"><?php printf( wp_kses_post( __( 'Fill your SendGrid <a href="%s" target="_blank">API Key</a>.', 'weforms') ), 'https://app.sendgrid.com/settings/api_keys' ); ?></p>
                </td>
            </tr>
            <tr v-if="settings.email_gateway == 'mailgun'">
                <th><?php esc_html_e( 'Domain Name', 'weforms' ); ?></th>
                <td>
                    <input type="text" v-model="settings.gateways.mailgun_domain" class="regular-text">

                    <p class="description"><?php esc_html_e( 'Your Mailgun domain name', 'weforms' ); ?></p>
                </td>
            </tr>
            <tr v-if="settings.email_gateway == 'mailgun'">
                <th><?php esc_html_e( 'API Key', 'weforms' ); ?></th>
                <td>
                    <input type="text" v-model="settings.gateways.mailgun" class="regular-text">

                    <p class="description"><?php printf( wp_kses_post( __( 'Fill your Mailgun <a href="%s" target="_blank">API Key</a>.', 'weforms' ) ), 'https://app.mailgun.com/app/account/security' ); ?></p>
                </td>
            </tr>
            <tr v-if="settings.email_gateway == 'sparkpost'">
                <th><?php esc_html_e( 'SparkPost API Key', 'weforms' ); ?></th>
                <td>
                    <input type="text" v-model="settings.gateways.sparkpost" class="regular-text">

                    <p class="description"><?php printf( wp_kses_post( 'Fill your SparkPost <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.sparkpost.com/account/credentials' ); ?></p>
                </td>
            </tr>
        </template>
        <tr>
            <th><?php esc_html_e( 'Show Credit', 'weforms' ); ?></th>
            <td>
                <label>
                    <input type="checkbox" v-model="settings.credit">
                    <?php wp_kses_post( 'Show <em>powered by weForms</em> credit in form footer.', 'weforms' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Enable No-Conflict Mode', 'weforms' ); ?></th>
            <td>
                <label>
                    <input type="checkbox" v-model="settings.no_conflict">
                    <?php esc_html_e( 'Minimize conflict with other plugins in the backend.', 'weforms' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Show Credit in Email Footer', 'weforms' ); ?></th>
            <td>
                <label>
                    <input :disabled="!is_pro" type="checkbox" v-model="settings.email_footer">
                    <?php esc_html_e( 'Show credit text in email footer.', 'weforms' ); ?>
                </label>
                <p v-if="!is_pro" class="description"><?php esc_html_e( 'Available in PRO version.', 'weforms' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Form Permission', 'weforms' ); ?></th>
            <td>
                <select :disabled="!is_pro" v-model="settings.permission">
                    <option value="manage_options"><?php esc_html_e( 'Admins Only', 'weforms' ); ?></option>
                    <option value="edit_others_posts"><?php esc_html_e( 'Admins, Editors', 'weforms' ); ?></option>
                    <option value="publish_posts"><?php esc_html_e( 'Admins, Editors, Authors', 'weforms' ); ?></option>
                    <option value="edit_posts"><?php esc_html_e( 'Admins, Editors, Authors, Contributors', 'weforms' ); ?></option>
                </select>

                <p v-if="!is_pro" class="description"><?php esc_html_e( 'Available in PRO version.', 'weforms' ); ?></p>
                <p v-else class="description"><?php esc_html_e( 'Which user roles can access and create forms, manage form submissions.', 'weforms' ); ?></p>
            </td>
        </tr>
    </table>
</div>

<div class="submit-wrapper">
    <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php esc_html_e( 'Save Changes', 'weforms' ); ?></button>
</div>
