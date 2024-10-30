<?php
/*
Plugin Name: Companion Revision Manager
Plugin URI:  http://codeermeneer.nl/portfolio/companion-revision-manager/
Description: Lightweight plugin that allows full control over post revisions
Version:     1.6.2
Author:      Papin Schipper
Author URI:  http://codeermeneer.nl
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: companion-revision-manager
Domain Path: /languages/

The WordPress plugin Companion Sitemap Generator is licensed under the GPL v2 or later.
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

// Disable direct access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Load translations
function crm_load_init() {
	load_plugin_textdomain( 'companion-revision-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_action( 'init', 'crm_load_init' );

// Add plugin to menu
function register_crm_menu_page() {

	if( current_user_can('administrator') ) {
		add_submenu_page( 'tools.php', __( 'Revision Manager', 'companion-revision-manager' ), __( 'Revisions', 'companion-revision-manager' ), 'manage_options', 'crm-settings', 'crm_frontend' );
	}
}
add_action( 'admin_menu', 'register_crm_menu_page' );

// Get current value of revisions
function crmGetRevisions() {
	if ( defined( 'WP_POST_REVISIONS' ) ) {
		return WP_POST_REVISIONS;
	}
}

// Set up the database and other stuff
function crm_install() {
	crm_database_creation(); // Db handle
	crm_clear_config_php(); // Clear definition in wp-config
}

// Create database
function crm_database_creation() {

	global $wpdb;
	global $crm_db_version;

	$crm_db_version = '1.0.0';

	// Create db table
	$table_name = $wpdb->prefix . "revision_control"; 

	$sql = "CREATE TABLE $table_name (
		id INT(9) NOT NULL AUTO_INCREMENT,
		setting VARCHAR(11) NOT NULL,
		val VARCHAR(11) NOT NULL,
		UNIQUE KEY id (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	// Database version
	add_option( "crm_db_version", "$crm_db_version" );

	// Insert data
	crm_install_data();

	// Updating..
	$installed_ver = get_option( "crm_db_version" );
	if ( $installed_ver != $crm_db_version ) update_option( "crm_db_version", $crm_db_version );

}

// Check if database table exists before creating
function crm_check_if_exists( $whattocheck ) {

	global $wpdb;
	$table_name = $wpdb->prefix . "revision_control"; 

	$rows 	= $wpdb->get_col( $wpdb->prepare( "SELECT COUNT(*) as num_rows FROM {$table_name} WHERE setting = '%s'", $whattocheck ) );
	$check 	= $rows[0];

	if( $check > 0) {
		return true;
	} else {
		return false;
	}

}

// Insert date into database
function crm_install_data() {

	// Get database table
	global $wpdb;
	$table_name = $wpdb->prefix . "revision_control"; 

	// Set default value
	$revisionDefault = crmGetRevisions();

	// Insert in db
	if( !crm_check_if_exists( 'revisions' ) ) $wpdb->insert( $table_name, array( 'setting' => 'revisions', 'val' => $revisionDefault ) );

}
register_activation_hook( __FILE__, 'crm_install' );

// Update
function crm_update_db_check() {
    global $crm_db_version;
    if ( get_site_option( 'crm_db_version' ) != $crm_db_version ) {
        crm_database_creation();
    }
}
add_action( 'upgrader_process_complete', 'crm_update_db_check' );

// Settings page
function crm_frontend() { 

	// Check user rights
	if( !current_user_can('administrator') ) {
		wp_die();
	}

	// The number of revisions currently stored
	$numOfRevs = wp_count_posts( 'revision' )->inherit; 

	// Delete revisions
	if( isset( $_POST['crmDeleteRevisions'] ) ) {

		check_admin_referer( 'crm_save_settings' );

		global $wpdb;
		$dbTable = $wpdb->prefix . "posts";
		$wpdb->delete( $dbTable, array( 'post_type' => 'revision' ) );

		$message = 2;

	}

	// Save settings
	if( isset( $_POST['submit'] ) ) {

		check_admin_referer( 'crm_save_settings' ); 

		$numOfRevs = sanitize_text_field( $_POST['numOfRevs'] );

		// Check for valid input
		if( !is_numeric( $numOfRevs ) && $numOfRevs != '' ) {

			$message = 3;

		} else {

			$disableRevs = sanitize_text_field( $_POST['disableRevs'] );

			if( isset( $_POST['disableRevs'] ) == 'on' ) {
				crm_set_revisions( '0' );
			} else {
				if( $numOfRevs != '' ) {
					crm_set_revisions( $numOfRevs );
				} else {
					crm_set_revisions( 'true' );
				}
			}

			$message = 1;

		}
	}


	// Show correct message
	if( isset( $message )  ) {

		switch ( $message ) {
			case '1':
				$text = __( 'Settings saved', 'companion-revision-manager' );
				break;
			case '2':
				$text = __( 'Succesfully deleted all revisions', 'companion-revision-manager' );
				break;
			case '3':
				$text = __( 'Number of revisions has to be a number', 'companion-revision-manager' );
				break;
		}

		echo '<div id="message" class="updated"><p>'.$text.'</p></div>';

	}

	// Get database
	global $wpdb;
	$table_name = $wpdb->prefix . "revision_control"; 

	// Get value
	$configs 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE setting = '%s'", 'revisions' ) );

	foreach ( $configs as $config ) {
		$revisionValue = $config->val;
	}

	?>
	
	<div class="wrap">

		<h1 class="wp-heading-inline"><?php _e( "Revision Manager", "companion-revision-manager" ); ?></h1>

		<p><?php _e( "Having a lot of revisions stored may affect the speed of your website, deleting and/or disabling them can speed up your site.", "companion-revision-manager" ); ?></p>

		<form method="POST">

			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( "Delete existing revisions", "companion-revision-manager" ); ?></th>
					<td>
						<button name="crmDeleteRevisions" class="button button-alt"> <?php $numOfRevs = wp_count_posts( 'revision' )->inherit; printf( esc_html__( "Delete %s revisions", "companion-revision-manager" ), $numOfRevs ); ?></button>
						<p class='description'><?php _e( "Deleting revisions cannot be undone.", "companion-revision-manager" ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( "Disable revisions", "companion-revision-manager" ); ?></th>
					<td>
						<?php
						$checked = '';
						if( $revisionValue == false OR $revisionValue == '0' ) {
							$checked = 'CHECKED';
						}
						?>
						<label><input type='checkbox' id='disableRevs' name='disableRevs' <?php echo $checked; ?> >
						<?php _e( "Will disable revisions for all post types.", "companion-revision-manager" ); ?></label>
					</td>
				</tr>

				<tr id="numberOfRevisionsBlock" <?php if( $revisionValue == false OR $revisionValue == '0' ) { echo "class='hiddenBlock'"; } ?> >
					<th scope="row"><?php _e( "Number of revisions", "companion-revision-manager" ); ?></th>
					<td>
						<label for='numOfRevs'>
							<input type='number' id= 'numOfRevs' name='numOfRevs' value='<?php echo $revisionValue; ?>'>
							<p class='description'><?php _e( "The maximum number of revisions that can be stored (per post).", "companion-revision-manager" ); ?></p>
						</label>
					</td>
				</tr>

			</table>	

			<?php wp_nonce_field( 'crm_save_settings' ); ?>	

			<?php submit_button(); ?>

		</form>

		<style>
		#numberOfRevisionsBlock.hiddenBlock {
			opacity: .5;
		}
		</style>
		<script>
		jQuery( '#disableRevs' ).change(function() {
	    	if( jQuery( this ).is( ":checked" ) ) {
	           jQuery( "#numberOfRevisionsBlock" ).addClass( 'hiddenBlock' );
	        } else {
	        	jQuery( "#numberOfRevisionsBlock" ).removeClass( 'hiddenBlock' );
	        }
	                
	    });
		</script>

	</div>

<?php }

// Update revisions
function crm_set_revisions( $value ) {

	global $wpdb;
	$table_name = $wpdb->prefix . "revision_control";

	$wpdb->query( $wpdb->prepare( "UPDATE {$table_name} SET val = %s WHERE setting = 'revisions'", $value ) );

}

// Show thank you in the footer
function crm_change_footer_admin ( ) {  
	_e( 'Thank you for using Companion Revision Manager.', 'companion-revision-manager' );
}  

if( isset( $_GET['page'] ) && $_GET['page'] == 'crm-settings' ) {
	add_filter( 'admin_footer_text', 'crm_change_footer_admin' );
} 

// Add generate sitemap link on plugin page
function crm_settings_link( $links ) { 

	$settings_link 	= '<a href="tools.php?page=crm-settings">'.__( 'Settings', 'companion-revision-manager' ).'</a>'; 
	$settings_link2 = '<a href="https://translate.wordpress.org/projects/wp-plugins/companion-revision-manager" target="_blank">'.__( 'Help us translate', 'companion-revision-manager' ).'</a>'; 

	array_unshift( $links, $settings_link ); 
	array_unshift( $links, $settings_link2 ); 

	return $links; 

}

$plugin = plugin_basename(__FILE__); 

add_filter("plugin_action_links_$plugin", 'crm_settings_link' );

// Define WP_POST_REVISIONS
function crm_settings() {

	// Get database
	global $wpdb;
	$table_name = $wpdb->prefix . "revision_control"; 

	// Get value
	$configs 	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE setting = '%s'", 'revisions' ) );

	foreach ( $configs as $config ) {
		$value = $config->val;
	}

	// Define WP_POST_REVISIONS
	if ( !defined( 'WP_POST_REVISIONS' ) ) {
		define( 'WP_POST_REVISIONS', $value );
	}

}
// add_action( 'init', 'crm_settings', 1 );
crm_settings(); // Adding it to init doesn't seem to work

/* FALLBACK FOR OLDER VERSIONS, CAN PROBABLY BE REMOVED IN FUTURE UPDATES */
// Get wp-config file
function crm_config_file() {

	// Config file
	if ( file_exists( ABSPATH . 'wp-config.php') ) {
		$conFile = ABSPATH . 'wp-config.php';
	} else {
		$conFile = dirname(ABSPATH) . '/wp-config.php';
	}

	return $conFile;

}

// Clear wp-config file for updates from older versions or predefined settings
function crm_clear_config_php() {

	// Config file
	$conFile = crm_config_file();

	// Lines to check and replace
	$revLine 		= "";
	$oldLine 		= "define('WP_POST_REVISIONS', ".crmGetRevisions()." );";

	// If line exists remove it
	if( strpos( file_get_contents( $conFile ), $oldLine ) !== false) {

        $contents = file_get_contents( $conFile );
		$contents = str_replace( $oldLine, $revLine, $contents );
		file_put_contents( $conFile, $contents );

    }

}