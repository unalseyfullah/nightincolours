<?php
namespace Ari_Adminer;

use Ari\App\Installer as Ari_Installer;
use Ari\Database\Helper as DB;
use Ari\Wordpress\Security as Security;

class Installer extends Ari_Installer {
    function __construct( $options = array() ) {
        if ( ! isset( $options['installed_version'] ) ) {
            $installed_version = get_option( ARIADMINER_VERSION_OPTION );

            if ( false !== $installed_version) {
                $options['installed_version'] = $installed_version;
            }
        }

        if ( ! isset( $options['version'] ) ) {
            $options['version'] = ARIADMINER_VERSION;
        }

        parent::__construct( $options );
    }

    private function init() {
        $this->add_cap();

        $sql = file_get_contents( ARIADMINER_INSTALL_PATH . 'install.sql' );

        $queries = DB::split_sql( $sql );

        foreach( $queries as $query ) {
            $this->db->query( $query );
        }
    }

    public function run() {
        $this->init();

        if ( ! $this->run_versions_updates() ) {
            return false;
        }

        update_option( ARIADMINER_VERSION_OPTION, $this->options->version );

        return true;
    }

    private function add_cap() {
        if ( is_multisite() )
            return ;

        $roles = Security::get_roles();

        foreach ( $roles as $role ) {
            if ( $role->has_cap( 'manage_options' ) ) {
                $role->add_cap( ARIADMINER_CAPABILITY_RUN );
            }
        }
    }
}
