<div class="wpuf-contact-form-transactions">
    <h1 class="wp-heading-inline">
        <?php _e( 'Transactions', 'weforms' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
           <span style="color: #999;" class="form-name">
            {{ form_title }}
        </span>

        <select v-if="Object.keys(forms).length " v-model="selected">
            <option :value="form.id" v-for="form in forms">{{ form.name }}</option>
        </select>
    </h1>

    <p v-if="no_transactions">
       <?php printf( 
            __( 'You don\'t have any transactions yet. Learn how to %sset up payment integration%s and take payments with weFroms.' ),
            '<a target="_blank" href="https://wedevs.com/docs/weforms/integrations/payment/">',
            '</a>'
              );
        ?>
    </p>

    <wpuf-table v-if="selected"
        has_export="no"
        action="weforms_form_payments"
        delete="weforms_form_payments_trash_bulk"
        :id="selected"
        v-on:ajaxsuccess="form_title = $event.form_title; $route.params.id = selected"
    >
    </wpuf-table>

</div>
