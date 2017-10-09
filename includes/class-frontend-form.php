<?php

/**
 * The frontend form class
 */
class WeForms_Frontend_Form {

    public function __construct() {
        add_shortcode( 'weforms', array( $this, 'render_shortcode' ) );
    }

    /**
     * Show form error
     *
     * @param  string $message
     * @param  string $type
     *
     * @return string
     */
    public function show_error( $message, $type = 'info' ) {
        return sprintf( '<div class="wpuf-%s">%s</div>', $type, $message );
    }

    /**
     * Render contact form shortcode
     *
     * @param  array $atts
     * @param  string $contents
     *
     * @return string
     */
    public function render_shortcode( $atts, $contents = '' ) {
        extract( shortcode_atts( array( 'id' => 0 ), $atts ) );

        weforms()->scripts->enqueue_frontend();

        ob_start();

        $form = weforms()->form->get( $id );

        if ( ! $form->id ) {
            return $this->show_error( __( 'The form couldn\'t be found.', 'weforms' ) );
        }

        $is_open = $form->is_submission_open();

        if ( is_wp_error( $is_open ) ) {
            return $this->show_error( $is_open->get_error_message() );
        }

        $this->render_form( $form );

        return ob_get_clean();
    }

    /**
     * Render the form
     *
     * @param  \WeForms_Form $form
     *
     * @return void
     */
    function render_form( $form ) {
        $form_fields   = $form->get_fields();
        $form_settings = $form->get_settings();
        $show_credit   = weforms_get_settings( 'credit', false );

        if ( $form_settings['modal_form'] ) {
            /**
             * Enqueue scripts
             *
             * @param string $handle Script name
             * @param string $src Script url
             * @param array $deps (optional) Array of script names on which this script depends
             * @param string|bool $ver (optional) Script version (used for cache busting), set to null to disable
             * @param bool $in_footer (optional) Whether to enqueue the script before </head> or before </body>
             */
            wp_enqueue_script( 'weforms-modal-js', WEFORMS_ASSET_URI . '/modal/jquery.modal.js', array( 'jquery', 'wpuf-form' ), false, false );
            wp_enqueue_style( 'weforms_modal_styles', WEFORMS_ASSET_URI . '/modal/jquery.modal.css' );
            
            // add_filter( 'weforms_frontend_scripts', array( $this, 'weforms_modal_frontend_script' ) );
            // add_filter( 'weforms_admin_styles', array( $this, 'weforms_modal_frontend_style' ) );
        }
        ?>

        <script type="text/javascript">
            if ( typeof wpuf_conditional_items === 'undefined' ) {
                window.wpuf_conditional_items = [];
            }

            if ( typeof wpuf_plupload_items === 'undefined' ) {
                window.wpuf_plupload_items = [];
            }

            if ( typeof wpuf_map_items === 'undefined' ) {
                window.wpuf_map_items = [];
            }
        </script>

        <form class="wpuf-form-add <?php echo $form_settings['modal_form'] ? 'modal' : ''; ?>" action="" method="post" id="<?php echo $form_settings['modal_form'] ? 'modal-form' : ''; ?>" <?php echo $form_settings['modal_form'] ? 'style="display:none"' : ''; ?>>

            <ul class="wpuf-form form-label-<?php echo $form_settings['label_position']; ?>">

                <?php
                /**
                 * @since 1.1.0
                 */
                do_action( 'weforms_form_fields_top', $form, $form_fields );

                weforms()->fields->render_fields( $form_fields, $form->id );
                $this->submit_button( $form->id, $form_settings );

                /**
                 * @since 1.1.0
                 */
                do_action( 'weforms_form_fields_bottom', $form, $form_fields );
                ?>
            </ul>

        </form>

        <?php
        if ( $form_settings['modal_form'] ) {
            if ( 'link' == $form_settings['appearance'] ) {
                printf('<p><a href="#modal-form" rel="modal:open">%s</a></p>', $form_settings['modal_text'] );
            } else if ( 'button' == $form_settings['appearance'] ) {
                printf('<p><button><a href="#modal-form" rel="modal:open">%s</a></button></p>', $form_settings['modal_text'] );
            }
        }

        if ( $show_credit ) {
            printf( '<em>' . __( 'Powered by <a href="%s" target="_blank">weForms</a>', 'weforms' ) . '</em>', 'https://wordpress.org/plugins/weforms/' );
        }

        weforms_track_form_view( $form->id );
    }

    /**
     * Render submit button
     *
     * @param  integer $form_id
     * @param  array   $form_settings
     *
     * @return void
     */
    function submit_button( $form_id, $form_settings ) {
        ?>
        <li class="wpuf-submit">
            <div class="wpuf-label">
                &nbsp;
            </div>

            <?php wp_nonce_field( 'wpuf_form_add' ); ?>

            <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
            <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
            <input type="hidden" name="action" value="weforms_frontend_submit">

            <input type="submit" class="weforms_submit_btn" name="submit" value="<?php echo $form_settings['submit_text']; ?>" />
        </li>
    <?php
    }

    /**
     * Get all modal scripts
     * 
     * @param  array   $scripts
     *
     * @return array
     */
    public function weforms_modal_frontend_script( $scripts ) {
        $scripts['weforms-modal-js'] = array(
            'src'       => WEFORMS_ASSET_URI . '/modal/jquery.modal.js',
            'deps'      => array( 'wpuf-form', 'jquery' ),
            'in_footer' => false
        );
        return $scripts;
    }

    /**
     * Get all modal style
     * 
     * @param  array   $style
     *
     * @return array
     */
    public function weforms_modal_frontend_style( $style ) {
        $style['weforms-modal-css'] = array(
            'src'  => WEFORMS_ASSET_URI . '/modal/jquery.modal.css',
        );
        return $style;
    }
}