<?php

/**
 * Importer Manager
 *
 * @since 1.1.0
 */
class WeForms_Importer_Manager {

    function __construct() {
        $this->get_importers();
    }

    /**
     * Fetch and instantiate all the importers
     *
     * @return array
     */
    public function get_importers() {
        require_once WEFORMS_INCLUDES . '/importer/class-importer-abstract.php';
        require_once WEFORMS_INCLUDES . '/importer/class-importer-cf7.php';
        require_once WEFORMS_INCLUDES . '/importer/class-importer-gf.php';
        require_once WEFORMS_INCLUDES . '/importer/class-importer-wpforms.php';
        require_once WEFORMS_INCLUDES . '/importer/class-importer-ninja-forms.php';
        require_once WEFORMS_INCLUDES . '/importer/class-importer-caldera-forms.php';

        $importers = array(
            'cf7'        => new WeForms_Importer_CF7(),
            'gravity'    => new WeForms_Importer_GF(),
            'wpforms'    => new WeForms_Importer_WPForms(),
            'ninjaforms' => new WeForms_Importer_Ninja_Forms(),
            'caldera'    => new WeForms_Importer_Caldera_Forms(),
        );

        return apply_filters( 'weforms_form_importers', $importers );
    }
}