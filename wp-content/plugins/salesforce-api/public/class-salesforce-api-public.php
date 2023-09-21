<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://applause.com
 * @since      1.0.0
 *
 * @package    Salesforce_Api
 * @subpackage Salesforce_Api/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Salesforce_Api
 * @subpackage Salesforce_Api/public
 * @author     Tammy Shipps <tshipps@applause.com>
 */
class Salesforce_Api_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Salesforce_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Salesforce_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/salesforce-api-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Salesforce_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Salesforce_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/salesforce-api-public.js', array( 'jquery'), $this->version, false );

	}

	public function registerCustomRoute() 
	{

		register_rest_route( 'salesforce-api/v1', '/subscriptions/submit', array(
			'methods' => ['POST', 'GET' ],
			'callback' => array($this, 'manageSubscription'),
			'permission_callback' => '__return_true'
		) );
	}

	public function manageSubscription( WP_REST_Request $request ) 
	{

		$formParams = [];

		// Make sure we have an email address, and start our search.
		$emailAddress = $request->get_param("email");
		$ipAddress = $request->get_param("ipAddress");

		if ( empty($emailAddress) || empty($ipAddress) ) {
			GFCommon::log_error("Email address or IP address was missing - cannot proceed.");
			exit;
		}
		
        $all = $request->get_param("optout-all");
        $email = $request->get_param("optout-email");
        $phone = $request->get_param("optout-phone");
        $marketing = $request->get_param("optout-marketing");
		$accessToken = $request->get_param("accessToken");

        // This field should always be updated, to show origin of unsubscribe
        $formParams['Subscription_Landing_Page_Opt_Out__c'] = true;

        if ( $all ) {
          $formParams['Sales_Phone_Opt_Out__c'] = true;
          $formParams['Sales_Email_Opt_Out__c'] = true;
          $formParams['Sales_Message_Opt_Out__c'] = true;
          $formParams['Marketing_Opt_Out__c'] = true;
        };

        if ( $phone ) {
          $formParams['Sales_Phone_Opt_Out__c'] = true;
        };

        if ( $email ) {
          $formParams['Sales_Message_Opt_Out__c'] = true;
          $formParams['Sales_Email_Opt_Out__c'] = true;
        };

        if ( $marketing ) {
          $formParams['Marketing_Opt_Out__c'] = true;
        };

		// Find Matches
		$leads = $this->findLead($emailAddress, $accessToken, $ipAddress);
        $contacts = $this->findContact($emailAddress, $accessToken, $ipAddress);

		// Are there results?
        if ( $leads && $leads->totalSize > 0 ) {
			$ids = [];
			$records = $leads->records;
			foreach ( $records as $record ) {
			  $ids[] = $record->Id;
			}
  
			$message = $ipAddress . " || Lead:Search -- count=" . $leads->totalSize . " -- data=" . implode(", ", $ids);
			GFCommon::log_debug($message, 'sf-subscriptions');
  
			// Now we need to update any found Leads...
			foreach ( $records as $record ) {
			  GFCommon::log_debug("updateLead:: -- id=" . $record->Id . " data=" . implode(", ", $formParams), "sf-devlog");
			  $updater = $this->updateLead($record->Id, $formParams, $accessToken);
			};
  
		  } else {
			$message = $ipAddress . " || Lead:Search -- count=0 -- No leads found";
			GFCommon::log_debug($message, 'sf-subscriptions');
		  };
  
		  if ( $contacts && $contacts->totalSize > 0 ) {
			$ids = [];
			$records = $contacts->records;
			foreach ( $records as $record ) {
			  $ids[] = $record->Id;
			}
  
			$message = $ipAddress . " || Contact:Search -- count=" . $contacts->totalSize . " -- data=" . implode(", ", $ids);
			GFCommon::log_debug($message);
  
			// Now we need to update any found Contacts...
			foreach ( $records as $record ) {
			  GFCommon::log_debug("updateContact:: -- id=" . $record->Id . " data=" . implode(", ", $formParams));
			  $updater = $this->updateContact($record->Id, $formParams, $accessToken);
			};
  
		  } else {
			$message = $ipAddress . " || Contact:Search -- count=0 -- No leads found";
			GFCommon::log_debug($message, 'sf-subscriptions');
		  };
  
		  $message = 'none';
		  if ( ($leads && $leads->totalSize > 0) || ($contacts && $contacts->totalSize > 0) ) {
			$message = 'done';
		  }
  
		  $responseObject[] = [ 'status' => 200, 'message' => $message ];
		  
		  return json_encode($responseObject);

	}

	public function findLead($email, $accessToken, $ipAddress)
    {

      // We need both these things to proceed
      if ( $email && $accessToken ) {
        $url =  getenv('SALESFORCE_URL') . "/services/data/v50.0/query/";
        $query = str_replace(" ", "+", "SELECT Id, MasterRecordId, Email FROM Lead WHERE Email = '" . $email . "'");

        $headers = array(
          'Content-Type: application/json',
          'Authorization: Bearer '. $accessToken
        );

        $endpoint = $url . "?q=" . $query;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $valid = json_decode($response);
        $body = is_array($valid) ? $valid[0] : $valid; // Sometimes we get an object wrapped in an array and sometimes we don't.

        if ( isset( $body->errorCode ) ) {
          // We got an error from Salesforce, bubble it up
          GFCommon::log_error("findLead:: " . $body->errorCode . " - " . $body->message);
          return false;
        } else {
          return $body;
        }
      } else {
        // Log the issue and report error
        GFCommon::log_error("findLead:: Missing required params: email or token");
        return false;
      }
    }

    public function findContact($email, $accessToken, $ipAddress)
    {

      // We need both these things to proceed
      if ( $email && $accessToken ) {
        $url =  getenv('SALESFORCE_URL') . "/services/data/v50.0/query/";
        $query = str_replace(" ", "+", "SELECT Id, MasterRecordId, Email FROM Contact WHERE Email = '" . $email . "'");

        $headers = array(
          'Content-Type: application/json',
          'Authorization: Bearer '. $accessToken
        );

        $endpoint = $url . "?q=" . $query;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $valid = json_decode($response);
        $body = is_array($valid) ? $valid[0] : $valid; // Sometimes we get an object wrapped in an array and sometimes we don't.

        if ( isset( $body->errorCode ) ) {
          // We got an error from Salesforce, bubble it up
          GFCommon::log_error("findContact:: " . $body->errorCode . " - " . $body->message);
          return false;
        } else {
          return $body;
        }
      } else {
        // Log the issue and report error
        GFCommon::log_error("findContact:: Missing required params: email or token");
        return false;
      }
    }

    public function updateLead($id, $updateData, $accessToken)
    {
      $endpoint =  getenv('SALESFORCE_URL') . "/services/data/v50.0/sobjects/Lead/" . $id;

      $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer '. $accessToken
      );

      $ch = curl_init($endpoint);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData) );
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
      $response = curl_exec($ch);

      return json_decode($response);
    }

    public function updateContact($id, $updateData, $accessToken)
    {
      $endpoint =  getenv('SALESFORCE_URL') . "/services/data/v50.0/sobjects/Contact/" . $id;

      $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer '. $accessToken
      );

      $ch = curl_init($endpoint);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData) );
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
      $response = curl_exec($ch);

      return json_decode($response);
    }
}
