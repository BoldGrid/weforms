<h3 class="hndle"><?php esc_html_e( 'Human Presence', 'weforms' ); ?></h3>

<div class="inside">
    <p class="help">
        <?php esc_html_e( 'Human Presence takes a revolutionary approach to BOT detection and website security that utilizes "human-centered" learning algorithms to protect against suspicious activity.', 'weforms' ); ?>
    </p>
    <p class="help" v-if="!has_humanpresence_installed()">
        <?php esc_html_e( 'Ready to protect your forms? ', 'weforms' ); ?><a href="http://www.humanpresence.io/weforms-signup"><?php esc_html_e( 'Click here to receive a special discount on Human Presence for weForms', 'weforms' ); ?></a>
    </p>
    <div v-else>
	    <?php
	    /**
	     * @since 1.6.4
	     */
	    do_action( 'weforms_humanpresence_global_settings_form' );
	    ?>
	</div>
</div>