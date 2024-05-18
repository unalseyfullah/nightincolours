<?php
namespace Ari_Adminer\Helpers;

use Ari_Adminer\Utils\Db_Driver as DB_Driver;

class Helper {
    private static $system_args = array(
        'action',

        'msg',

        'msg_type',

        'noheader',
    );

    private static $themes = null;

    public static function has_access_to_adminer() {
        return is_super_admin( get_current_user_id() ) || current_user_can( ARIADMINER_CAPABILITY_RUN );
    }

    public static function build_url( $add_args = array(), $remove_args = array(), $remove_system_args = true, $encode_args = true ) {
        if ( $remove_system_args ) {
            $remove_args = array_merge( $remove_args, self::$system_args );
        }

        if ( $encode_args )
            $add_args = array_map( 'rawurlencode', $add_args );

        return add_query_arg( $add_args, remove_query_arg( $remove_args ) );
    }

    public static function get_themes() {
        if ( ! is_null( self::$themes ) ) {
            return self::$themes;
        }

        $folders = array();
        $path = ARIADMINER_THEMES_PATH;
        $exclude = array( 'assets' );

        if ( ! ( $handle = @opendir( $path ) ) ) {
            return $folders;
        }

        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( '.' == $file || '..' == $file || in_array( $file, $exclude ) )
                continue ;

            $is_dir = is_dir( $path . $file );

            if ( ! $is_dir )
                continue ;

            $folders[] = $file;
        }

        self::$themes = $folders;

        return self::$themes;
    }

    public static function resolve_theme_name( $theme ) {
        $themes = self::get_themes();

        if ( ! in_array( $theme, $themes ) )
            $theme = ARIADMINER_THEME_DEFAULT;

        return $theme;
    }

    public static function get_theme_url( $theme = null ) {
        if ( empty( $theme ) ) {
            $theme = Settings::get_option( 'theme' );
        }

        $theme = self::resolve_theme_name( $theme );
        $theme_url = ARIADMINER_THEMES_URL . $theme . '/adminer.css';

        return $theme_url;
    }

    public static function db_type_to_label( $type ) {
        $label = $type;

        switch ( $type ) {
            case DB_Driver::MYSQL:
            case DB_Driver::SERVER:
                $label = __( 'MySQL', 'ari-adminer' );
                break;

            case DB_Driver::SQLITE:
                $label = __( 'SQLite', 'ari-adminer' );
                break;

            case DB_Driver::POSTGRESQL:
                $label = __( 'PostgreSQL', 'ari-adminer' );
                break;
        }

        return $label;
    }
}
