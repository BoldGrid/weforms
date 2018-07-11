<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_support_content_before
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_support_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-wpuf-dashboard">
		<?php

            $form_id = dokan_get_option( 'vendor_contact_form', 'weforms_integration' );

            if ( !empty( $form_id ) ) {
                echo do_shortcode('[weforms id="'.$form_id.'"]');
            } else{
                _e( 'No contact form assigned yet by marketplace owner.', 'weforms' );
            }

		?>
    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_support_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_support_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->