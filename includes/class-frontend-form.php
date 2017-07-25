<?php

/**
 * The frontend form class
 */
class WeForms_Frontend extends WPUF_Render_Form {

    public function __construct() {
        add_shortcode( 'weforms', array( $this, 'render_shortcode' ) );
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
        ob_start();

        $is_open = wpuf_is_form_submission_open( $id );

        if ( is_wp_error( $is_open ) ) {
            return '<div class="wpuf-info">' . $is_open->get_error_message() . '</div>';
        }

        $form_settings = wpuf_get_form_settings( $id );

        // var_dump( $form_settings );
        $this->render_form( $id );

        return ob_get_clean();
    }

    /**
     * Handles the add post shortcode
     *
     * @param $atts
     */
    function render_form( $form_id, $post_id = NULL, $preview = false ) {
        $form_vars     = wpuf_get_form_fields( $form_id );
        $form_settings = wpuf_get_form_settings( $form_id );
        ?>

        <form class="wpuf-form-add" action="" method="post">

            <ul class="wpuf-form form-label-<?php echo $form_settings['label_position']; ?>">
                <?php
                $this->render_items( $form_vars, $post_id, 'contact_form', $form_id, $form_settings );
                $this->submit_button( $form_id, $form_settings, $post_id );
                ?>
            </ul>

        </form>

        <?php
        weforms_track_form_view( $form_id );
    }

    function submit_button( $form_id, $form_settings, $post_id ) {
        ?>
        <li class="wpuf-submit">
            <div class="wpuf-label">
                &nbsp;
            </div>

            <?php wp_nonce_field( 'wpuf_form_add' ); ?>

            <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
            <input type="hidden" name="page_id" value="<?php echo get_post() ? get_the_ID() : '0'; ?>">
            <input type="hidden" name="action" value="wpuf_submit_contact">

            <input type="submit" name="submit" value="<?php echo $form_settings['submit_text']; ?>" />
        </li>
    <?php
    }

    function field_name( $form_field, $post_id, $type, $form_id ) {
        // var_dump( $form_field );
        ?>
        <div class="wpuf-fields">
            <div class="wpuf-name-field-wrap format-<?php echo $form_field['format']; ?>">
                <div class="wpuf-name-field-first-name">
                    <input
                        name="<?php echo $form_field['name'] ?>[first]"
                        type="text"
                        placeholder="<?php echo esc_attr( $form_field['first_name']['placeholder'] ); ?>"
                        value="<?php echo esc_attr( $form_field['first_name']['default'] ); ?>"
                        size="40"
                        data-required="<?php echo $form_field['required'] ?>"
                        data-type="text"
                        class="textfield wpuf_<?php echo $form_field['name']; ?>_<?php echo $form_id; ?>"
                    >

                    <?php if ( ! $form_field['hide_subs'] ) : ?>
                        <label class="wpuf-form-sub-label"><?php _e( 'First', 'weforms' ); ?></label>
                    <?php endif; ?>
                </div>

                <?php if ( $form_field['format'] != 'first-last' ) : ?>
                    <div class="wpuf-name-field-middle-name">
                        <input
                            name="<?php echo $form_field['name'] ?>[middle]"
                            type="text" class="textfield"
                            placeholder="<?php echo esc_attr( $form_field['middle_name']['placeholder'] ); ?>"
                            value="<?php echo esc_attr( $form_field['middle_name']['default'] ); ?>"
                            size="40"
                        >

                        <?php if ( ! $form_field['hide_subs'] ) : ?>
                            <label class="wpuf-form-sub-label"><?php _e( 'Middle', 'weforms' ); ?></label>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="<?php echo $form_field['name'] ?>[middle]" value="">
                <?php endif; ?>

                <div class="wpuf-name-field-last-name">
                    <input
                        name="<?php echo $form_field['name'] ?>[last]"
                        type="text" class="textfield"
                        placeholder="<?php echo esc_attr( $form_field['last_name']['placeholder'] ); ?>"
                        value="<?php echo esc_attr( $form_field['last_name']['default'] ); ?>"
                        size="40"
                    >
                    <?php if ( ! $form_field['hide_subs'] ) : ?>
                        <label class="wpuf-form-sub-label"><?php _e( 'Last', 'weforms' ); ?></label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php

        $this->conditional_logic( $form_field, $form_id );
    }
}
