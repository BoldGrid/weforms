<div class="wpuf-contact-form-payments">
    <h1 class="wp-heading-inline">
        <?php _e( 'Transactions', 'weforms' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
        <span style="color: #999;" class="form-name">{{ form_title }}</span>
    </h1>

    <router-link class="page-title-action" to="/"><?php _e( 'Back to forms', 'weforms' ); ?></router-link>

    <wpuf-table
    	action="weforms_form_payments"
    	delete="weforms_form_payments_trash_bulk"
    	:id="id"
    	v-on:ajaxsuccess="form_title = $event.form_title"
    >
    </wpuf-table>

</div>