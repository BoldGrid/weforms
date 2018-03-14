<div>

    <div class="panel-field-opt panel-field-opt-text">
        <label>
            <?php _e( 'Dynamic value population', 'weforms' ); ?>
            <help-text text="<?php _e( "Field value or options can be populated dynamically through filter hook or query string", 'weforms' ) ?>"></help-text>
        </label>

        <ul>
            <li>
                <label><input type="checkbox" value="yes" v-model="dynamic.status"> <?php _e( 'Allow field to be populated dynamically', 'weforms' ); ?></label>
            </li>
        </ul>
    </div>


	<template v-if="dynamic.status">

		<div class="panel-field-opt panel-field-opt-text"><label>
            <?php _e( 'Parameter Name', 'weforms' ); ?>
	        <help-text text="<?php _e( "Enter a Parameter Name, using that the field value can be populated through filter hook or query string", 'weforms' ) ?>"></help-text>
	         <input type="text" v-model="dynamic.param_name">
	     	</label>
     	</div>
	</template>

</div>
