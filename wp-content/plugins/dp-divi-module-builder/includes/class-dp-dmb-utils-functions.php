<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    dp-divi-odule-builder
 * @subpackage dp-divi-odule-builder/includes
 * @author     DiviPlugins <support@divipugins.com>
 */
class DP_DMB_Utils_Functions {

	public function __construct() {

	}

	/**
	 * Create the modules files directory
	 *
	 * @since    2.0.0
	 */
	public function set_module_files_dir() {
		$wp_upload_dir = wp_upload_dir();
		$dirs          = array(
			'/dmb/modules',
			'/dmb/export',
			'/dmb/css',
			'/dmb/js'
		);
		foreach ( $dirs as $dir ) {
			( wp_mkdir_p( $wp_upload_dir['basedir'] . $dir ) ) ? $this->dmb_write_log( 'Success create dir ' . $wp_upload_dir['basedir'] . $dir ) : $this->dmb_write_log( 'Fail create dir ' . $wp_upload_dir['basedir'] . $dir );
			$this->write_file( $wp_upload_dir['basedir'] . $dir . '/index.php', '<?php // Silence is golden.' );
		}
		$this->write_file( $wp_upload_dir['basedir'] . '/dmb/index.php', '<?php // Silence is golden.' );
		$this->write_file( $wp_upload_dir['basedir'] . '/dmb/.htaccess', 'IndexIgnore *' );
	}

	/**
	 * Write info to the log file.
	 *
	 * @since 2.0.0
	 */
	public function dmb_write_log( $log ) {
		$debug_file = DPDMB_MODULES_DIR . '/dmb-debug.log';
		if ( file_exists( $debug_file ) ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				// phpcs:ignore
				error_log( current_time( 'mysql' ) . " | " . print_r( $log, true ) . "\r\n", 3, $debug_file );
			} else {
				// phpcs:ignore
				error_log( current_time( 'mysql' ) . " | " . $log . "\r\n", 3, $debug_file );
			}
		}
	}

	/**
	 * Write file content
	 *
	 * @param $file_path
	 * @param $file_content
	 *
	 * @since    2.0.0
	 */
	public function write_file( $file_path, $file_content ) {
		// phpcs:ignore
		$file = fopen( $file_path, "w" );
		if ( $file !== false ) {
			if ( wp_is_writable( $file_path ) ) {
				// phpcs:ignore
				fwrite( $file, $file_content );
				// phpcs:ignore
				( fclose( $file ) ) ? $this->dmb_write_log( 'Success on write and close file path ' . $file_path ) : $this->dmb_write_log( 'Error on close file path ' . $file_path );
			} else {
				$this->dmb_write_log( 'Non writable file path ' . $file_path );
			}
		} else {
			$this->dmb_write_log( 'Error on open file path ' . $file_path );
		}
	}

	/**
	 * Remove file
	 *
	 * @since    2.0.0
	 */
	public function remove_file( $file_path ) {
		if ( file_exists( $file_path ) ) {
			( unlink( $file_path ) ) ? $this->dmb_write_log( 'Success on remove file path ' . $file_path ) : $this->dmb_write_log( 'Error on remove file path ' . $file_path );
		}
	}

	/**
	 * Fix backslashes issue
	 *
	 * @since 2.0.0
	 */
	public function add_backslashes( $content ) {
		return str_replace( '\\', '\\\\', $content );
	}

	/**
	 * Get publish modules data (id, name, php)
	 *
	 * @since 2.0.0
	 */
	public function get_modules() {
		$modules_data = array();
		$modules      = new WP_Query( array(
			'posts_per_page' => - 1,
			'post_type'      => 'dp_custom_modules',
			'post_status'    => 'publish'
		) );
		if ( $modules->have_posts() ) {
			while ( $modules->have_posts() ) {
				$modules->the_post();
				$modules_data[] = array(
					'id'   => get_the_ID(),
					'name' => get_the_title(),
					'php'  => get_post_meta( get_the_ID(), '_dp_dmb_htmlbox_checkbox_php_onoff', true )
				);
			}
		}
		wp_reset_postdata();

		return $modules_data;
	}

	/**
	 * Get draft modules data (id, name, php)
	 *
	 * @since 2.0.0
	 */
	public function get_draft_modules() {
		$modules_data = array();
		$modules      = new WP_Query( array(
			'posts_per_page' => - 1,
			'post_type'      => 'dp_custom_modules',
			'post_status'    => 'draft'
		) );
		if ( $modules->have_posts() ) {
			while ( $modules->have_posts() ) {
				$modules->the_post();
				$modules_data[] = array(
					'id'   => get_the_ID(),
					'name' => get_the_title(),
					'php'  => get_post_meta( get_the_ID(), '_dp_dmb_htmlbox_checkbox_php_onoff', true )
				);
			}
		}
		wp_reset_postdata();

		return $modules_data;
	}

	/**
	 * Check if the dmb folder exist
	 *
	 * @since 2.0.0
	 */
	public function check_dmb_folder_existence() {
		return is_dir( DPDMB_MODULES_DIR );
	}

	/**
	 * Check the license status on the DiviPlugins store
	 *
	 * @return boolean
	 */
	public function check_license() {
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => get_option( 'dp_dmb_license_key' ),
			'item_id'    => DPDMB_ITEM_ID,
			'url'        => home_url()
		);
		$response   = wp_remote_post( DPDMB_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );
		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $license_data->success === true ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function send_json_response( $status = 0, $message = "", $data = [] ) {
		if ( ! empty( $message ) ) {
			$this->dmb_write_log( $message );
		}

		wp_send_json( array(
			'status'  => $status,
			'message' => $message,
			'data'    => $data
		) );
	}

}
