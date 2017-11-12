<div>

	<div class="panel-field-opt panel-field-opt-text">
		<label><input type="checkbox" value="yes" v-model="dynamic.status"> Allow field to be populated dynamically</label>
	</div>

	<template v-if="dynamic.status">

		<div class="panel-field-opt panel-field-opt-text"><label>
	        Parameter Name
	        <help-text text="<?php _e( "Enter a Parameter Name, using that the field value can be populated through filter hook or query string", 'weforms' ) ?>"></help-text>
	         <input type="text" v-model="dynamic.param_name">
	     	</label>
     	</div>
	</template>

</div>
