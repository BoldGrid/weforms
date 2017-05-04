<div class="wpuf-contact-form-entries">
    <h1 class="wp-heading-inline">
        <?php _e( 'Entries', 'best-contact-form' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
        <span style="color: #999;" class="form-name">{{ form_title }}</span>
    </h1>

    <router-link class="page-title-action" to="/">Back to forms</router-link>

    <wpuf-table action="wpuf_contact_form_entries" :id="id" v-on:ajaxsuccess="form_title = $event.form_title"></wpuf-table>

</div>