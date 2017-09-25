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

        <form class="wpuf-form-add" action="" method="post">

            <ul class="wpuf-form form-label-<?php echo $form_settings['label_position']; ?>">
                <?php
                weforms()->fields->render_fields( $form_fields, $form->id );
                $this->submit_button( $form->id, $form_settings );
                ?>
            </ul>

        </form>

        <?php
        if ( $show_credit ) {
            printf( '<em>' . __( 'Powered by <a href="%s" target="_blank">weForms</a>', 'weforms' ) . '</em>', 'https://wordpress.org/plugins/weforms/' );
        }

        weforms_track_form_view( $form->id );
    }

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

            <input type="submit" name="submit" value="<?php echo $form_settings['submit_text']; ?>" />
        </li>
    <?php
    }
}