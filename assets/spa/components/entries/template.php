<div class="wpuf-contact-form-entries">
    <h1 class="wp-heading-inline">
        <?php _e( 'Entries', 'weforms' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>

        <span style="color: #999;" class="form-name">
            {{ form_title }}
        </span>

        <select v-if="forms" v-model="selected">
            <option :value="form.id" v-for="form in forms">{{ form.name }}</option>
        </select>
    </h1>

    <template v-if="selected">

        <wpuf-table
            action="weforms_form_entries"
            :id="selected"
            v-on:ajaxsuccess="form_title = $event.form_title; $route.params.id = selected"
        >
        </wpuf-table>
    </template>

</div>