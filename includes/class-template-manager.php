<?php

/**
 * Template Manager Class
 *
 * @since 1.1.0
 */
class WeForms_Template_Manager {

    /**
     * The templates
     *
     * @var array
     */
    private $templates = array();

    /**
     * Get all the registered fields
     *
     * @return array
     */
    public function get_templates() {

        if ( ! empty( $this->templates ) ) {
            return $this->templates;
        }

        $this->register_templates();

        return $this->templates;
    }

    /**
     * Get all the templates
     *
     * @return array
     */
    public function register_templates() {

        require_once WEFORMS_INCLUDES . '/templates/class-abstract-template.php';

        require_once WEFORMS_INCLUDES . '/templates/class-template-blank.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-contact.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-event-registration.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-support.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-tell-a-friend.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-job-application.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-comment-and-rating.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-employee-information.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-to-do-list.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-real-estate-listing.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-my-directory-information.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-request-for-quote.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-leave-request.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-admission-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-patient-itake-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-loan-application-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-website-feedback-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-volunteer-application.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-bug-report.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-job-listing.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-donation-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-product-order-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-online-booking-form.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-restaurant-reservation.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-conference-proposal.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-polling-form.php';
        require_once WEFORMS_INCLUDES . '/templates/dokan/class-vendor-contact-form.php';

        $templates = array(
            'blank'                    => new WeForms_Template_Blank(),
            'contact'                  => new WeForms_Template_Contact(),
            'event_registration'       => new WeForms_Template_Event_Registration(),
            'support'                  => new WeForms_Template_Support(),
            'tell_a_friend'            => new WeForms_Template_Tell_A_Friend(),
            'job_application'          => new WeForms_Template_Job_Application(),
            'my_directory_information' => new WeForms_Template_My_Directory_Information(),
            'volunteer_application'    => new WeForms_Template_Volunteer_Application(),
            'bug_report'               => new WeForms_Template_Bug_Report(),
            'job_listing'              => new WeForms_Template_Job_Listing(),
            'leave_request'            => new WeForms_Template_Leave_Request(),
            'real_estate_listing'      => new WeForms_Template_Real_Estate_Listing(),
            'website_feedback'         => new WeForms_Template_Website_Feedback(),
            'request_for_quote'        => new WeForms_Template_Request_For_Quote(),
            'comment_rating'           => new WeForms_Template_Comment_Rating(),
            'employee_information'     => new WeForms_Template_Employee_Information(),
            'todo_list'                => new WeForms_Template_Todo_List(),
            'admission_form'           => new WeForms_Template_Admission_Form(),
            'patient_intake_form'      => new WeForms_Template_Patient_Intake_Form(),
            'loan_application_form'    => new WeForms_Template_Loan_Application_Form(),
            'donation_form'            => new Weforms_Donation_Form(),
            'product_order_form'       => new WeForms_Template_Product_Order_Form(),
            'online_booking_form'      => new Weforms_Template_Online_Booking_Form(),
            'restaurant_reservation'   => new WeForms_Template_Restaurant_Reservation(),
            'conference_proposal'      => new Weforms_Template_Conference_Proposal(),
            'polling_form'             => new WeForms_Template_Polling_Form(),
            'vendor_contact_form'      => new WeForms_Vendor_Contact_Form(),
        );

        $this->templates = apply_filters( 'weforms_get_templates', $templates );
    }

    /**
     * Check if a template exists
     *
     * @param  string $name
     *
     * @return boolean
     */
    public function exists( $name ) {
        if ( array_key_exists( $name, $this->get_templates() ) ) {
            return $this->templates[ $name ];
        }

        return false;
    }

    /**
     * Create a form from a template
     *
     * @param  string $name
     *
     * @return integer
     */
    public function create( $name ) {
        if ( ! $template = $this->exists( $name ) ) {
            return;
        }

        $form_id = weforms()->form->create( $template->get_title(), $template->get_form_fields() );

        if ( is_wp_error( $form_id ) ) {
            return $form_id;
        }

        $meta_updates = array(
            'wpuf_form_settings' => $template->get_form_settings(),
            'notifications'      => $template->get_form_notifications(),
            'integrations'       => array()
        );

        foreach ($meta_updates as $meta_key => $meta_value) {
            update_post_meta( $form_id, $meta_key, $meta_value );
        }

        return $form_id;
    }
}
