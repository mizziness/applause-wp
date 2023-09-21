<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://applause.com
 * @since             1.0.0
 * @package           Salesforce_Api
 *
 * @wordpress-plugin
 * Plugin Name:       Salesforce API
 * Plugin URI:        https://applause.com
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Tammy Shipps
 * Author URI:        https://applause.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       salesforce-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SALESFORCE_API_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-salesforce-api-activator.php
 */
function activate_salesforce_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-salesforce-api-activator.php';
	Salesforce_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-salesforce-api-deactivator.php
 */
function deactivate_salesforce_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-salesforce-api-deactivator.php';
	Salesforce_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_salesforce_api' );
register_deactivation_hook( __FILE__, 'deactivate_salesforce_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-salesforce-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_salesforce_api() {

	$plugin = new Salesforce_Api();
	$plugin->run();

}
run_salesforce_api();

function getUserIP() {
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function subscribeForm($atts) {

	$endpoint = getenv('SALESFORCE_URL');
	$clientId = getenv('SALESFORCE_ID');
	$clientSecret = getenv('SALESFORCE_SECRET');
	$password = getenv('SALESFORCE_PASSWORD') . getenv('SALESFORCE_SECURITY');
	$email = getenv('SALESFORCE_EMAIL');

	$innerdata = [
		'grant_type' => 'password',
		'client_id' => $clientId,
		'client_secret' => $clientSecret,
		'username' => $email,
		'password' => $password,
	];

	$data = [ 'form_params' => $innerdata ];

	$getForm = wp_remote_post( $endpoint . "/services/oauth2/token", array(
		'method' => 'POST',
		'body' => $innerdata,
		'timeout' => 60
	) );

	$responseBody = null;
	$accessToken =  null;
	$userIP = null;

	if ( !empty($getForm['response']['code']) ) {
		$responseBody = json_decode($getForm['body']);
		$accessToken = $responseBody->access_token;
		$userIP = getUserIP();
	}

	$template = '<div class="tw-container">
			<div id="message" class="" role="alert" aria-live="polite">
				<p class="tw-p-2 tw-mb-0"></p>
			</div>
			<form id="manage-subscriptions-form" action="/wp-json/salesforce-api/v1/subscriptions/submit" method="POST" autocomplete="off">
				<input type="hidden" name="accessToken" value="' . $accessToken . '">
				<input type="hidden" name="ipAddress" value='. $userIP . '">
				<div id="manage-subscriptions" class="tw-text-left">
					<div class="form-inner tw-p-6 tw-text-left tw-bg-gray-100 tw-border tw-border-gray-300 tw-rounded">
						<label for="email">Enter your email address:</label><br>
						<input id="email" name="email" class="tw-px-2 tw-py-1 tw-border tw-rounded-sm" type="email" value="" placeholder="your@email.here">
						<br><br>

						<p class="tw-mb-2">Please select the type of communications you would like to stop receiving from us:
						</p>
						<div id="choices" class="tw-p-2">
							<div class="tw-ml-4">
								<label for="optout-marketing" class="tw-mb-2">
									<input class="tw-border" type="checkbox" id="optout-marketing" name="optout-marketing">
									Marketing updates including invitations to webinars, resource offers, research reports, and
									newsletters
								</label>

								<div id="options">
									<label for="optout-sales">
										<input class="tw-border" type="checkbox" id="optout-sales" name="optout-sales">
										All one-to-one communications &amp; promotions
									</label>
									<br>
									<div class="tw-ml-4">
										<label for="optout-email">
											<input class="tw-border" type="checkbox" id="optout-email" name="optout-email">
											One-to-one emails
										</label>
										<br><label for="optout-phone">
											<input class="tw-border" type="checkbox" id="optout-phone" name="optout-phone">
											One-to-one phone calls
										</label>
									</div>
								</div>
							</div>
							<br>
							<p class="tw-mb-2">Or select here to opt out of all further communications from Applause:</p>
							<div class="tw-ml-4">
								<input class="tw-border" type="checkbox" id="optout-all" name="optout-all">
								<label for="optout-all">Select all</label><br>
							</div>
						</div>
						<div data-lastpass-icon-root="true" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;">
						</div>
					</div>
				</div><input type="checkbox" id="contact_me_by_phone_only" name="contact_me_by_phone_only" value="1" style="display:none !important" tabindex="-1" autocomplete="off">
				<div class="tw-grid tw-items-center">
					<div class="tw-text-center">
						<button id="submit" type="submit" name="submit" data-category="Form Submit" data-action="Button" data-label="https://www.applause.com/subscriptions" class="button is-secondary submit-track tw-inline-block tw-px-4 tw-mt-4 tw-mr-4">Unsubscribe</button>
						<button id="reset" type="reset" name="reset" class="button is-secondary is-outlined tw-inline-block tw-px-4 tw-mt-4 tw-text-gray-700 tw-border-gray-500">Clear</button>
					</div>
					<div class="tw-mt-4 tw-text-xs tw-text-center">
						<a class="hover:tw-no-underline tw-text-blue-500 tw-underline" href="/terms-of-use">Terms &amp;
							Conditions</a> |
						<a class="hover:tw-no-underline tw-text-blue-500 tw-underline" href="/privacy-policy">Privacy Policy</a>
					</div>
				</div>
			</form>
			<div id="overlay" class="tw-absolute tw-w-full tw-h-full" role="alert" aria-busy="true" aria-label="Loading, please wait...">
				<div class="lds-circle">
					<div></div>
				</div>
			</div>
		</div>';

	return $template;
}

add_shortcode('subscription_form', 'subscribeForm');
