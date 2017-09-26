<div class="wefroms-form-templates">
    <wpuf-modal :show.sync="show" :onClose="onClose">
        <h2 slot="header">
            <?php _e( 'Select a Template', 'weforms' ); ?>
            <small><?php printf( __( 'Select from a pre-defined template or from a <a href="#" %s>blank form</a>', 'weforms' ), '@click.prevent="blankForm()"' ); ?></small>

            <span class="choose-form-categroy">
                <?php _e( 'Select Category', 'weforms' ); ?> &nbsp;
                <select v-model="category">
                    <option value="all">All</option>
                    <?php 
                        $registry   = weforms_get_form_templates();
                        $categories = weforms_get_form_template_categories();
                        $colors     = weforms_get_flat_ui_colors();


                        foreach ( $categories as $key => $category ) {
                            printf( '<option value="%s">%s</option>', $key, $category['name'] );
                        }
                     ?>
                </select>
            </span>
        </h2>

        <div slot="body">
            
                <?php

                foreach ($categories as $category_id => $category) {

                    ?>
                    <div class="clearfix" v-if="category=='<?php echo $category_id; ?>' || category=='all'">
                
                    <?php 
             
                    printf( '<h2><i class="%s" style="color: %s"></i> &nbsp;  %s</h2> <ul class="clearfix">',$category['icon'], $colors[array_rand($colors)], $category['name'] );

                    if ( $category_id == 'default' ) {
        
                        ?>

                            <li class="blank-form">    
                                <h3><?php _e( 'Blank Form', 'weforms' ); ?></h3>
                                
                                <div class="blank-form-text">
                                    <span class="dashicons dashicons-plus"></span>
                                    <div class="title"><?php _e( 'Blank Form', 'weforms' ); ?></div>
                                </div>

                                <div class="form-create-overlay">

                                    <div class="title"><?php _e( 'Blank Form', 'weforms' ); ?></div>
                                    <br>
                                    <button class="button button-primary" @click.prevent="blankForm($event.target)" title="<?php echo esc_attr('Blank Form'); ?>">
                                        <?php _e('Create Form', 'weforms' );  ?>
                                    </button>
                                </div>
                            </li>

                        <?php  
                    }

                    foreach ( $registry as $key => $template ) {

                        if ( $category_id !== $template->category ) {
                            continue;
                        }

                        $class = 'template-active';
                        $title = $template->title;
                        $image = $template->image ? $template->image : '';

                        if ( ! $template->is_enabled() ) {
                            $class = 'template-inactive';
                            $title = __( 'This integration is not installed.', 'weforms' );
                        }

                        ?>

                        <li>    
                            <h3><?php _e( $title, 'weforms' ); ?></h3>
                            
                            <?php  if ( $image ) { printf( '<img src="%s" alt="%s">', $image, $title );   }  ?>

                            <div class="form-create-overlay">

                                <div class="title"><?php echo $template->get_title(); ?></div>
                                <div class="description"><?php echo $template->get_description(); ?></div>
                                <br>
                                <button class="button button-primary" @click.prevent="createForm('<?php echo $key; ?>', $event.target)" title="<?php echo esc_attr( $title ); ?>">
                                    <?php _e('Create Form', 'weforms' );  ?>
                                </button>
                            </div>
                        </li>
                        <?php
                    }


                    ?>

                    </ul></div>
                    
                    <?php 
                }
                ?>
        </div>
    </wpuf-modal>
</div>