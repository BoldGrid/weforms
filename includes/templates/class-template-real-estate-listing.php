<?php

/**
 * Blank form template
 */
class WeForms_Template_Real_Estate_Listing extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists('WeForms_Pro');
        $this->title       = __( 'Real Estate Listing', 'weforms' );
        $this->description = __( 'Take on bigger projects Right Now.You can do far more. Earn more clients and grow your business', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/real_state_list.png';
        $this->category    = 'others';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = array(

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => ' ',
                'name'      => 'featured',
                'options'   => array(
                    'feature'   => 'Featured'
                ),
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'MLS ID', 'weforms' ),
                'name'      => 'mls_id',
            ) ),


            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'Street Address', 'weforms' ),
                'name'      => 'street_address',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'City', 'weforms' ),
                'name'      => 'city',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'State', 'weforms' ),
                'name'      => 'state',
                'options'   => array(
                    'example1'  => 'Example One',
                    'example2'  => 'Example Two',
                    'example3'  => 'Example Trhee',
                    'example4'  => 'Example Four',
                    'example5'  => 'Example Five'
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'Zip Code', 'weforms' ),
                'name'      => 'zip_code',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'Type', 'weforms' ),
                'name'      => 'type',
                'options'   => array(
                    'example1'  => 'Example One',
                    'example2'  => 'Example Two',
                    'example3'  => 'Example Trhee',
                    'example4'  => 'Example Four',
                    'example5'  => 'Example Five'
                ),
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'Property Status', 'weforms' ),
                'name'      => 'property_status',
                'options'   => array(
                    'active'         => 'Active',
                    'sale_pending'   => 'Sale Pending',
                    'sold'           => 'Sold',
                    'lease_pending'  => 'Lease Pending',
                    'rented'         => 'Rented'
                ),
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __( 'Price', 'weforms' ),
                'name'      => 'price',
            ) ),

            array_merge( $all_fields['date_field']->get_field_props(), array(
                'label'     => __( 'List Date', 'weforms' ),
                'name'      => 'list_date',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'  =>  'yes',
                'label'     => __( 'Brief Blurb', 'weforms' ),
                'name'      => 'brief_blurb',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required'  =>  'yes',
                'label'     => __( 'Description', 'weforms' ),
                'name'      => 'description',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'  =>  'yes',
                'label'     => __( 'Bedrooms', 'weforms' ),
                'name'      => 'bedrooms',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'  =>  'yes',
                'label'     => __( 'Full Boths', 'weforms' ),
                'name'      => 'full_boths',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __( 'Garage Spaces', 'weforms' ),
                'name'      => 'garage_space',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __( 'Sqft (Living)', 'weforms' ),
                'name'      => 'sqft_living',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __( 'Sqft (Total)', 'weforms' ),
                'name'      => 'sqft_total',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __( 'Acres', 'weforms' ),
                'name'      => 'acres',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __( 'Year Built', 'weforms' ),
                'name'      => 'year_built',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'General Features', 'weforms' ),
                'name'      => 'general_feature',
                'options'   => array(
                    'balcony'           => 'Balcony',
                    'bbq'               => 'BBQ',
                    'courtyard'         => 'Courtyard',
                    'horse_facilitie'   => 'Horse Facilities',
                    'greenhouse'        => 'Greenhouse',
                    'lease_option'      =>  'Lease Option',
                    'pets_allowed'      => 'Pets Allowed',
                    'rv_boat_parking'   => 'RV/Boat Parking',
                    'spa_hot_tub'       => 'Spa/Hot Tub',
                    'tennis_court'      => 'Tennis Court(s)',
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Rooms', 'weforms' ),
                'name'      => 'rooms',
                'options'   => array(
                    'dining_room'   => 'Dining Room',
                    'family_Room'   => 'Family Room',
                    'den_office'    => 'Den/Office',
                    'basement'      => 'Basement',
                    'laundry_Room'  => 'Laundry Room',
                    'game_Room'     => 'Game Room',
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Lot Features', 'weforms' ),
                'name'      => 'lot_features',
                'options'   => array(
                    'corner_lot'                => 'Corner Lot',
                    'cul_de_sac'                =>  'Cul-de-Sac',
                    'golf_course_lot_frontage'  => 'Golf Course Lot/Frontage',
                    'golf_course_view'          => 'Golf Course View',
                    'waterfront'                => 'Waterfront',
                    'city_view'                 => 'City View',
                    'lake_view'                 => 'Lake View',
                    'hill_mountain_view'        => 'Hill/Mountain View',
                    'ocean_view'                => 'Ocean View',
                    'park_view'                 => 'Park View',
                    'river_view'                => 'River View',
                    'water_view'                => 'Water View',
                    'view'                      => 'View'
                ),
            ) ),


            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Air Conditioning', 'weforms' ),
                'name'      => 'air_conditioning',
                'options'   => array(
                    'central_air'   => 'Central Air',
                    'forced_air'    =>  'Forced Air',
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Interior', 'weforms' ),
                'name'      => 'interior',
                'options'   => array(
                    'ceiling_fans'              =>  'Ceiling Fans',
                    'custom_window_covering'    =>  'Custom Window Covering',
                    'disability_features'       =>  'Disability Features',
                    'energy_efficient_home'     =>  'Energy Efficient Home',
                    'hardwood_floors'           =>  'Hardwood Floors',
                    'home_warranty'             =>  'Home Warranty',
                    'intercom'                  =>  'Intercom',
                    'pool'                      =>  'Pool',
                    'skylight'                  =>  'Skylight',
                    'window_blinds'             =>  'Window Blinds',
                    'window_coverings'          =>  'Window Coverings',
                    'window_drapes/Curtains'    =>  'Window Drapes/Curtains',
                    'window_shutters'           =>  'Window Shutters',
                    'vaulted_ceiling'           =>  'Vaulted Ceiling',
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Heat', 'weforms' ),
                'name'      => 'heat',
                'options'   => array(
                    'central'            =>  'Central',
                    'electric'           =>  'Electric',
                    'multiple_units'     =>  'Multiple Units',
                    'natural_gas'        =>  'Natural Gas',
                    'solar'              =>  'Solar',
                    'wall_furnace'       =>  'Wall Furnace',
                    'wood'               =>  'Wood',
                    'none'               =>  'None',
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Fireplace', 'weforms' ),
                'name'      => 'fireplace',
                'options'   => array(
                    'freestanding'      =>  'Freestanding',
                    'gas_burning'       =>  'Gas Burning',
                    'two_way'           =>  'Two-way',
                    'wood_burning'      =>  'Natural Gas',

                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __( 'Community Features', 'weforms' ),
                'name'      => 'community_features',
                'options'   => array(
                    'recreation_facilities'         =>  'Recreation Facilities',
                    'community_security_features'   =>  'Community Security Features',
                    'community_swimming_pool'       =>  'Community Swimming Pool(s)',
                    'community_boat_facilities'     =>  'Community Boat Facilities',
                    'community_clubhouse'           =>  'Community Clubhouse(s)',
                    'community_horse_facilities'    =>  'Community Horse Facilities',
                    'community_tennis_Court'        =>  'Community Tennis Court(s)',
                    'community_park'                =>  'Community Park(s)',
                    'community_golf'                =>  'Community Golf',
                    'senior_community'              =>  'Senior Community',
                    'community_spa_hot_tub'         =>  'Community Spa/Hot Tub(s)',
                ),

            ) ),

        );

        return $form_fields;

    }

}
