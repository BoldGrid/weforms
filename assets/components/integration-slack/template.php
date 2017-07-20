<div>
    <div class="wpuf-int-form-row">
        <div class="wpuf-int-field-label">
            <label for="mailchimp-list-id"><?php _e( 'Message', 'weforms' ); ?></label>
        </div>
        <div class="wpuf-int-field">
            <div class="wpuf-int-field-small">
                <textarea class="regular-text" v-model="settings.message" id="" cols="30" rows="10"></textarea>
                <wpuf-merge-tags v-on:insert="insertValue" field="message"></wpuf-merge-tags>
            </div>
        </div>
    </div>

    <div class="wpuf-int-form-row">
        <div class="wpuf-int-field-label">
            <label for="mailchimp-list-id"><?php _e( 'Slack Webhook URL', 'weforms' ); ?></label>
        </div>
        <div class="wpuf-int-field">
            <input type="url" class="regular-text" v-model="settings.url" placeholder="https://hooks.slack.com/services/...">
            <p class="help"><?php _e( 'Slack webhook URL to send our JSON payloads', 'textdomain' ); ?></p>
        </div>
    </div>
</div>