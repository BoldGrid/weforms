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

        $this->class_map = apply_filters( 'weforms_rest_api_class_map', array(
            WEFORMS_INCLUDES . '/api/class-weforms-entries-controller.php'     => 'Weforms_Entry_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-forms-controller.php'       => 'Weforms_Forms_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-settings-controller.php'    => 'Weforms_Setting_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-tools-controller.php'       => 'Weforms_Tools_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-uploads-controller.php'     => 'Weforms_Upload_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-log-controller.php'         => 'Weforms_Log_Controller',
            WEFORMS_INCLUDES . '/api/class-weforms-modules-controller.php'     => 'Weforms_Modules_Controller',
        ) );

         add_action( 'rest_api_init', array( $this, 'register_routes' ) );
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

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function register_trello_rest_api_integration( $class_map ) {
        $class_map[dirname( __FILE__ ) . '/api/weforms_rest_api_class_map'] = 'Weforms_Trello_Controller';

        return $class_map;
    }
}

