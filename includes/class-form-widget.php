<?php

/**
 * Class weForms_Widget
 *
 * Widget class for weForms Form widget
 *
 * @since 1.2.7
 */
class weForms_Widget extends WP_Widget {

    /**
     * weForms_Widget constructor.
     */
    public function __construct() {
        parent::__construct(
            'weforms_widget',
            __( 'weForms Widget', 'weforms' ),
            [ 'description' => __( 'Add weForms Form to Sidebar', 'weforms' )]
          );
    }

    /**
     * Render widget
     *
     * @return void
     */
    public function widget( $args, $instance ) {
        $title            = apply_filters( 'widget_title', $instance['title'] );
        $form_id          = apply_filters( 'weforms_widget_form_id', $instance['form_id'] );

        echo wp_kses_post( $args['before_widget'] );

        if ( ! empty( $title ) ) {
            echo wp_kses_post( $args['before_title'] ) . wp_kses_post( $title ) . wp_kses_post( $args['after_title'] );
        }

        echo do_shortcode( sprintf( '[weforms id ="%s" ]', $form_id  ) );

        echo wp_kses_post( $args['after_widget'] );
    }

    /**
     * weForms widget backend
     *
     * @return void
     */
    public function form( $instance ) {
        $title   = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Form', 'weforms' );
        $form_id = isset( $instance[ 'form_id' ] ) ? $instance[ 'form_id' ] : 0;

        $all_forms = weforms()->form->all();
        $options   = sprintf( "<option value='%s'>%s</option>", 0, __( 'Select Form', 'weforms' ) );

        foreach ( $all_forms['forms'] as $form ) {
            $options .= sprintf( "<option value='%s' %s >%s</option>", $form->id, selected( $form_id, $form->id, false ), $form->name );
        } ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'weforms' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo wp_kses_post( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'form_id' ) ); ?>"> <?php esc_html_e( 'Form:', 'weforms' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'form_id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'form_id' ) ); ?>">
                <?php echo $options; ?> // WPCS: sanitization ok.
            </select>
        </p>

        <?php
    }

    /**
     * Updating widget replacing old instances with new
     *
     * @return $instance
     */
    public function update( $new_instance, $old_instance ) {
        $instance            = [];
        $instance['title']   = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['form_id'] = ( !empty( $new_instance['form_id'] ) ) ? strip_tags( $new_instance['form_id'] ) : '';

        return $instance;
    }
}
/**
 * Register weForms widget
 *
 * @return void
 */
function weforms_register_widget() {
    register_widget( 'weforms_widget' );
}
add_action( 'widgets_init', 'weforms_register_widget' );
