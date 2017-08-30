<div class="weforms-settings">
    <h1><?php _e( 'Settings', 'weforms' ); ?></h1>

    <div class="postboxes metabox-holder two-col">
        <div class="postbox">
            <h3 class="hndle"><?php _e( 'General Settings', 'weforms' ); ?></h3>

            <div class="inside">
                <p class="help">
                    <?php _e( 'For better email deliverability choose a email provider that will ensure the email reaches your inbox, as well as reducing your server load.', 'weforms' ); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Send Email Via', 'weforms' ); ?></th>
                        <td>
                            <select v-model="settings.email_gateway">
                                <option value="wordpress"><?php _e( 'WordPress', 'weforms' ); ?></option>
                                <option value="sendgrid" :disabled="is_pro ? false : true"><?php _e( 'SendGrid', 'weforms' ); ?> <span v-if="!is_pro">(<?php _e( 'Premium', 'weforms' ); ?>)</span></option>
                                <option value="mailgun" :disabled="is_pro ? false : true"><?php _e( 'Mailgun', 'weforms' ); ?> <span v-if="!is_pro">(<?php _e( 'Premium', 'weforms' ); ?>)</span></option>
                                <option value="sparkpost" :disabled="is_pro ? false : true"><?php _e( 'SparkPost', 'weforms' ); ?> <span v-if="!is_pro">(<?php _e( 'Premium', 'weforms' ); ?>)</span></option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'sendgrid'">
                        <th><?php _e( 'SendGrid API Key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.sendgrid" class="regular-text">

                            <p class="description"><?php printf( __( 'Fill your SendGrid <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.sendgrid.com/settings/api_keys' ); ?></p>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'mailgun'">
                        <th><?php _e( 'Domain Name', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.mailgun_domain" class="regular-text">

                            <p class="description"><?php _e( 'Your Mailgun domain name', 'weforms' ); ?></p>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'mailgun'">
                        <th><?php _e( 'API Key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.mailgun" class="regular-text">

                            <p class="description"><?php printf( __( 'Fill your Mailgun <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.mailgun.com/app/account/security' ); ?></p>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'sparkpost'">
                        <th><?php _e( 'SparkPost API Key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.sparkpost" class="regular-text">

                            <p class="description"><?php printf( __( 'Fill your SparkPost <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.sparkpost.com/account/credentials' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Show Credit', 'weforms' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" v-model="settings.credit">
                                <?php _e( 'Show <em>powered by weForms</em> credit in form footer.', 'weforms' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="submit-wrapper">
                <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php _e( 'Save Changes', 'weforms' ); ?></button>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><?php _e( 'reCaptcha', 'weforms' ); ?></h3>

            <div class="inside">
                <p class="help">
                    <?php printf( __( '<a href="%s" target="_blank">reCAPTCHA</a> is a free anti-spam service from Google which helps to protect your website from spam and abuse. Get <a href="%s" target="_blank">your API Keys</a>.', 'weforms' ), 'https://www.google.com/recaptcha/intro/', 'https://www.google.com/recaptcha/admin#list' ); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Site key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.recaptcha.key" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Secret key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.recaptcha.secret" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="submit-wrapper">
                <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php _e( 'Save Changes', 'weforms' ); ?></button>
            </div>
        </div>

        <?php do_action( 'weforms_settings' ); ?>
    </div>
</div>