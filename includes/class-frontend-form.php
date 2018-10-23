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

        $this->render_form( $form, $atts );

        return ob_get_clean();
    }

    /**
     * Render the form
     *
     * @param  \WeForms_Form $form
     *
     * @return void
     */
    function render_form( $form, $atts ) {
        $form_fields      = $form->get_fields();
        $form_settings    = $form->get_settings();
        $show_credit      = weforms_get_settings( 'credit', false );
        $formid           = 'weforms-' . $form->id;
        $use_theme_css    = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';

        if ( isset( $atts['modal'] ) && 'true' == $atts['modal'] ) {

            wp_enqueue_script( 'weforms-modal-js', WEFORMS_ASSET_URI . '/modal/jquery.modal.js', array( 'jquery', 'weforms-form' ), false, false );
            wp_enqueue_style( 'weforms_modal_styles', WEFORMS_ASSET_URI . '/modal/jquery.modal.css' );

            $modal_class = 'modal';
            $modal_id = 'modal-form';
            $modal_style = 'style="display:none"';

        } else {
            $modal_class = $modal_id = $modal_style = '';
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

        <form class="wpuf-form-add <?php echo $formid ?> <?php echo $modal_class; ?> <?php echo $use_theme_css; ?>" action="" method="post"  <?php echo $modal_style; ?> id="<?php echo $modal_id; ?>">

            <ul class="wpuf-form form-label-<?php echo $form_settings['label_position']; ?>">

                <?php
                /**
                 * @since 1.1.0
                 */
                do_action( 'weforms_form_fields_top', $form, $form_fields );

                weforms()->fields->render_fields( $form_fields, $form->id, $atts );

                /**
                 * @since 1.1.1
                 */
                do_action( 'weforms_form_fields_before_submit_button', $form, $form_fields, $form_settings );

                $this->submit_button( $form->id, $form_settings );

                /**
                 * @since 1.1.0
                 */
                do_action( 'weforms_form_fields_bottom', $form, $form_fields );
                ?>
            </ul>

        </form>

        <?php
        if ( isset( $atts['modal'] ) && 'true' == $atts['modal'] ) {
            if ( isset( $atts['link'] ) ) {
                printf('<p><a href="#modal-form" rel="modal:open">%s</a></p>', $atts['link'] );
            } else {
                if ( isset( $atts['button'] ) ) {
                    $button_text = $atts['button'];
                } else {
                    $button_text = __( 'Open Form', 'weforms' );
                }
                printf('<p><button><a href="#modal-form" rel="modal:open">%s</a></button></p>', $button_text );
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

            <?php
            if ( isset( $form_settings['show_frontend_report'] ) && $form_settings['show_frontend_report'] ) { ?>
                <input type="hidden" name="weforms-front-report" value="yes">
            <?php } else { ?>
                <input type="hidden" name="weforms-front-report" value="no">
            <?php } ?>

            <?php do_action( 'weforms_submit_btn', $form_id, $form_settings ); ?>

            <input type="submit" class="weforms_submit_btn" name="submit" value="<?php echo $form_settings['submit_text']; ?>" />

        </li>
    <?php
    }
}
