<?php
/**
 * Plugin Name:     Ultimate Member - Ads Free Cookies
 * Description:     Extension to Ultimate Member for creating ads free cookies supporting the Ezoic plugin.
 * Version:         1.0.0 development
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_Ads_Free_Cookies {

    function __construct( ) {

        add_action( 'wp',                    array( $this, 'um_ads_free_cookies' ));
        add_filter( 'um_settings_structure', array( $this, 'um_settings_structure_ads_free_cookies' ), 10, 1 );
    }

    public function um_ads_free_cookies() {

        global $current_user;

        $start_time_key = sanitize_key( UM()->options()->get( 'um_ads_free_cookies_key' ));
        if ( empty( $start_time_key )) {
            $start_time_key = 'ads_free_start_time';
        }

        $cookie = array_map( 'sanitize_text_field', explode( ',', UM()->options()->get( 'um_ads_free_cookies_name' )));
        if ( ! is_array( $cookie ) || count( $cookie ) != 2 ) {
            $cookie = array( 'um_ads_free_cookie', 'ads_free_cookie_value' );
        }

        $roles_text = array_map( 'sanitize_text_field', explode( "\n", UM()->options()->get( 'um_ads_free_cookies_roles' )));
        $ads_free_roles = array();

        if ( is_array( $roles_text )) {

            foreach( $roles_text as $text ) {
                $role_days = array_map( 'trim', explode( ':', $text ));

                if( is_array( $role_days ) && count( $role_days ) == 2 ) {
                    $ads_free_roles[$role_days[0]] = $role_days[1];
                }
            }
        }

        $roles = $current_user->roles;
        $role = array_shift( $roles );

        if ( ! empty( $role ) && array_key_exists( $role, $ads_free_roles )) {

            um_fetch_user( $current_user->ID );
            $start_time = 0;

            if ( empty( um_user( $start_time_key ) )) {

                if ( UM()->options()->get( 'um_ads_free_cookies_start' ) == '1' ) {

                    $start_time = time();
                    update_user_meta( $current_user->ID, $start_time_key, $start_time );

                    UM()->user()->remove_cache( $current_user->ID );
                    um_fetch_user( $current_user->ID );
                }

            } else {

                $start_time = um_user( $start_time_key );
            }

            $stop_time = (int)$start_time + ((int)$ads_free_roles[$role] * 86400 );

            if ( $stop_time < time()) {

                if ( isset( $_COOKIE[$cookie[0]] )) {
                    setcookie( $cookie[0], "", time() - 3600, "/" );
                }

            } else {

                if ( ! isset( $_COOKIE[$cookie[0]] )) {
                    setcookie( $cookie[0], $cookie[1], $stop_time, "/" );
                }
            }
        }
    }

    public function um_settings_structure_ads_free_cookies( $settings_structure ) {

        $settings_structure['access']['sections']['other']['fields'][] = array(
            'id'            => 'um_ads_free_cookies_key',
            'type'          => 'text',
            'size'          => 'small',
            'label'         => __( 'Ads Free Cookies - Meta Key Start time', 'ultimate-member' ),
            'tooltip'       => __( 'Name of the meta key with start time for ads free.', 'ultimate-member' )
            );

        $settings_structure['access']['sections']['other']['fields'][] = array(
            'id'            => 'um_ads_free_cookies_start',
            'type'          => 'checkbox',
            'label'         => __( 'Ads Free Cookies - Start time usage', 'ultimate-member' ),
            'tooltip'       => __( 'Click checkbox for start time for ads free from next page display by the user if meta_key empty. 
                                    Unchecked you must give the starttime as a Unix timestamp.', 'ultimate-member' )
            );

        $settings_structure['access']['sections']['other']['fields'][] = array(
            'id'            => 'um_ads_free_cookies_name',
            'type'          => 'text',
            'size'          => 'small',
            'label'         => __( 'Ads Free Cookies - Cookie Name and Value', 'ultimate-member' ),
            'tooltip'       => __( 'Comma separated', 'ultimate-member' )
            );

        $settings_structure['access']['sections']['other']['fields'][] = array(
            'id'            => 'um_ads_free_cookies_roles',
            'type'          => 'textarea',
            'size'          => 'small',
            'label'         => __( 'Ads Free Cookies - Role IDs and Number of Days', 'ultimate-member' ),
            'tooltip'       => __( 'One Role per line and RoleID and number of days colon separated.', 'ultimate-member' )
            );

        return $settings_structure;
    }

}

new UM_Ads_Free_Cookies();
