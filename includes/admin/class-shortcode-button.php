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
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'media_buttons', [ $this, 'add_media_button' ], 20 );
        add_action( 'admin_footer', [ $this, 'media_thickbox_content' ] );
    }

    /**
     * Enqueue scripts and styles for form builder
     *
     * @global string $pagenow
     *
     * @return void
     */
    public function enqueue_scripts() {
        global $pagenow;

        if ( !in_array( $pagenow, [ 'post.php', 'post-new.php'] ) ) {
            return;
        }

        wp_enqueue_script( 'weforms-shortcode', WEFORMS_ASSET_URI . '/js/weforms-shortcode.js', ['jquery'] );
    }

    /**
     * Adds a media button (for inserting a form) to the Post Editor
     *
     * @param int $editor_id The editor ID
     *
     * @return void
     */
    public function add_media_button( $editor_id ) {
        ?>
        <a href="#TB_inline?width=480&amp;inlineId=weforms-media-dialog" class="button thickbox insert-form" data-editor="<?php echo esc_attr( $editor_id ); ?>" title="<?php esc_html_e( 'Add a Form', 'weforms' ); ?>">
            <?php echo '<span class="wp-media-buttons-icon dashicons dashicons-welcome-widgets-menus"></span>' . esc_html_e( ' Add Contact Form', 'weforms' ); ?>
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

        if ( !in_array( $pagenow, [ 'post.php', 'post-new.php'] ) ) {
            return;
        } ?>

        <div id="weforms-media-dialog" style="display: none;">

            <div class="weforms-popup-container">
                <?php

                $all_forms = weforms()->form->all();
        $options           = sprintf( "<option value='%s'>%s</option>", 0, __( '&mdash; Select Form &mdash;', 'weforms' ) );

        foreach ( $all_forms['forms'] as $form ) {
            $options .= sprintf( "<option value='%s'>%s</option>", esc_attr( $form->id ), esc_attr( $form->name ) );
        } ?>

                <div class="weforms-form-div">
                    <label><h3><?php esc_html_e( 'Select a form to insert', 'weforms' ); ?></h3></label>
                    <select id="weforms-form-select">
                        <?php echo wp_kses( $options, array( 'option' => array( 'value' => array() ) ) ); ?>
                    </select>
                </div>

                <div class="submit-button weforms-submit-div">
                    <button id="weforms-form-insert" class="button-primary"><?php esc_html_e( 'Insert Form', 'weforms' ); ?></button>
                    <button id="weforms-form-close" class="button-secondary" style="margin-left: 5px;" onClick="tb_remove();"><?php esc_html_e( 'Close', 'weforms' ); ?></a></button>
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
