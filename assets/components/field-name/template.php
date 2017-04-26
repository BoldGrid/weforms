<div>
    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'First Name', 'wpuf-contact-form' ); ?>
        </label>

        <div class="name-field-placeholder">
            <input type="text" v-model="editing_form_field.first_name.placeholder">
            <label><?php _e( 'Placeholder', 'wpuf-contact-form' ); ?></label>
        </div>

        <div class="name-field-value">
            <input type="text" v-model="editing_form_field.first_name.default">
            <label><?php _e( 'Default Value', 'wpuf-contact-form' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix" v-if="editing_form_field.format !== 'first-last'">

        <label>
            <?php _e( 'Middle Name', 'wpuf-contact-form' ); ?>
        </label>

        <div class="name-field-placeholder">
            <input type="text" v-model="editing_form_field.middle_name.placeholder">
            <label><?php _e( 'Placeholder', 'wpuf-contact-form' ); ?></label>
        </div>

        <div class="name-field-value">
            <input type="text" v-model="editing_form_field.middle_name.default">
            <label><?php _e( 'Default Value', 'wpuf-contact-form' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'Last Name', 'wpuf-contact-form' ); ?>
        </label>

        <div class="name-field-placeholder">
            <input type="text" v-model="editing_form_field.last_name.placeholder">
            <label><?php _e( 'Placeholder', 'wpuf-contact-form' ); ?></label>
        </div>

        <div class="name-field-value">
            <input type="text" v-model="editing_form_field.last_name.default">
            <label><?php _e( 'Default Value', 'wpuf-contact-form' ); ?></label>
        </div>
    </div>
</div>