<?php

/**
 * Form field manager class
 *
 * @since 1.4.2
 */

class Weforms_Forms_Controller extends WP_REST_Controller {

    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'weforms/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $rest_base = 'forms';

    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route(
            $this->namespace,
            '/'. $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args' => array(
                        'per_page' => array(
                            'required'    => false,
                            'type'        => 'integer',
                            'description' => __( 'Post Per Page', 'weforms' ),
                            'sanitize_callback' => 'absint',
                            'default'     => 10,
                        ),

                        'page' => array(
                            'required'    => false,
                            'type'        => 'integer',
                            'description' => __( 'Paged', 'weforms' ),
                            'sanitize_callback' => 'absint',
                            'default'     => 1,
                        ),

                        'status' => array(
                            'default' => 'publish',
                            'type'    => 'array',
                            'items'   => array(
                                'type' => 'string',
                                'enum' => array( 'publish', 'draft', 'pending' )
                            ),
                        ),

                        'order' => array(
                            'required'    => false,
                            'description' => __( 'Order sort attribute ascending or descending.', 'weforms' ),
                            'type'        => 'string',
                            'sanitize_callback' => 'sanitize_text_field',
                            'default'     => 'DESC',
                            'enum'        => array( 'ASC', 'DESC' ),
                        ),

                        'orderby' => array(
                            'required'    => false,
                            'description' => __( 'Sort collection by object attribute.', 'weforms' ),
                            'type'        => 'string',
                            'sanitize_callback' => 'sanitize_text_field',
                            'default'     => 'post_date',
                            'enum'        => array(
                                'author',
                                'post_date',
                                'id',
                                'title',
                            ),
                        ),

                        'search' => array(
                            'required' => false,
                            'description' => __( '', 'weforms'),
                            'type'        => 'string',
                            'sanitize_callback' => 'sanitize_text_field',
                        )
                    )
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_item' ),
                    'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    'args' => array(
                        'template' => array(
                            'required'          => false,
                            'type'              => 'string',
                            'description'       => 'template name',
                            'sanitize_callback' => 'sanitize_text_field',
                            'validate_callback' => array( $this, 'is_template_exists' )
                        ),
                        'name' => array(
                            'description'       => __( '', 'weforms' ),
                            'type'              => 'string',
                            'sanitize_callback' => 'sanitize_text_field',
                            'required'          => false,
                        ),
                        "settings" => array(
                            'description'       => __( '', 'weforms' ),
                            'type'              => 'object',
                            'required'          => false,
                        ),
                        "notifications" => array(
                            'description' => __( '', 'weforms' ),
                            'type'        => 'object',
                            'required'    => false,
                        ),
                        "fields" => array(
                            'description' => __( '', 'weforms' ),
                            'type'        => 'object',
                            'required'    => false,
                        ),
                        "integrations" => array(
                            'description' => __( '', 'weforms' ),
                            'type'        => 'object',
                            'required'    => false,
                        ),
                    )
                ),

                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'bulk_delete_form' ),
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                    'args' => array(
                        'ids' => array(
                            'description' => __( 'Unique identifier for the object', 'weforms' ),
                            'type'    => 'array',
                            'items'   => array(
                                'type' => 'integer'
                            ),
                            'validate_callback' => array( $this, 'is_bulk_delete_form_exists' ),
                            'required'          => true,
                        ),
                    ),
                )
            )
        );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>\d+)', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),

            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_item' ),
                'permission_callback' => array( $this, 'delete_item_permissions_check' ),
            ),

        ) );


        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' .'/entries/', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists_field_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'save_entry' ),
                'permission_callback' => array( $this, 'submit_permissions_check' ),
            ),

        ) );


        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/entries/', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                'per_page' => array(
                    'required'          => false,
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'description'       => __( 'Post Per Page', 'weforms' ),
                    'default'           => 10,
                ),

                'page' => array(
                    'required'          => false,
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'description'       => __( 'Paged', 'weforms' ),
                    'default'           => 1,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_entry' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                'name' => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'required'          => true,
                ),

                "settings_key" => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'required'          => false,
                ),

                "settings" => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'object',
                    'required'          => false,
                ),

                "notifications" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'required'    => true,
                ),

                "fields" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'required'    => true,
                ),

                "integrations" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'required'    => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),

            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/settings', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                "settings_key" => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'required'          => true,
                ),

                "settings" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'required'    => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item_settings' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/settings', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item_settings' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/fields', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                "fields" => array(
                    'description' => __( '', 'weforms' ),
                    'key'         => 'object',
                    'required'    => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item_fields' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),

            ),

        ) );


        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/fields', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'key'               => 'integer',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item_fields' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),

            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/fields', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                "name" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type' => 'string'
                    ),
                    'required'    => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_item_fields' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/fields', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'key'               => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                "name" => array(
                    'description' => __( '', 'weforms' ),
                    'type'    => 'array',
                    'items'   => array(
                        'type' => 'string'
                    ),
                    'required' => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_item_fields' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/integrations', array(
            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'key'               => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item_integrations' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/integrations', array(

            'args' => array(

                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'key'               => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                "integrations" => array(
                    'description'       => __( '', 'weforms' ),
                    'key'               => 'object',
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item_integrations' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),

            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/notifications', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item_notification' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),

            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/notifications', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'add_item_notification' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/notifications', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
                "notifications" => array(
                    'description'       => __( '', 'weforms' ),
                    'key'               => 'object',
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item_notification' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/notifications', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),

                'index' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'array',
                    'items'   => array(
                        'type' => 'integer',
                    ),
                    // 'sanitize_callback' => 'absint',
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_item_notification' ),
                'permission_callback' => array( $this, 'delete_item_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/bulkdelete/', array(

            'args' => array(
                'ids' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'array',
                    'validate_callback' => array( $this, 'is_bulk_delete_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'bulk_delete_form' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' .'/duplicate/', array(

            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'key'               => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'duplicate_form' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' .'/export_entries/', array(
            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'export_form_entries' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
            ),
        ) );


        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' .'/entries/(?P<entry_id>[\d]+)',
            array(
            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
                'entry_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_entry_exists' ),
                    'required'          => true,
                )
            ),

            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_entry_details' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
        ) );


        register_rest_route( $this->namespace, '/' . $this->rest_base . '/import', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'import_forms' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/export',array(

            'args' => array(
                'ids' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'array',
                    'items'             => array(
                        'id' => array(
                            'type'              => 'integer',
                            'sanitize_callback' => function($param, $request, $key) {
                                error_log(print_r($param,true));
                                return is_numeric( $param );
                            }
                        ),
                    ),
                    'validate_callback' => array( $this, 'is_bulk_delete_form_exists' ),
                    'required'          => true,
                ),
            ),

            array (
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'export_forms' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            )
        ) );

    }

    /**
     * Check Template Exist
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     **/
    public function is_template_exists( $param, $request, $key ) {
        $templates = weforms()->templates->get_templates();
        return (bool) array_key_exists( $param,$templates );
    }

    /**
     * Export Form Entries
     *

*
     **/
    public function export_form_entries( $request ) {
        $form_id = isset( $request['form_id'] ) ? absint( $request['form_id'] ) : 0;

        if ( ! $form_id ) {
            return new WP_Error( 'rest_invalid_form',__( 'Form id not exists', 'weforms' ) );
        }

        $entry_array   = [];
        $columns       = weforms_get_entry_columns( $form_id, false );
        $total_entries = weforms_count_form_entries( $form_id );
        $entries       = weforms_get_form_entries( $form_id, array(
            'number'       => $total_entries,
            'offset'       => 0
        ) );
        $extra_columns =  array(
            'ip_address' => __( 'IP Address', 'weforms' ),
            'created_at' => __( 'Date', 'weforms' )
        );

        $columns = array_merge( array( 'id' => 'Entry ID' ), $columns, $extra_columns );

        foreach ($entries as $entry) {
            $temp = array();

            foreach ($columns as $column_id => $label) {
                switch ( $column_id ) {
                    case 'id':
                        $temp[ $column_id ] = $entry->id;
                        break;

                    case 'ip_address':
                        $temp[ $column_id ] = $entry->ip_address;
                        break;

                    case 'created_at':
                        $temp[ $column_id ] = $entry->created_at;
                        break;

                    default:
                        $value              = weforms_get_entry_meta( $entry->id, $column_id, true );
                        $value              = weforms_get_pain_text( $value );
                        $temp[ $column_id ] = str_replace( WeForms::$field_separator, ' ', $value );
                        break;
                }
            }

            $entry_array[] = $temp;
        }

        error_reporting(0);

        if ( ob_get_contents() ) {
            ob_clean();
        }

        $blogname  = sanitize_title( strtolower( str_replace( " ", "-", get_option( 'blogname' ) ) ) );
        $file_name = $blogname . "-weforms-entries-" . time() . '.csv';

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$file_name}");
        header("Content-Transfer-Encoding: binary");

        $handle = fopen("php://output", 'w');

        //handle UTF-8 chars conversion for CSV
        fprintf( $handle, chr(0xEF).chr(0xBB).chr(0xBF) );

        // put the column headers
        fputcsv( $handle, array_values( $columns ) );

        // put the entry values
        foreach ( $entry_array as $row ) {
            fputcsv( $handle, $row );
        }

        fclose( $handle );

        exit;
    }



    /**
     * Export forms to JSON
     *
     * @param WP_REST_Request $request
     *
     * @return json
     **/
    public function export_forms( $request ) {
        $export_type = isset( $request['type'] ) ? $request['type'] : 'all';
        $selected    = isset( $request['ids'] ) ? array_map( 'absint', $request['ids'] ) : array();

        if ( ! class_exists( 'WeForms_Admin_Tools' ) ) {
            require_once WEFORMS_INCLUDES . '/admin/class-admin-tools.php';
        }

        switch ( $export_type ) {
            case 'all':
                WeForms_Admin_Tools::export_to_json();
                break;
            case 'selected':
                WeForms_Admin_Tools::export_to_json( $selected );
                break;
        }
    }

    /**
     * Impot Forms
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function import_forms( $request ) {
        $files   = $request->get_file_params();

        if ( ! $files ) {
            return new WP_Error( 'rest_invalid_file',__( 'No file found to import.', 'weforms' ), array( 'status' => 404) );
        }

        $file_ext  = pathinfo( $files['file']['name'], PATHINFO_EXTENSION );

        if ( ! class_exists( 'WeForms_Admin_Tools' ) ) {
            require_once WEFORMS_INCLUDES . '/admin/class-admin-tools.php';
        }

        if ( ( $file_ext == 'json' ) && ( $files['file']['size'] < 500000 ) ) {
            $status = WeForms_Admin_Tools::import_json_file( $files['file']['tmp_name'] );

            if ( $status ) {
                $response_data             = array();
                $response_data ['message'] = __( 'The forms have been imported successfully!', 'weforms' );
                $response_data ['status']  = 200;

                $response = $this->prepare_response_for_collection( $response_data );
                $response = rest_ensure_response( $response );

                return $response;
            } else {
                return new WP_Error( 'rest_invalid_file',__( 'Something went wrong importing the file.', 'weforms' ), array( 'status' => 404 ) );
            }
        } else {
            return new WP_Error( 'rest_invalid_file',__( 'Invalid file or file size too big.', 'weforms' ), array( 'status' => 404 ) );
        }
    }

    /**
     * Get Form Entries
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     *
    */
    public function get_entry( $request ) {
        $form_id      = isset( $request['form_id'] ) ? intval( $request['form_id'] ) : 0;
        $current_page = isset( $request['page'] ) ? intval( $request['page'] ) : 1;
        $per_page     = isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 10;
        $status       = isset( $request['status'] ) ? $request['status'] : 'publish';
        $offset       = ( $current_page - 1 ) * $per_page;

        if ( ! $form_id ) {
             return new WP_Error( 'rest_invalid_data', __( 'Please provide a form id', 'weforms'), array( 'status' => 404 ) );
        }

        $entries = weforms_get_form_entries(
            $form_id, array(
                'number' => $per_page,
                'offset' => $offset,
                'status' => $status,
            )
        );

        $columns       = weforms_get_entry_columns( $form_id );
        $total_entries = weforms_count_form_entries( $form_id, $status );

        array_map(
            function( $entry ) use ( $columns ) {
                    $entry_id = $entry->id;
                    $entry->fields = array();

                foreach ( $columns as $meta_key => $label ) {
                    $value                    = weforms_get_entry_meta( $entry_id, $meta_key, true );
                    $entry->fields[ $meta_key ] = str_replace( WeForms::$field_separator, ' ', $value );
                }
            }, $entries
        );

        $entries = apply_filters( 'weforms_get_entries', $entries, $form_id );

        $response = array(
            'form_title'        => get_post_field( 'post_title', $form_id ),
            'form_entries'      => $entries,
        );

        $max_pages= ceil( $total_entries / $per_page );
        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        $response->header( 'X-WP-Total', (int) $total_entries );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        return $response;
    }

    /**
     * Bulk Delete Form
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function bulk_delete_form( $request ) {
        $form_ids = $request['ids'];

        if ( empty( $form_ids  ) ) {
            return new WP_Error( 'error', __( 'No form ids provided!', 'weforms') , array( 'status' => 404 ) );
        }

        $response = array();

        foreach ( $form_ids as $form_id ) {
            $status =  $this->delete( $form_id );

            if( is_wp_error( $status ) ) {
                $response[$form_id] = $status->get_error_message();

            } else {
                $response[$form_id] = __( ' form  deleted successfully ', 'weforms' );
            }
        }

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Updates a single form
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function update_item( $request ) {
        $post_title        = $request->get_param('name');
        $wpuf_settings     = $request->get_param('wpuf_settings');
        $page_id           = $request->get_param('page_id');
        $form_settings_key = $request->get_param('settings_key');
        $wpuf_form_id      = $request->get_param('form_id');
        $page              = $request->get_param('page');
        $settings          = $request->get_param('settings');
        $notifications     = $request->get_param('notifications');
        $form_fields       = $request->get_param('fields');
        $integrations      = $request->get_param('integrations');

        $data = array(
            'form_id'           => $wpuf_form_id,
            'post_title'        => $post_title,
            'form_fields'       => $form_fields,
            'form_settings'     => $settings,
            'form_settings_key' => $form_settings_key,
            'notifications'     => $notifications,
            'integrations'      => $integrations
        );

        // $form_fields = weforms()->form->save( $data );
        $form_fields = $this->weforms_api_save( $data );

        $form = weforms()->form->get( $wpuf_form_id );

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations()
        );

        $request->set_param( 'context', 'edit' );
        $response = $this->prepare_item_for_response( $response_data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * [update_item_settingss description]
     * @param  [type] $request [description]
     * @return [type]          [description]
    */
    public function update_item_settings( $request ) {
        $wpuf_form_id      = $request->get_param('form_id');
        $settings          = $request->get_param('settings');
        $form_settings_key = $request->get_param('settings_key');

        $data = array(
            'form_id'           => $wpuf_form_id,
            'form_settings'     => $settings,
            'form_settings_key' => $form_settings_key,
        );

        $form              = weforms()->form->get( $wpuf_form_id );
        $new_form_settings = array_merge( $data['form_settings'], array_diff_key( $form->get_settings(), $data['form_settings'] ) );

        update_post_meta( $data['form_id'], $data['form_settings_key'], $new_form_settings );

        $form = weforms()->form->get( $wpuf_form_id );

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'settings'      => $form->get_settings(),
        );

        $request->set_param( 'context', 'edit' );
        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * [update_item_settingss description]
     * @param  [type] $request [description]
     * @return [type]          [description]
    */
    public function update_item_fields( $request ) {
        $wpuf_form_id      = $request->get_param('form_id');
        $form_fields       = $request->get_param('fields');

        $data = array(
            'form_id'           => $wpuf_form_id,
            'form_fields'       => $form_fields,
        );

        $existing_wpuf_input_ids = get_children( array(
            'post_parent' => $data['form_id'],
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'ids'
        ) );

        $new_wpuf_input_ids = array();

        if ( ! empty( $data['form_fields'] ) ) {

            foreach ( $data['form_fields'] as $order => $field ) {
                if ( ! empty( $field['is_new'] ) ) {
                    unset( $field['is_new'] );
                    unset( $field['id'] );

                    $field_id = 0;

                } else {
                    $field_id = $field['id'];
                }

                $field_id = weforms_insert_form_field( $data['form_id'], $field, $field_id, $order );

                $new_wpuf_input_ids[] = $field_id;

                $field['id'] = $field_id;

                $saved_wpuf_inputs[] = $field;
            }
        }

        $form = weforms()->form->get( $wpuf_form_id );

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
        );

        $request->set_param( 'context', 'edit' );
        $response = $this->prepare_item_for_response( $response_data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    public function get_item_fields( $request ) {
        $form_id = $request->get_param('form_id');
        $form    = weforms()->form->get( $form_id );

        $data = array(
            'id'            => $form->data->ID,
            'name'            => $form->name,
            'fields'        => $form->get_fields(),
        );

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['fields'] )  );

        return $response;
    }

    public function delete_item_fields( $request ) {
        $form_id          = $request->get_param('form_id');
        $fields_to_delete = $request->get_param('name');

        if ( empty( $fields_to_delete )  ) {
            return new WP_Error( 'error', __( 'Fields name not provided', 'weforms') , array( 'status' => 404 ) );
        }

        $fields = get_children( array(
            'post_parent' => $form_id,
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'All'
        ) );

        $deleted_fields = array();

        foreach ( $fields as $field ) {
            $field_data = maybe_unserialize( $field->post_content );

            if ( in_array( $field_data['name'], $fields_to_delete ) ) {
                $field_id          = $field->ID;
                $deleted_fields[]  = wp_delete_post( $field_id , true );
            }
        }

        if ( empty( $deleted_fields )  ) {
            return new WP_Error( 'error', __( 'Fields not exist or deleted before.', 'weforms') , array( 'status' => 404 ) );
        }

        $form_array['message'] = __( 'Fields  deleted successfully ', 'weforms' );
        $response              = $this->prepare_response_for_collection( $form_array, $request );
        $response              = rest_ensure_response( $response );

        return $response;
    }

    /**
     * [update_item_integrations description]
     * @param  [type] $request [description]
     * @return [type]          [description]
    */
    public function update_item_integrations( $request ) {
        $wpuf_form_id      = $request->get_param('form_id');
        $integrations      = $request->get_param('integrations');

        $integration_list = weforms()->integrations->get_integration_js_settings();

        $form             = weforms()->form->get( $wpuf_form_id );
        $form_integration = $form->get_integrations();
        $integrations     =  array_intersect_key(  $integrations, $integration_list );

        if ( !class_exists( 'WeForms_Pro' ) ) {
            $integrations = array_udiff_assoc( $integrations, $integration_list,
                function( $item, $item_list ) {
                    if( isset( $item_list['pro'] ) && $item_list['pro'] == true ) {
                        return 0;
                    }
                    return $item;
                }
            );
        }

        $data = array(
            'form_id'           => $wpuf_form_id,
            'integrations'      => $integrations
        );

        $new_form_integrations = array_merge( $data['integrations'], array_diff_key( $form->get_integrations(), $data['integrations'] ) );

        update_post_meta( $data['form_id'], 'integrations', $new_form_integrations );

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'integrations'  => $form->get_integrations()
        );

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * Duplicate Form
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function duplicate_form( $request ) {
        $form_id = $request['form_id'];
        $form    = weforms()->form->get( $form_id );

        if ( empty( $form ) ) {
            return new WP_Error( 'error', __( 'Could not duplicate the form', 'weforms') , array( 'status' => 404 ) );
        }

        $form_id = weforms()->form->create( $form->name, $form->get_fields());

        $data = array(
            'form_id'           => absint( $form_id ),
            'post_title'        => sanitize_text_field( $form->name ) . ' (#' . $form_id . ')',
            'form_fields'       => weforms()->form->get( $form_id )->get_fields(), // already imported just proxy
            'form_settings'     => $form->get_settings(),
            'form_settings_key' => 'wpuf_form_settings',
            'notifications'     => $form->get_notifications(),
            'integrations'      => $form->get_integrations()
        );

        $form_fields    = weforms()->form->save( $data );
        $form           = weforms()->form->get( $form_id );
        $form->settings = $form->get_settings();

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations()
        );

        $request->set_param( 'context', 'edit' );
        $response = $this->prepare_item_for_response( $response_data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * Get Reports Single Form
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function reports_form( $request ) {
        $form_id = isset( $request['form_id'] ) ? intval( $request['form_id'] ) : 0;
        $labels  = array(); $data = array(); $field_name = array(); $field_label = array(); $temp_names = array();
        $form    = weforms()->form->get( $form_id );
        $fields  = $form->get_fields();

        if ( false === $fields ) {
            return new WP_Error('rest_invalid_field',__( 'No form fields found!','weforms' ),array( 'status' => 404 ) );
        }

        if ( sizeof( $fields ) < 1 ) {
            $fields[] = array(
                'label' => __( 'No form fields found!', 'weforms' )
            );
        }

        foreach ( $fields as $key => $field ) {
            if ( empty( $field['value'] ) ) {
                $has_empty = true;
                break;
            }
        }

        foreach ( $fields as $field ) {
            $temp_names['template'][]    = $field['template'];
            $temp_names['field_name'][]  = $field['name'];
            $temp_names['field_label'][] = $field['label'];
        }

        $form_meta_keys  = $this->get_unique_form_keys( $form_id );

        $i = 0; $j = 0; $k = 0; $l = 0;

        array_shift( $form_meta_keys );
        $report_data = array();

        foreach ( $form_meta_keys as $key => $form_meta_key ) {
            $temp                   = $this->get_chart_data( $form_id, $form_meta_key, $temp_names['template'][$i] );
            $temp2                  = array_values( $temp['value'] );
            $temp['template']       = $temp_names['template'][$i++];
            $temp['field_name']     = $temp_names['field_name'][$j++];
            $temp['field_label']    = $temp_names['field_label'][$k++];

            if( is_array( $temp2[0] ) && !empty( $temp2 ) ) {
                $meta_array = array();

                foreach ($temp2 as $key => $value) {
                    if( !empty( $value ) && is_array( $value ) ) {
                       $meta_value  = unserialize( $value[0] );
                       $meta_array = array_merge($meta_array, $meta_value);
                    }
                }

                $temp2 = $meta_array;
            }

            $temp2 = array_count_values( $temp2 );
            ksort( $temp2 );

            foreach ( $temp2 as $key => $value) {
                if ( empty( $key ) || empty( $value ) ) {
                    continue;
                }
                $temp['data_label'][] = $key;
                $temp['data_value'][] = $value;
            }

            if ( !empty ( $temp['data_label'] ) ) {
                $temp['label'] = $temp['data_label'];
            }

            if ( !empty ( $temp['data_value'] ) ) {
                $temp['data'] = $temp['data_value'];
            }

            unset( $temp['value'], $temp['data_label'], $temp['data_value'] );

            $report_data[] = $temp;
        }

        $response = array(
            'id'     => $form_id,
            'form_title'  => get_post_field( 'post_title', $form_id ),
            'report_data' => $report_data ,
        );

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Save Form Entry from Frontend
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure
     **/
    public function save_entry( $request ) {
        $form_id       = isset( $request['form_id'] ) ? intval( $request['form_id'] ) : 0;
        $page_id       = isset( $request['page_id'] ) ? intval( $request['page_id'] ) : 0;
        $form          = weforms()->form->get( $form_id );
        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();
        $entry_fields  = $form->prepare_entries();
        $entry_fields  = apply_filters( 'weforms_before_entry_submission', $entry_fields, $form, $form_settings, $form_fields );

        $entry_id = $this->weforms_api_insert_entry (array(
            'form_id' => $form_id
        ), $entry_fields);

        if ( is_wp_error( $entry_id ) ) {
            return new WP_Error( 'rest_invalid_data', $entry_id->get_error_message(), array( 'status' => 404 ) );
        }

        // redirect URL
        $show_message = false;
        $redirect_to  = false;

        if ( $form_settings['redirect_to'] == 'page' ) {
            $redirect_to = get_permalink( $form_settings['page_id'] );
        } elseif ( $form_settings['redirect_to'] == 'url' ) {
            $redirect_to = $form_settings['url'];
        } elseif ( $form_settings['redirect_to'] == 'same' ) {
            $show_message = true;
        } else {
            $redirect_to = get_permalink( $post_id );
        }

        $field_search = $field_replace = array();

        foreach ( $form_fields as $r_field ) {
            $field_search[] = '{'.$r_field['name'].'}';

            if ( $r_field['template'] == 'name_field' ) {
                $field_replace[] = implode( ' ' , explode( '|', $entry_fields[$r_field['name']] ));
            } else {
                $field_replace[] = isset( $entry_fields[$r_field['name']] ) ? $entry_fields[$r_field['name']] : '';
            }
        }

        $message = str_replace( $field_search, $field_replace, $form_settings['message'] );

        $notification = new WeForms_Notification( array(
            'form_id'  => $form_id,
            'page_id'  => $page_id,
            'entry_id' => $entry_id
        ) );

        $notification->send_notifications();

        $response_data                 = array();
        $response_data['id']           =  $entry_id;
        $response_data['message']      =  "Entries Created Successfully";
        $response_data['success']      = true;
        $response_data['redirect_to']  = $redirect_to;
        $response_data['show_message'] = $show_message;
        $response_data['message']      = $message;
        $response_data['data']         = $request;
        $response_data['form_id']      = $form_id;
        $response_data['entry_id']     = $entry_id;

        $response = $this->prepare_response_for_collection( $response_data );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Get Single Entry Details
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure
     **/
    public function get_entry_details( $request ) {
        $form_id       = $request['form_id'];
        $entry_id      = $request['entry_id'];
        $form          = weforms()->form->get( $form_id );
        $form_settings = $form->get_settings();
        $entry         = $form->entries()->get( $entry_id );
        $fields        = $entry->get_fields();
        $metadata      = $entry->get_metadata();
        $payment       = $entry->get_payment_data();

        if ( isset( $payment->payment_data ) && is_serialized( $payment->payment_data ) ) {
            $payment->payment_data = unserialize( $payment->payment_data );
        }

        $has_empty          = false;
        $answers            = array();
        $respondentPoints   = isset($form_settings['total_points']) ? floatval( $form_settings['total_points'] ) : 0 ;

        foreach ( $fields as $key => $field ) {

            if ( $form_settings['quiz_form'] == 'yes' ) {
                $selectedAnswers    = isset($field['selected_answers']) ? $field['selected_answers'] : '';
                $givenAnswer        = isset($field['value']) ? $field['value'] : '';
                $options            = isset($field['options']) ? $field['options'] : '';
                $template           = $field['template'];
                $fieldPoints        = isset($field['points']) ? floatval( $field['points'] ) : 0;

                if ( $template == 'radio_field' || $template == 'dropdown_field' ) {
                    $answers[$field['name']] = true;

                    if ( empty($givenAnswer) ) {
                        $answers[$field['name']] = false;
                        $respondentPoints  -= $fieldPoints;
                    } else {
                        foreach ($options as $key => $value) {
                            if ( $givenAnswer == $value ) {
                                if ( $key != $selectedAnswers ) {
                                    $answers[$field['name']] = false;
                                    $respondentPoints  -= $fieldPoints;
                                }
                            }
                        }
                    }
                } elseif ( $template == 'checkbox_field' || $template == 'multiple_select' ) {
                    $answers[$field['name']] = true;
                    $userAnswer = [];

                    foreach ($options as $key => $value) {
                        foreach ($givenAnswer as $answer) {
                            if ($value == $answer) {
                                $userAnswer[] = $key;
                            }
                        }
                    }

                    $userAnswer   = implode('|', $userAnswer);
                    $rightAnswers = implode('|', $selectedAnswers);

                    if ( $userAnswer != $rightAnswers || empty($userAnswer) ) {
                        $answers[$field['name']] = false;
                        $respondentPoints  -= $fieldPoints;
                    }
                }
            } elseif ( empty( $field['value'] ) ) {
                $has_empty      = true;
                break;
            }

        }

        $response = array(
            'form_fields'       => $fields,
            'form_settings'     => $form_settings,
            'meta_data'         => $metadata,
            'payment_data'      => $payment,
            'has_empty'         => $has_empty,
            'respondent_points' => $respondentPoints,
            'answers'           => $answers,
        );

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d/%s/%d', $this->namespace, $this->rest_base, $form->id, 'entries', $entry->id  ) ) );
        return $response;
    }

    /**
     * Retrieves a collection of forms.
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function get_items( $request ) {
        $args = array(
            'posts_per_page' => isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 10,
            'paged'          => isset( $request['page'] ) ? absint( $request['page'] ) : 1,
            'order'          => isset( $request['order'] ) ?  $request['order']  : 'DESC',
            'orderby'        => isset( $request['orderby'] ) ?  $request['orderby']  : 'post_date',
            'post_status'    => isset( $request['status'] ) ?  $request['status']  : 'any',
            's'              => isset( $request['search'] ) ?  $request['search']  : '',
        );

        $forms_array  = array();

        $defaults     = array(
            'post_type'   => 'wpuf_contact_form',
            'post_status' => array( 'publish', 'draft', 'pending' )
        );

        $args           = wp_parse_args( $args, $defaults );
        $query          = new WP_Query( $args );
        $forms          = $query->get_posts();
        $formatted_items = [];

        if ( $forms ) {
            foreach ( $forms as $form_obj ) {
                $form              = new WeForms_Form( $form_obj );

                $data              = array();
                $data['id']        = $form->id;
                $data['name']      = $form->name;
                $data['data']      = $form->data;
                $data['status']    = $form->is_api_form_submission_open();
                $data['fields']    = $form->get_fields();
                $data['entries']   = $form->num_form_entries();
                $data['settings']  = $form->get_settings();
                $data['views']     = $form->num_form_views();
                $data['payments']  = $form->num_form_payments();
                $data              = $this->prepare_item_for_response( (array) $data, $request );
                $formatted_items[] = $this->prepare_response_for_collection( $data );
            }
        }

        $contact_forms = apply_filters( 'weforms_ajax_get_contact_forms', $formatted_items );
        $response      = rest_ensure_response( $contact_forms );
        $response      = $this->format_collection_response( $response, $request, (int) $query->found_posts  );

        return $response;
    }

    /**
     * Creates a single form
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @since 4.7.0
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function create_item( $request ) {
        $template      = isset( $request['template'] ) ? sanitize_text_field( $request['template'] ) : '';
        $name          = isset( $request['name'] ) ? sanitize_text_field( $request['name'] ) : '';
        $setting       = isset( $request['settings'] ) ? $request['settings'] : '';
        $notifications = isset( $request['notifications'] ) ? $request['notifications'] : '';
        $fields        = isset( $request['fields'] ) ? $request['fields'] : '';
        $integrations  = isset( $request['integrations'] ) ? $request['integrations'] : '';

        $default_form_settings     =  weforms_get_default_form_settings();
        $default_form_notification =  weforms_get_default_form_notification();
        $integration_list          =  weforms()->integrations->get_integration_js_settings();
        $field_list                =  weforms()->fields->get_fields();

        foreach ( $fields as $key => $field ) {
            $f  = in_array(  $field['template'], array_keys( $field_list ) );
            if( empty( $f ) ) {
                unset($fields[ $key ] );
            }
        }

        if( isset( $template  ) && !empty( $template ) ) {
            $form_id = weforms()->templates->create( $template );
        } else {
            $form_id = weforms()->form->create( $name, $template->get_form_fields() );

            if ( is_wp_error( $form_id ) ) {
                return new WP_Error( 'rest_invalid_data', __( 'Could not create the form', 'weforms') , array( 'status' => 404 ) );
            }

            $new_form_settings     =  array_diff_key( $default_form_settings, $setting );
            $new_form_notification =  array_diff_key( $default_form_notification, $notifications );
            $integrations          =  array_intersect_key(  $integrations, $integration_list );
            $fields                =  array_intersect_key(  $fields, $field_list );

            if ( !class_exists( 'WeForms_Pro' ) ) {
                $integrations = array_udiff_assoc( $integrations, $integration_list,
                    function( $item, $item_list ) {
                        if( isset( $item_list['pro'] ) && $item_list['pro'] == true ) {
                            return 0;
                        }
                        return $item;
                    }
                );
            }

            // update_post_meta( $data['form_id'], $data['form_settings_key'], $new_form_settings );
            // update_post_meta( $data['form_id'], 'notifications', $existing_notifications );
            // update_post_meta( $data['form_id'], 'integrations', $new_form_integrations );

            return $form_id;
        }



        if ( is_wp_error( $form_id ) ) {
            return new WP_Error( 'rest_invalid_data', __( 'Could not create the form', 'weforms') , array( 'status' => 404 ) );
        }

        $form = weforms()->form->get( $form_id );

        $data = array(
            'id'            =>  $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations()
        );
        $response = $this->prepare_item_for_response( $data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 200 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }


    /**
     * Retrieves a single Form.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
     **/
    public function get_item( $request ) {
        $form_id = isset( $request['form_id'] ) ? absint( $request['form_id'] ) : 0;
        $form = weforms()->form->get( $form_id );

        $data = array(
            'id'            => $form->data->ID,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations(),
            'status'        => $form->is_api_form_submission_open()
        );
        $data     = $this->prepare_item_for_response( $data, $request );
        $response = rest_ensure_response( $data );

        return $response;
    }

    /**
     * Delete a single Form.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
     **/
    public function delete_item( $request ) {
        $form_array = array();
        $form_id    = $request['form_id'];
        $form       = $this->delete( $form_id );

        if ( is_wp_error( $form ) ) {
            return new WP_Error( 'error', __( 'Could not delete the form', 'weforms') , array( 'status' => 404 ) );
        }

        $form_array['id']             = $form_id;
        $form_array['message']        = __( ' form  deleted successfully ', 'weforms' );
        $form_array['data']['status'] = 200;

        $response = $this->prepare_response_for_collection( $form_array, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }


    /**
     * Checks if a given request has access to read form.
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to get  form','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Checks if a given request has access to read form.
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to get the list of form','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Checks if a given request has access to create a form.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_cannot_create', __( 'Sorry, you are not allowed to create form as this user.','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    public function submit_permissions_check( $request ) {
        return true;
    }

    /**
     * Checks if a given request has access to delete a form.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function delete_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this form.','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }



    /**
     * Check form exist or not
     *
     * @since 1.4.2
     *
     * @param integer $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     */
    public function is_entry_exists( $param, $request, $key ) {
        $form          = weforms()->form->get( (int) $request[ 'form_id' ] );
        $form_settings = $form->get_settings();
        $entry         = $form->entries()->get( (int) $request[ 'entry_id' ] );
        $fields        = $entry->get_fields();

        if( $entry->id ) {
            if ( false === $fields ) {
                return new WP_Error( 'rest_forbidden_context', __( 'No form fields found!','weforms' ), array( 'status' => 404 ) );
            }

            if ( sizeof( $fields ) < 1 ) {
                return new WP_Error( 'rest_forbidden_context', __( 'No form fields found!','weforms' ), array( 'status' => 404 ) );
            }
        }

        return (bool) $entry->id;
    }

    /**
     * Check form exist or not
     *
     * @since 1.4.2
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     */
    public function is_builder_form_exists( $param, $request, $key ) {
        parse_str( $param, $form_data );

        if ( isset( $form_data['wpuf_form_id'] ) ) {
            $form = weforms()->form->get( (int) $form_data['wpuf_form_id'] );
            return (bool) $form->id;
        }

        return false;
    }

    /**
     * Check form exist or not
     *
     * @since 1.4.2
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     */
    public function is_form_exists( $param, $request, $key ) {
        global $wpdb;

        $form_id = (int) $param;

        $querystr = "
            SELECT $wpdb->posts.id
            FROM $wpdb->posts
            WHERE $wpdb->posts.ID = $form_id
            AND $wpdb->posts.post_type = 'wpuf_contact_form'
         ";

        $result = $wpdb->get_var( $querystr );

        if( is_null( $result ) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check Form Field Exist
     *
     * @since 1.4.2
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     **/
    public function is_form_exists_field_exists( $param, $request, $key  ) {
        $form = weforms()->form->get( (int) $param );

        if( $form->id ) {

            $form_settings = $form->get_settings();
            $form_fields   = $form->get_fields();
            $form_entries  = weforms_get_form_entries( $form->id, array( 'number'  => '', 'offset'  => '' ) );

            if ( ! $form_fields ) {
                return new WP_Error( 'rest_forbidden_context', __( 'No form fields found!','weforms' ), array( 'status' => 404 ) );
            } else {
                $is_open = $form->is_submission_open();

                if ( is_wp_error( $is_open ) ) {
                     return new WP_Error( 'rest_forbidden_context', $is_open->get_error_message(), array( 'status' => 404 ) );
                }

                if ( $form->has_field( 'recaptcha' ) ) {
                    $validate_Captcha =  $this->validate_reCaptcha( $request );

                    if ( $validate_Captcha == false ) {
                        return new WP_Error( 'rest_invalid_data', __( 'reCAPTCHA validation failed', 'weforms'), array( 'status' => 404,'success' => false, ) );
                    }
                }

                $entry_fields = array();

                foreach ( $form_fields as $key => $field ) {
                    if( ( isset( $field['required'] ) &&  $field['required'] == "yes" && empty( $request->get_param( $field['name']  ) ) ) ) {
                        return new WP_Error( 'rest_forbidden_context', __( 'Required Field Missing' ,'weforms'), array( 'status' => 404 ) );
                    }

                    $ignore_list  = apply_filters('wefroms_entry_ignore_list', array(
                        'recaptcha','step_start'
                    ) );

                    if ( in_array( $field['template'], $ignore_list ) ) {
                        continue;
                    }

                    if( !empty( $field['name']) ) {
                        $entry_fields[ $field['name'] ] = $request->get_param( $field['name'] );
                    }

                    if ( ! array_key_exists( $field['template'], $form_fields ) ) {
                        continue;
                    }
                }

                foreach ( $entry_fields as $field_key => $field_value ) {
                    $duplicate_check = false;
                    $field_label     = 'This';

                    foreach ( $form_fields as $form_field ) {
                        if ( in_array( $form_field['template'], array( 'text_field', 'website_url', 'numeric_text_field', 'email_address' ) ) && $form_field['name'] == $field_key && isset( $form_field['duplicate'] ) && 'no' == $form_field['duplicate'] ) {
                            $duplicate_check = true;
                            $field_label     = $form_field['label'];
                        }
                    }

                    if ( $duplicate_check ) {

                        foreach ( $form_entries as $entry ) {
                            $existing = weforms_get_entry_meta( $entry->id, $field_key, true );

                            if ( $existing && $field_value == $existing ) {
                                    return new WP_Error( 'rest_forbidden_context',
                                        __('field requires a unique entry and  has already been used.' ,'weforms')
                                    , array( 'status' => 404 ) );
                            }
                        }

                    }
                }


                foreach ( $form_fields as $key => $field ) {
                    //skip custom html field as it is not saved
                    if ( 'custom_html' == $field['template'] )
                        continue;

                    //skip recaptcha field as it is not saved
                    if ( 'recaptcha' == $field['template'] )
                        continue;

                    if( !empty( $field['name'] ) ) {
                        $value = $request[ $field['name'] ];
                    }

                    if ( 'file_upload' ===  $field['template'] ) {
                        $file     = $request->get_param('wpuf_files');
                        $file_ids = $file[ $field['name'] ];

                        foreach ($file_ids as $key => $file_id) {
                            if ( !wp_get_attachment_url( $file_id ) ) {
                                return new WP_Error( 'rest_forbidden_context', __( 'File Not Found' ,'weforms'), array( 'status' => 404 ) );
                            }
                        }
                    }

                     if ( 'image_upload' ===  $field['template'] ) {
                        $image     = $request->get_param('wpuf_files');
                        $image_ids = $image[ $field['name'] ];

                        foreach ($image_ids as $key => $image_id) {
                            if ( !wp_get_attachment_url( $image_id ) ) {
                                return new WP_Error( 'rest_forbidden_context',  __( 'Image Not Found' ,'weforms'), array( 'status' => 404 ) );
                            }
                        }
                    }

                    if ( 'date_field' ===  $field['template'] ) {
                        $date_format = $field['format'];

                        $possible_date= array(
                              "dd-mm-yy" => "d-m-y",
                              "yy-mm-dd" => "y-m-d",
                              "mm-dd-yy" => "m-d-y",
                              "dd/mm/yy" => "d/m/y",
                              "yy/mm/dd" => "y/m/d",
                              "mm/dd/yy" => "m/d/y",
                              "dd.mm.yy" => "d/m/y",
                              "yy.mm.dd" => "y/m/d",
                              "mm.dd.yy" => "m/d/y",
                        );


                        foreach ( $possible_date as $key => $date ) {
                            if ( strcmp( $key,$date_format ) == 0 ) {
                                $date_format = $date;
                                break;
                           }

                        }

                        $d = DateTime::createFromFormat( $date_format, $value );

                        if ( !( $d && ( $d->format($date_format) == $value ) ) ) {
                            return new WP_Error( 'rest_forbidden_context', __( 'Date Field Is not Valid' ,'weforms'), array( 'status' => 404 ) );
                        }

                    }

                    if ( 'email_address' === $field['template'] ) {
                        if ( !filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
                            return new WP_Error( 'rest_forbidden_context', __( 'Invalid Email Address' ,'weforms'), array( 'status' => 404 ) );
                        }
                    }

                    if ( 'website_url' === $field['template'] ) {
                        if ( !filter_var( $value, FILTER_VALIDATE_URL ) ) {
                            return new WP_Error( 'rest_forbidden_context', __( 'Invalid Url Address' ,'weforms'), array( 'status' => 404 ) );
                        }
                    }

                    if ( 'numeric_text_field' === $field['template'] ) {
                        if( !is_numeric( $value ) ) {
                            return new WP_Error( 'rest_forbidden_context', __( 'Number Field Should be numberic value ' ,'weforms'), array( 'status' => 404 ) );
                        }
                    }



                    if ( 'single_product' === $field['template'] ) {
                        if ( ! $value ) {
                            $value = array();
                        }

                        $value['price']    = isset( $value['price'] ) ? floatval( $value['price'] ) : 0;
                        $value['quantity'] = isset( $value['quantity'] ) ? floatval( $value['quantity'] ) : 0;
                        $quantity          = isset( $field['quantity'] ) ? $field['quantity'] : array();
                        $price             = isset( $field['price'] ) ? $field['price'] : array();

                        if ( isset( $price['is_flexible'] ) && $price['is_flexible'] ) {
                            $min = isset( $price['min'] ) ? floatval( $price['min'] ) : 0;
                            $max = isset( $price['max'] ) ? floatval( $price['max'] ) : 0;

                            if ( $value['price'] < $min ) {
                                return new WP_Error( 'rest_forbidden_context', sprintf( __( '%s price must be equal or greater than %s',
                                        $field['weforms'],
                                        $min ,
                                        'weforms'
                                    )
                                ), array( 'status' => 404 ) );
                            }

                            if ( $max && $value['price'] > $max ) {
                                return new WP_Error( 'rest_forbidden_context', sprintf( __( '%s price must be equal or less than %s',
                                        $field['weforms'],
                                        $max ,
                                        'weforms'
                                    )
                                ), array( 'status' => 404 ) );
                            }
                        }

                        if ( isset( $quantity['status'] ) && $quantity['status'] ) {
                            $min = isset( $quantity['min'] ) ? floatval( $quantity['min'] ) : 0;
                            $max = isset( $quantity['max'] ) ? floatval( $quantity['max'] ) : 0;

                            if ( $value['quantity'] < $min ) {
                                return new WP_Error( 'rest_forbidden_context', sprintf( __(
                                        '%s quantity must be equal or greater than %s',
                                        $field['weforms'],
                                        $min ,
                                        'weforms'
                                    )
                                ), array( 'status' => 404 ) );
                            }

                            if ( $max && $value['quantity'] > $max ) {
                                return new WP_Error( 'rest_forbidden_context', sprintf( __(
                                        '%s quantity must be equal or less than %s',
                                        $field['weforms'],
                                        $max ,
                                        'weforms'
                                    )
                                ), array( 'status' => 404 ) );
                            }
                        }
                    }
                }

            }

            return true;
        }

        return false;
    }

    /**
     * Bulk Delete Form Exist
     *
     * @since 1.4.2
     *
     * @param array $param
     * @param WP_REST_Request $request Full details about the request
     * @param string $key
     *
     * @return boolean
     **/
    public function is_bulk_delete_form_exists( $param, $request, $key ) {
        global $wpdb;

        if( is_array( $param ) ) {
            $form_id = implode( ",", $param );

            $querystr = " SELECT $wpdb->posts.id
                FROM $wpdb->posts
                WHERE $wpdb->posts.post_type = 'wpuf_contact_form'
                AND $wpdb->posts.ID IN ( $form_id )
            ";

        } else {
            $form_id = (int) $param;

            $querystr = " SELECT $wpdb->posts.id
                FROM $wpdb->posts
                WHERE $wpdb->posts.post_type = 'wpuf_contact_form'
                AND $wpdb->posts.ID = $form_id
            ";

        }

        $result = $wpdb->get_results( $querystr );

        if ( empty( $result ) ) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * reCaptcha Validation
     *
     * @since 1.4.2
     *
     * @return void
     */
    function validate_reCaptcha( $request ) {
        if ( class_exists( 'WPUF_ReCaptcha' ) ) {
            $recaptcha_class = 'WPUF_ReCaptcha';
        } else {
            if ( ! function_exists( 'recaptcha_get_html' ) ) {
                require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib.php';
            }

            require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib_noCaptcha.php';

            $recaptcha_class = 'Weforms_ReCaptcha';
        }

        $invisible = isset( $request['g-recaptcha-response'] ) ? false : true;

        $recaptcha_settings = weforms_get_settings( 'recaptcha' );
        $secret             = isset( $recaptcha_settings->secret ) ? $recaptcha_settings->secret : '';

        if ( ! $invisible ) {
            $response = null;
            $reCaptcha = new $recaptcha_class( $secret );

            $resp = $reCaptcha->verifyResponse(
                $_SERVER['REMOTE_ADDR'],
                $request['g-recaptcha-response']
            );

            if ( ! $resp->success ) {
                return false;
            }
        } else {
            $recap_challenge = isset( $request['recaptcha_challenge_field'] ) ? $request['recaptcha_challenge_field'] : '';
            $recap_response  = isset( $request['recaptcha_response_field'] ) ? $request['recaptcha_response_field'] : '';
            $resp            = recaptcha_check_answer( $secret, $_SERVER['REMOTE_ADDR'], $recap_challenge, $recap_response );

            if ( ! $resp->is_valid ) {
                ob_clean();
                return false;
            }
        }

        return true;
    }


    /**
     * Delete a form with it's input fields
     *
     * @since 1.4.2
     *
     * @param  integer  $form_id
     * @param  boolean $force
     *
     * @return void
     */
    public function delete( $form_id, $force = true ) {
        global $wpdb;

        $form = weforms()->form->get( $form_id );

        if ( !$form->id ) {
            return new WP_Error( 'form_not_found', __( 'Form not found', 'weforms' ) );
        }

        $wp_post = wp_delete_post( $form_id, $force );

        if ( ! $wp_post ) {
            return new WP_Error( 'unable_to_delete', __( 'Unable to delete form', 'weforms' ) );
        }

        // delete form inputs as WP doesn't know the relationship
        return $wpdb->delete( $wpdb->posts,
            array(
                'post_parent' => $form_id,
                'post_type'   => 'wpuf_input'
            )
        );

        return $form;
    }

    /**
     * Insert a new entry
     *
     * @since 1.4.2
     * @param  array $args
     * @param  array $fields
     *
     * @return WP_Error|integer
     */
    function weforms_api_insert_entry( $args, $fields = array() ) {
        global $wpdb;

        $browser = $this->weforms_api_get_browser();

        $defaults = array(
            'form_id'     => 0,
            'user_id'     => get_current_user_id(),
            'user_ip'     => ip2long( weforms_get_client_ip() ),
            'user_device' => $browser['name'] . '/' . $browser['platform'],
            'created_at'  => current_time( 'mysql' )
        );

        $r = wp_parse_args( $args, $defaults );

        if ( ! $r['form_id'] ) {
            return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'weforms' ) );
        }

        if ( ! $fields ) {
            return new WP_Error( 'no-fields', __( 'No form fields were found.', 'weforms' ) );
        }

        $success = $wpdb->insert( $wpdb->weforms_entries, $r );

        if ( is_wp_error( $success ) || ! $success ) {
            return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'weforms' ), array( 'status' => 404 ) );
        }

        $entry_id = $wpdb->insert_id;

        foreach ( $fields as $key => $value ) {
            weforms_add_entry_meta( $entry_id, $key, $value );
        }

        return $entry_id;
    }

    /**
     * Get User Agent browser and OS type
     *
     * @since 1.4.2
     *
     * @return array
     */
    function weforms_api_get_browser() {
        $u_agent  = $_SERVER['HTTP_USER_AGENT'];
        $bname    = 'Unknown';
        $platform = 'Unknown';
        $version  = '';

        // first get the platform
        if ( preg_match( '/linux/i', $u_agent ) ) {
            $platform = 'Linux';
        } elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
            $platform = 'MAC OS';
        } elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
            $platform = 'Windows';
        }

        // next get the name of the useragent yes seperately and for good reason
        if ( preg_match( '/MSIE/i',$u_agent ) && ! preg_match( '/Opera/i',$u_agent ) ) {
            $bname = 'Internet Explorer';
            $ub    = 'MSIE';
        } elseif ( preg_match( '/Trident/i',$u_agent ) ) {
            // this condition is for IE11
            $bname = 'Internet Explorer';
            $ub = 'rv';
        } elseif ( preg_match( '/Firefox/i',$u_agent ) ) {
            $bname = 'Mozilla Firefox';
            $ub = 'Firefox';
        } elseif ( preg_match( '/Chrome/i',$u_agent ) ) {
            $bname = 'Google Chrome';
            $ub = 'Chrome';
        } elseif ( preg_match( '/Safari/i',$u_agent ) ) {
            $bname = 'Apple Safari';
            $ub = 'Safari';
        } elseif ( preg_match( '/Opera/i',$u_agent ) ) {
            $bname = 'Opera';
            $ub = 'Opera';
        } elseif ( preg_match( '/Netscape/i',$u_agent ) ) {
            $bname = 'Netscape';
            $ub = 'Netscape';
        }
        $ub = 'Netscape';

        // finally get the correct version number
        // Added "|:"
        $known = array( 'Version', $ub, 'other' );
        $pattern = '#(?<browser>' . join( '|', $known ) . ')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
            // we have no matching number just continue
        }

        $version = '1';

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'   => $pattern
        );
    }

    /**
     * Format item's collection for response
     *
     * @since 1.4.2
     *
     * @param  object $response
     * @param  object $request
     * @param  array $items
     * @param  int $total_items
     *
     * @return object
     */
    public function format_collection_response( $response, $request, $total_items ) {
        if ( $total_items === 0 ) {
            return $response;
        }

        // Store pagation values for headers then unset for count query.
        $per_page = (int) ( ! empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page     = (int) ( ! empty( $request['page'] ) ? $request['page'] : 1 );

        $response->header( 'X-WP-Total', (int) $total_items );

        $max_pages = ceil( $total_items / $per_page );

        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;
            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }
        if ( $max_pages > $page ) {

            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    /**
     * prepare_item_for_response
     * @param  [type] $form    [description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function prepare_item_for_response( $form, $request ) {
        $response = rest_ensure_response( $form );
        $response = $this->add_links( $response, $form );
        return $response;
    }

    /**
     * Adds multiple links to the response.
     *
     * @since 1.4.2
     *
     * @param   object $response
     * @param   object $item
     *
     * @return  object $response
     */
    protected function add_links( $response, $item ) {
        $response->data['_links'] = $this->prepare_links( $item );

        return $response;
    }

    /**
     * Prepare links for the request.
     *
     *  @since 1.4.2
     *
     * @param  object $item
     *
     * @return array Links for the given user.
     */
    protected function prepare_links( $item ) {
        $links = [
            'self' => [
                'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $item['id'] ) ),
            ],
            'collection' => [
                'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
            ]
        ];

        return $links;
    }

    /**
     * Save and existing form
     *
     * @since 1.4.2
     *
     * @param array $data Contains form_fields, form_settings, form_settings_key data
     *
     * @return boolean
     */
    public function weforms_api_save( $data ) {
        $saved_wpuf_inputs = array();

        wp_update_post( array( 'ID' => $data['form_id'], 'post_status' => 'publish', 'post_title' => $data['post_title'] ) );

        $existing_wpuf_input_ids = get_children( array(
            'post_parent' => $data['form_id'],
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'ids'
        ) );

        $new_wpuf_input_ids = array();

        if ( ! empty( $data['form_fields'] ) ) {

            foreach ( $data['form_fields'] as $order => $field ) {
                if ( ! empty( $field['is_new'] ) ) {
                    unset( $field['is_new'] );
                    unset( $field['id'] );

                    $field_id = 0;

                } else {
                    $field_id = $field['id'];
                }

                $field_id = weforms_insert_form_field( $data['form_id'], $field, $field_id, $order );

                $new_wpuf_input_ids[] = $field_id;

                $field['id'] = $field_id;

                $saved_wpuf_inputs[] = $field;
            }
        }

        $form                   = weforms()->form->get( $data['form_id'] );
        $new_form_settings      = array_merge( $data['form_settings'], array_diff_key( $form->get_settings(), $data['form_settings'] ) );
        $new_form_notifications = array_merge( $data['notifications'], array_diff_key( $form->get_notifications(), $data['notifications'] ) );
        $new_form_integrations  = array_merge( $data['integrations'], array_diff_key( $form->get_integrations(), $data['integrations'] ) );

        update_post_meta( $data['form_id'], $data['form_settings_key'], $new_form_settings );
        update_post_meta( $data['form_id'], 'notifications', $new_form_notifications );
        update_post_meta( $data['form_id'], 'integrations', $new_form_integrations );
        update_post_meta( $data['form_id'], '_weforms_version', WEFORMS_VERSION );

        return $saved_wpuf_inputs;
    }

    /**
     * get item settings
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item_settings( $request ) {
        $form_id = $request->get_param('form_id');
        $form    = weforms()->form->get( $form_id );

        $data = array(
            'id'       => $form->data->ID,
            'name'     => $form->name,
            'settings' => $form->get_settings(),
        );

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['fields'] )  );

        return $response;
    }

    /**
     * get item integrations
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item_integrations( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $form    = weforms()->form->get( $form_id );

        $data = array(
            'id'           => $form->data->ID,
            'name'         => $form->name,
            'integrations' => $form->get_integrations()
        );

        $response  = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['integrations'] )  );

        return $response;
    }

    /**
     * get item notification
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item_notification( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $form    = weforms()->form->get( $form_id );

        $data = array(
            'id'       => $form->data->ID,
            'name'     => $form->name,
            'notifications' => $form->get_notifications(),
        );

        $response  = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['notifications'] )  );

        return $response;
    }

    /**
     * add item notification
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function add_item_notification( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $notifications      = $request->get_param('notifications');

        $data = array(
            'form_id'       => $form_id,
            'notifications' => $notifications
        );

        $form                  = weforms()->form->get( $form_id );
        $new_form_notification = array_merge( $form->get_notifications(), $data['notifications'] );

        update_post_meta( $data['form_id'], 'notifications', $new_form_notification );

        $form = weforms()->form->get( $form_id );

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'notifications' => $form->get_notifications(),
        );

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );
        return $response;
    }

    /**
     * update item integrations
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_item_notification( $request ) {
        $wpuf_form_id  = $request->get_param('form_id');
        $notifications = $request->get_param('notifications');

        $data = array(
            'form_id'       => $wpuf_form_id,
            'notifications' => $notifications
        );

        $form                   = weforms()->form->get( $wpuf_form_id );
        $existing_notifications = $form->get_notifications();
        $message = array();

        foreach ( $existing_notifications as $key => $notification ) {
            if( array_key_exists( $key , $data['notifications'] ) ) {
                $existing_notifications[ $key ] = $data['notifications'][$key];
            }
        }

        update_post_meta( $data['form_id'], 'notifications', $existing_notifications );

        $form = weforms()->form->get( $wpuf_form_id );

        $response_data = array(
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'notifications' => $form->get_notifications(),
            'message'       => $message
        );

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * delete item notification
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_item_notification( $request ) {
        $form_id           = $request->get_param( 'form_id' );
        $notification_ids  = $request->get_param( 'index' );
        $form              = weforms()->form->get( $form_id );
        $form_notification = $form->get_notifications();

        foreach ($notification_ids as $notification_id) {
            unset( $form_notification[ $notification_id ] );
        }

        update_post_meta( $form_id, 'notifications', array_values( $form_notification ) );

        $form = weforms()->form->get( $form_id );

        $data = array(
            'id'       => $form->data->ID,
            'name'     => $form->name,
            'notifications' => $form->get_notifications(),
        );

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['notifications'] )  );

        return $response;
    }

    /**
     * Get the Form's schema, conforming to JSON Schema
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'forms',
            'type'       => 'object',
            'properties' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'required'          => true,
                    'readonly'    => true,
                ),

                'name' => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'string',
                    'context'           => [ 'edit','view'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'required'          => true,
                ),

                "settings_key" => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'string',
                    'context'           => [ 'edit','view' ],
                    'sanitize_callback' => 'sanitize_text_field',
                    'required'          => false,
                ),

                "settings" => array(
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'object',
                    'context'     => [ 'edit' ,'view'],
                    'required'          => false,
                ),

                "notifications" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit','view' ],
                    'required'    => true,
                ),

                "fields" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit','view' ],
                    'required'    => true,
                ),

                "integrations" => array(
                    'description' => __( '', 'weforms' ),
                    'context'     => [ 'edit','view' ],
                    'type'        => 'object',
                    'required'    => true,
                ),
            ),
        );

        return $schema;
    }
}
