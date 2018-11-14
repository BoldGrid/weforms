<?php

/**
 * weForms Shortcode Button class
 *
 * @since 1.2.9
 */
class Weforms_Form_Button {

    /**
     * Constructor for shortcode class
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'media_buttons', array( $this, 'add_media_button' ), 20 );
        add_action( 'admin_footer', array( $this, 'media_thickbox_content' ) );
    }


    /**
     * Enqueue scripts and styles for form builder
     *
     * @global string $pagenow
     * @return void
     */
    function enqueue_scripts() {
        global $pagenow;

        if ( !in_array( $pagenow, array( 'post.php', 'post-new.php') ) ) {
            return;
        }

        wp_enqueue_script( 'weforms-shortcode', WEFORMS_ASSET_URI . '/weforms-shortcode.js', array('jquery') );
    }

    /**
     * Adds a media button (for inserting a form) to the Post Editor
     *
     * @param  int  $editor_id The editor ID
     * @return void
     */
    function add_media_button( $editor_id ) {
        ?>
        <a href="#TB_inline?width=480&amp;inlineId=weforms-media-dialog" class="button thickbox insert-form" data-editor="<?php echo esc_attr( $editor_id ); ?>" title="<?php _e( 'Add a Form', 'weforms' ); ?>">
            <?php echo '<span class="wp-media-buttons-icon dashicons dashicons-welcome-widgets-menus"></span>' . __( ' Add Contact Form', 'weforms' ); ?>
        </a>
        <?php

    }

    /**
     * Prints the thickbox popup content
     *
     * @return void
     */
    public function media_thickbox_content() {
        global $pagenow;

        if ( !in_array( $pagenow, array( 'post.php', 'post-new.php') ) ) {
            return;
        }
        ?>

        <div id="weforms-media-dialog" style="display: none;">

            <div class="weforms-popup-container">
                <?php

                $all_forms = weforms()->form->all();
                $options = sprintf( "<option value='%s'>%s</option>", 0, __('&mdash; Select Form &mdash;', 'weforms') );
                foreach ( $all_forms['forms'] as $form ) {
                    $options.= sprintf( "<option value='%s'>%s</option>", $form->id, $form->name );
                }
                ?>

                <div class="weforms-form-div">
                    <label><h3><?php _e( 'Select a form to insert', 'weforms' ); ?></h3></label>
                    <select id="weforms-form-select">
                        <?php echo $options;  ?>
                    </select>
                </div>

                <div class="submit-button weforms-submit-div">
                    <button id="weforms-form-insert" class="button-primary"><?php _e( 'Insert Form', 'weforms' ); ?></button>
                    <button id="weforms-form-close" class="button-secondary" style="margin-left: 5px;" onClick="tb_remove();"><?php _e( 'Close', 'weforms' ); ?></a></button>
                </div>

            </div>
        </div>

        <style type="text/css">

            .weforms-form-div {
                padding: 10px;
                clear: left;
            }
            .weforms-submit-div {
                padding: 10px;
                clear: left;
                float: left;
                width: 90%;
            }
        </style>

        <?php
    }



    /**
     * * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Weforms_Form_Button();
        }

        return $instance;
    }

}

Weforms_Form_Button::init();
