<?php

/**
 * Plugin Upgrade Routine
 *
 * @since 1.1.2
 */
class WeForms_Upgrades {

    /**
     * The upgrades
     *
     * @var array
     */
    private static $upgrades = [];

    /**
     * Get the plugin version
     *
     * @return string
     */
    public function get_version() {
        return get_option( 'weforms_version' );
    }

    /**
     * Check if the plugin needs any update
     *
     * @return bool
     */
    public function needs_update() {

        // may be it's the first install
        if ( !$this->get_version() ) {
            return false;
        }

        if ( version_compare( $this->get_version(), WEFORMS_VERSION, '<' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Perform all the necessary upgrade routines
     *
     * @return void
     */
    public function perform_updates() {
        $installed_version = $this->get_version();
        $path              = trailingslashit( __DIR__ );

        foreach ( self::$upgrades as $version => $file ) {
            if ( version_compare( $installed_version, $version, '<' ) ) {
                include $path . $file;
                update_option( 'weforms_version', $version );
            }
        }

        update_option( 'weforms_version', WEFORMS_VERSION );
    }
}
