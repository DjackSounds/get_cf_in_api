<?php
/** ===============================================================================
Plugin Name: Get CF in API
Plugin URI: https://github.com/DjackSounds/get_cf_in_api
Description: Add the possibility to get all your custom fields (including ACF) with the Wordpress REST API
Version: 1
Author: Thomas Billaud
Author URI: https://thomas-billaud.com/
License: GPLv2 or later

Copyright 2011-2017  SnapCreek LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 **/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/*** Here we go ***/
add_action( 'rest_api_init', 'get_cf_add_custom_fields' );
function get_cf_add_custom_fields() {
    register_rest_field(
        'post',
        'custom_fields', //New Field Name in JSON RESPONSEs
        array(
            'get_callback'    => 'get_cf_get_custom_fields', // custom function name
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

function get_cf_get_custom_fields( $object ) {

    global $wpdb;
    $acf = false;

    $customfields = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id='". $object["id"] ."' and meta_key NOT LIKE '\_%'", OBJECT);

    foreach ( $customfields as $customfield ) {
        if ( $customfield->meta_value == "" ){
            $values[$customfield->meta_key] = false;
        }else{
            if( class_exists('acf') ){
                $values[$customfield->meta_key] = get_field($customfield->meta_key, $object["id"]);
            }else {
                $values[$customfield->meta_key] = $customfield->meta_value;
            }
        }
    }
    return $values;
}