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