<div>
    <div class="wpuf-int-form-row">
        <div class="wpuf-int-field-label">
            <label for="mailchimp-list-id"><?php _e( 'Slack Webhook URL', 'weforms' ); ?></label>
        </div>
        <div class="wpuf-int-field">
            <input type="url" class="regular-text" v-model="settings.url" placeholder="https://hooks.slack.com/services/...">
            <p class="help"><?php printf( __( '%sSlack webhook URL%s to send our JSON payloads (%sView documentation%s)', 'weforms' ), '<a href="https://api.slack.com/incoming-webhooks" target="_blank" >', '</a>', '<a href="https://wedevs.com/docs/weforms/integrations/slack/" target="_blank" >', '</a>' ); ?></p>
        </div>
    </div>
</div>