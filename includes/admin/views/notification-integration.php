<div id="wpuf-form-builder-tab-notification" class="tab-content" v-show="isActiveTab('notification')">

    <wpuf-cf-form-notification></wpuf-cf-form-notification>

</div>

<div id="wpuf-form-builder-tab-integration" class="tab-content" v-show="isActiveTab('integration')">

    <wpuf-integration></wpuf-integration>

</div>

<?php do_action( 'after_notification_intregration_tab_content' ); ?>