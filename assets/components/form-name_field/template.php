<div class="wpuf-fields">

    <!-- <pre>{{ field }}</pre> -->

    <div :class="['wpuf-name-field-wrap', 'format-' + field.format]">
        <div class="wpuf-name-field-first-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.placeholder"
                :value="field.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label">First</label>
        </div>

        <div class="wpuf-name-field-middle-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.placeholder"
                :value="field.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label">Middle</label>
        </div>

        <div class="wpuf-name-field-last-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.placeholder"
                :value="field.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label">Last</label>
        </div>
    </div>

    <span v-if="field.help" class="wpuf-help">{{ field.help }}</span>
</div>
