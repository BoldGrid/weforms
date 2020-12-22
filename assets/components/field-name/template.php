<div>
    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'First Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">

            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.first_name.placeholder">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'first_name', type: 'placeholder'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.first_name.default">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'first_name', type: 'default'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix" v-if="editing_form_field.format !== 'first-last'">

        <label>
            <?php _e( 'Middle Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">
            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.middle_name.placeholder">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'middle_name', type: 'placeholder'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">

            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.middle_name.default">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'middle_name', type: 'default'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'Last Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">

            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.last_name.placeholder">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'last_name', type: 'placeholder'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.last_name.default">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'last_name', type: 'default'}">
                </wpuf-merge-tags>
            </div>
            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>
</div>
