<div class="wpuf-fields">

    <div :class="['wpuf-name-field-wrap', 'format-' + field.format]">
        <div class="wpuf-name-field-first-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.first_name.placeholder"
                :value="field.first_name.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label" v-if="!field.hide_subs">{{ field.first_name.sub }}</label>
        </div>

        <div class="wpuf-name-field-middle-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.middle_name.placeholder"
                :value="field.middle_name.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label" v-if="!field.hide_subs">{{ field.middle_name.sub }}</label>
        </div>

        <div class="wpuf-name-field-last-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.last_name.placeholder"
                :value="field.last_name.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label" v-if="!field.hide_subs">{{ field.last_name.sub }}</label>
        </div>
    </div>

    <span v-if="field.help" class="wpuf-help">{{ field.help }}</span>
</div>
