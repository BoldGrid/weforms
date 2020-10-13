<h3 class="hndle"><?php esc_html_e( 'reCaptcha', 'weforms' ); ?></h3>

    <div class="inside">
        <p class="help">
            <?php printf( wp_kses_post( __( '<a href="%s" target="_blank">reCAPTCHA</a> is a free anti-spam service from Google which helps to protect your website from spam and abuse. Get <a href="%s" target="_blank">your API Keys</a>.', 'weforms' ) ), 'https://www.google.com/recaptcha/intro/', 'https://www.google.com/recaptcha/admin#list' ); ?>
        </p>

        <table class="form-table">
            <tr>
                <th> <?php esc_html_e( 'reCaptcha Version', 'weforms' );  ?> </th>
                <td>
                     <input type="radio" v-model="settings.recaptcha.type" name="v2_recaptcha" value="v2"  class="regular-text">
                     <label for="recaptchav2"> <?php esc_html_e( 'v2','weforms'); ?> </label>
                     <input type="radio" v-model="settings.recaptcha.type" name="v2_recaptcha" value="v3" class="regular-text">
                     <label for="recaptchav3"> <?php esc_html_e( 'v3','weforms'); ?> </label>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Site key', 'weforms' ); ?></th>
                <td>
                    <input type="text" v-model="settings.recaptcha.key" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Secret key', 'weforms' ); ?></th>
                <td>
                    <input type="text" v-model="settings.recaptcha.secret" class="regular-text">
                </td>
            </tr>
        </table>
    </div>

<div class="submit-wrapper">
    <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php esc_html_e( 'Save Changes', 'weforms' ); ?></button>
</div>
