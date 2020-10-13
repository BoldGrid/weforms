<?php

/**
 * API manager class
 *
 * @since 1.4.2
 */
class WeForms_Api extends WP_REST_Controller {

    /**
     * Class dir and class name mapping
     *
     * @var array
     */
    protected $class_map;

    public function __construct() {
        $this->class_map = apply_filters( 'weforms_rest_api_class_map', [
            WEFORMS_INCLUDES . '/api/class-weforms-entries-controller.php'           => 'Weforms_Entry_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-forms-controller.php'             => 'Weforms_Forms_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-settings-controller.php'          => 'Weforms_Setting_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-uploads-controller.php'           => 'Weforms_Upload_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-log-controller.php'               => 'Weforms_Log_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-form-fields-controller.php'       => 'Weforms_Form_Fields_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-form-integration-controller.php'  => 'Weforms_Form_Integration_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-form-notification-controller.php' => 'Weforms_Form_Notification_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-form-settings-controller.php'     => 'Weforms_Form_Setting_Controller',
        ] );

        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Register all routes releated with Weforms
     *
     * @return void
     */
    public function register_routes() {
        foreach ( $this->class_map as $file_name => $controller ) {
            require_once $file_name;
            $controller = new $controller();
            $controller->register_routes();
        }
    }
}
