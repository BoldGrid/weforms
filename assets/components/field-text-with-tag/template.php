<div v-if="met_dependencies" class="panel-field-opt panel-field-opt-text field-text-with-tag">
    <label>
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>

        <input
            v-if="option_field.variation && 'number' === option_field.variation"
            type="number"
            v-model="value"
            @focusout="on_focusout"
            @keyup="on_keyup"
        >

        <input
            v-if="!option_field.variation"
            type="text"
            v-model="value"
            @focusout="on_focusout"
            @keyup="on_keyup"
        >

       <wpuf-merge-tags :filter="option_field.tag_filter" v-on:insert="insertValue"></wpuf-merge-tags>
    </label>
</div>
