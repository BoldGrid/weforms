<?php

/**
 * The frontend form class
 */
class WeForms_Frontend_Form {

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
        $form_status   = get_post_status( $form_id );

        if ( ! $form_status ) {
            echo '<div class="wpuf-message">' . __( 'Your selected form is no longer available.', 'weforms' ) . '</div>';
            return;
        }

        if ( $form_status != 'publish' ) {
            echo '<div class="wpuf-message">' . __( "Please make sure you've published your form.", 'weforms' ) . '</div>';
            return;
        }

        $form          = weforms()->form->get( $form_id );

        $form_fields   = $form->get_fields();
        $form_settings = $form->get_settings();
        $show_credit   = weforms_get_settings( 'credit', false );

        // var_dump( $form_fields );
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
                // var_dump( $form_fields );
                weforms()->fields->render_fields( $form_fields, $form_id );
                // $this->render_items( $form_vars, $post_id, 'contact_form', $form_id, $form_settings );
                $this->submit_button( $form_id, $form_settings, $post_id );
                ?>
            </ul>

        </form>

        <?php
        if ( $show_credit ) {
            printf( '<em>' . __( 'Powered by <a href="%s" target="_blank">weForms</a>', 'weforms' ) . '</em>', 'https://wordpress.org/plugins/weforms/' );
        }

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

    function field_total( $form_field, $post_id, $type, $form_id ) {
        ?>
        <div class="wpuf-fields total <?php  echo ' wpuf_'.$form_field['name'].'_'.$form_id; ?>">
            $ <input
                type="number"
                class="input-total <?php echo 'wpuf_'.$form_field['name']. '_'. $form_id; ?>"
                id="form-total"
                name="form-total"
                value=""
                disabled="disabled"
                style="width:50px;" />
        </div>

        <script>
            ;(function($){
                $totalHtml = $('.wpuf-fields').find('input#form-total');
                $totalVal = $totalHtml.val();
                $total = $totalVal;
                $('.quantity').on('keyup', function() {
                    $singlePriceHtml = $('.wpuf-fields').find('span.input-price');
                    $singlePriceVal = $singlePriceHtml.text();
                    $singlePrice = $singlePriceVal;
                    $final = $total + $singlePrice;
                    $totalHtml.val($final);
                });
                $('.multiple-product').change(function() {
                    $price = $(this).val();
                    $totalHtml = $('.wpuf-fields').find('input#form-total');
                    $totalVal = $totalHtml.val();
                    $total = $totalVal;
                    $final = $total;
                    if(this.checked) {
                        $final = $total*1 + $price*1;
                    } else {
                        $final = $total*1 - $price*1;
                    }
                    $totalHtml.val($final);
                });
            })(jQuery);
        </script>

        <?php

        $this->conditional_logic( $form_field, $form_id );
    }

    function field_single_product( $form_field, $post_id, $type, $form_id ) {
        ?>
        <div class="wpuf-fields" data-required="<?php echo $form_field['required'] ?>" data-type="radio">
            <div>
                <label><?php echo __( 'Price: $', 'wpuf-pro' ) ?><span class="input-price"><?php echo $form_field['price']; ?></span></label>
            </div>
            <div>
                <label><?php echo __( 'Quantity: ', 'wpuf-pro' ) ?></label>
                <input
                    type="number"
                    class="<?php echo 'wpuf_'.$form_field['name']. '_'. $form_id; ?> quantity"
                    name="qty"
                    value=0
                    min=0
                    max="<?php echo $form_field['size']; ?>"
                    style="width:50px;" />
            </div>

            <?php $this->help_text( $form_field ); ?>

        </div>

        <script>
            ;(function($){
                $priceHtml = $('.wpuf-fields').find('span.input-price');
                $priceVal = $priceHtml.text();
                $price = $priceVal;
                $('.quantity').on('keyup', function() {
                    var self = $(this),
                        val = self.val();
                    $priceHtml.html( $price * val );
                });
            })(jQuery);
        </script>

        <?php

        $this->conditional_logic( $form_field, $form_id );
    }

    function field_multiple_product( $form_field, $post_id, $type, $form_id ) {
        $selected = isset( $form_field['selected'] ) ? $form_field['selected'] : array();

        if ( $post_id ) {
            if ( $value = $this->get_meta( $post_id, $form_field['name'], $type, true ) ) {
                $selected = explode( self::$separator, $value );
            }
        }
        ?>

        <div class="wpuf-fields" data-required="<?php echo $form_field['required'] ?>">

            <?php
            if ( $form_field['options'] && count( $form_field['options'] ) > 0 ) {
                foreach ($form_field['options'] as $value => $option) {
                    ?>
                    <label <?php echo $form_field['inline'] == 'yes' ? 'class="wpuf-checkbox-inline"' : 'class="wpuf-checkbox-block"'; ?>>
                        <input type="checkbox" class="multiple-product <?php echo 'wpuf_'.$form_field['name']. '_'. $form_id; ?>" name="<?php echo $form_field['name']; ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php echo in_array( $value, $selected ) ? ' checked="checked"' : ''; ?> />
                        <?php echo $option ?> - $<span class="price"><?php echo $value; ?></span>
                    </label>
                    <?php
                }
            }
            ?>

            <?php $this->help_text( $form_field ); ?>

        </div>

        <?php

        $this->conditional_logic( $form_field, $form_id );
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
