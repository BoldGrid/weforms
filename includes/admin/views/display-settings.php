<?php
global $post;

$form_settings  = wpuf_get_form_settings( $post->ID );

$label_position = isset( $form_settings['label_position'] ) ? $form_settings['label_position'] : 'above';

?>
<table class="form-table">

    <tr class="wpuf-label-position">
        <th><?php _e( 'Label Position', 'best-contact-form' ); ?></th>
        <td>
            <select name="wpuf_settings[label_position]">
                <?php
                $positions = array(
                    'above'  => __( 'Above Element', 'best-contact-form' ),
                    'left'   => __( 'Left of Element', 'best-contact-form' ),
                    'right'  => __( 'Right of Element', 'best-contact-form' ),
                    'hidden' => __( 'Hidden', 'best-contact-form' ),
                );

                foreach ($positions as $to => $label) {
                    printf('<option value="%s"%s>%s</option>', $to, selected( $label_position, $to, false ), $label );
                }
                ?>
            </select>

            <p class="description">
                <?php _e( 'Where the labels of the form should display', 'best-contact-form' ) ?>
            </p>
        </td>
    </tr>
</table>
