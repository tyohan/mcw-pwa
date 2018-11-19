<?php
/*
Contributors: tyohan,ivankristianto
Plugin Name:  Minimum Configuration WordPress PWA
Plugin URI:   https://github.com/wp-id/mcw-pwa
Description:  WordPress plugin to optimize loading performance with minimum configuration using PWA approach
Version:      0.2.0
Author:       WP-ID
Author URI:   https://wp-id.org
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  mcwpwa
Domain Path:  /languages
Contributors: tyohan,ivankristianto
Minimum Configuration WordPress PWA is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Minimum Configuration WordPress PWA is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Minimum Configuration WordPress PWA..
*/

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
define( 'MCW_PWA_URL', plugin_dir_url( __FILE__ ) );
define( 'MCW_PWA_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCW_PWA_OPTION', 'mcw_option_group' );
define( 'MCW_SECTION_PERFORMANCE', 'mcw_option_performance' );
define( 'MCW_SECTION_PWA', 'mcw_option_pwa' );
define( 'MCW_PWA_SETTING_PAGE', 'mcw_setting_page' );

require_once MCW_PWA_DIR . 'includes/service_workers/MCW_PWA_Service_Worker.php';
require_once MCW_PWA_DIR . 'includes/MCW_PWA_Settings.php';
require_once MCW_PWA_DIR . 'includes/MCW_PWA_LazyLoad.php';
require_once MCW_PWA_DIR . 'includes/MCW_PWA_Assets.php';
require_once MCW_PWA_DIR . 'includes/MCW_PWA_Monitor.php';

MCW_PWA_Settings::instance();
MCW_PWA_Service_Worker::instance();
MCW_PWA_LazyLoad::instance();
MCW_PWA_Assets::instance();
MCW_PWA_Monitor::instance();

/**
 * Run when plugin activate
 */
function activate() {
	MCW_PWA_Service_Worker::instance()->activate();
}
register_activation_hook( __FILE__, 'activate' );

/**
 * Run when plugin deactivate
 */
function deactivate() {
	MCW_PWA_Service_Worker::instance()->deactivate();
	MCW_PWA_LazyLoad::instance()->deactivate();
	MCW_PWA_Assets::instance()->deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate' );

/**
 * Run when plugin uninstall
 */
function uninstall() {
	MCW_PWA_Service_Worker::instance()->uninstall();
	MCW_PWA_LazyLoad::instance()->uninstall();
	MCW_PWA_Assets::instance()->uninstall();
}
register_uninstall_hook( __FILE__, 'uninstall' );

/**
 * Run Service worker.
 */
function mcw_init() {
	if ( ! is_admin() ) {

		MCW_PWA_Service_Worker::instance()->run();

		// Disable some performance enhancement on AMP.
		// Try to support AMP for WP plugin https://github.com/ahmedkaludi/Accelerated-Mobile-Pages.
		// use AMPFORWP_AMP_QUERY_VAR.
		if ( defined( 'AMP_QUERY_VAR' ) && ! get_query_var( AMP_QUERY_VAR, false ) ) {
			MCW_PWA_LazyLoad::instance()->run();
			MCW_PWA_Assets::instance()->run();
		}
	}

	MCW_PWA_Monitor::instance()->run();
}

add_action( 'parse_query', 'mcw_init' );


