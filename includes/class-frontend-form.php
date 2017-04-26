<?php

/**
 * The frontend form class
 */
class WPUF_Contact_Form_Frontend extends WPUF_Render_Form {

    public function __construct() {
        add_shortcode( 'wpuf_contact_form', array( $this, 'render_shortcode' ) );
    }

    public function render_shortcode( $atts, $contents = '' ) {
        extract( shortcode_atts( array( 'id' => 0 ), $atts ) );
        ob_start();

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
        wpuf_cf_track_form_view( $form_id );
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

        ?>
        <div class="wpuf-fields">
            <div class="wpuf-name-field-wrap format-<?php echo $form_field['format']; ?>">
                <div class="wpuf-name-field-first-name">
                    <input
                        name="<?php echo $form_field['name'] ?>[first]"
                        type="text" class="textfield"
                        placeholder=""
                        value=""
                        size="40"
                    >
                    <label class="wpuf-form-sub-label">First</label>
                </div>

                <div class="wpuf-name-field-middle-name">
                    <input
                        name="<?php echo $form_field['name'] ?>[middle]"
                        type="text" class="textfield"
                        placeholder=""
                        value=""
                        size="40"
                    >
                    <label class="wpuf-form-sub-label">Middle</label>
                </div>

                <div class="wpuf-name-field-last-name">
                    <input
                        name="<?php echo $form_field['name'] ?>[last]"
                        type="text" class="textfield"
                        placeholder=""
                        value=""
                        size="40"
                    >
                    <label class="wpuf-form-sub-label">Last</label>
                </div>
            </div>
        </div>
        <?php
    }
}
