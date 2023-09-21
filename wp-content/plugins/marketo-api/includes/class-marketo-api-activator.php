<?php

/**
 * Fired during plugin activation
 *
 * @link       https://applause.com
 * @since      1.0.0
 *
 * @package    Marketo_Api
 * @subpackage Marketo_Api/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Marketo_Api
 * @subpackage Marketo_Api/includes
 * @author     Tammy Shipps <tshipps@applause.com>
 */
class Marketo_Api_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
			
		// Create the table
		global $wpdb;
		$version = (new Marketo_Api)->get_version();
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'marketo_jobs';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		GFCommon::log_debug( "Adding marketo_jobs table to the database");

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			created_at datetime DEFAULT NOW() NOT NULL,
			edited_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			job_status varchar(64) NOT NULL,
			job_fields longtext NOT NULL,
			job_data longtext NOT NULL,
			lead_id bigint(20) NULL DEFAULT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
		
		dbDelta( $sql );

	}

}
