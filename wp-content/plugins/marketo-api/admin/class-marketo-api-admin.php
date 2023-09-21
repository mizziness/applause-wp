<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://applause.com
 * @since      1.0.0
 *
 * @package    Marketo_Api
 * @subpackage Marketo_Api/admin
 */

use Crontrol\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use WPMailSMTP\Geo;
use WPMailSMTP\Helpers\DB;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Marketo_Api
 * @subpackage Marketo_Api/admin
 * @author     Tammy Shipps <tshipps@applause.com>
 */
class Marketo_Api_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Marketo_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Marketo_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/marketo-api-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Marketo_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Marketo_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/marketo-api-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function gf_get_field_by_label( $form, $label ) {
		foreach( $form['fields'] as $field ) {
			if( $field->label == $label ) {
				return $field;
			}
		}
		return false;
	}

	/**
	 * Get form submission data for Marketo lead parsing
	 *
	 * @param [type] $entry
	 * @param [type] $form
	 * @return void
	 */
	public function afterMarketoSubmission( $entry, $form ) {

		$fields = $form['fields'];

		$fieldData = [
			'firstName'				=> null,
			'lastName'				=> null,
			'title' 				=> null,
			'company'				=> null,
			'of_Employees__c'		=> null,
			'email'					=> null,
			'phone'					=> null,
			'country'				=> null,
			'marketingLeadNotes'	=> null,
			'comments'				=> null,
			'formSubmitIntentLatest' => $form['formSubmitIntentLatest'],
			'formSubmitIntentOriginal' => $form['formSubmitIntentLatest']
		];

		foreach ($fields as $field) {
			$type = $field['label'];

			if ( $type = "First Name" ) {  $fieldData['firstName'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "Last Name" ) {  $fieldData['lastName'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "Title" ) {  $fieldData['title'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "Company Name" ) {  $fieldData['company'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "First Name" ) {  $fieldData['of_Employees__c'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "Company Email" ) {  $fieldData['email'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "Number of Employees" ) {  $fieldData['phone'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "Country" ) {  $fieldData['country'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "How Did You Find Us?" ) {  $fieldData['marketingLeadNotes'] = $_POST['input_' . $field['id'] ];  }
			if ( $type = "How Can We Help?" || $type == "Comments" ) {  $fieldData['comments'] = $_POST['input_' . $field['id'] ];  }
		}

		$jobData = [
			'mktTrk' 				=> $_POST['_mkto_trk'],
			'submittedFrom' 		=> $entry['source_url'],
			'formSubmitIntent' 		=> $form['formSubmitIntentLatest'],
			'gDPRFormConsent' 		=> empty($_POST['input_26']) ? null : $_POST['input_26'],
		];

		GFCommon::log_debug( "==>> Creating a new job");

		global $wpdb;
		$table_name = $wpdb->prefix . 'marketo_jobs';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		$wpdb->insert( $table_name, array(
			'edited_at' => date('Y-m-d H:i:s'), 
			'job_status' => 'new', 
			'job_fields' => json_encode( $fields ),
			'job_data' => json_encode( $jobData )
		));

		// Add custom hidden fields back to the entry
		$hiddenFields = array(
			'Most_Recent_Inbound_Marketing_Code__c'		=> $_POST['Most_Recent_Inbound_Marketing_Code__c'],
			'Most_Recent_Lead_Source__c' 				=> $_POST['Most_Recent_Lead_Source__c'],
			'Most_Recent_Location_Code__c' 				=> $_POST['Most_Recent_Location_Code__c'],
			'Most_Recent_Cost_Code__c' 					=> $_POST['Most_Recent_Cost_Code__c'],
			'Original_Inbound_Marketing_Code__c' 		=> $_POST['Original_Inbound_Marketing_Code__c'],
			'Original_Lead_Source__c' 					=> $_POST['Original_Lead_Source__c'],
			'Original_Location_Code__c' 				=> $_POST['Original_Location_Code__c'],
			'Original_Cost_Code__c' 					=> $_POST['Original_Cost_Code__c'],
			'curr_utm_campaign__c' 						=> $_POST['curr_utm_campaign__c'],
			'curr_utm_medium__c' 						=> $_POST['curr_utm_medium__c'],
			'curr_utm_source__c' 						=> $_POST['curr_utm_source__c'],
			'orig_utm_campaign__c' 						=> $_POST['orig_utm_campaign__c'],
			'origutmmedium' 							=> $_POST['origutmmedium'],
			'orig_utm_source__c' 						=> $_POST['orig_utm_source__c'],
		);

		$meta = array_merge( $jobData, $hiddenFields );

		foreach ($meta as $key => $value) {
			$checkEntry = rgar($entry, $key);
			if ( empty($checkEntry) ) {
				gform_add_meta( $entry['id'], $key, $value, $form['id'] );
			}
		}

		GFAPI::update_entry( $entry );
		return $entry;
	}

	/**
	 * Add Custom Settings to the Form
	 *
	 * @param [type] $fields
	 * @param [type] $form
	 * @return void
	 */
	public function addCustomSettings ( $fields, $form ) {
		$fields['form_options']['fields'][] = array( 
			'type' => 'select', 
			'name' => 'formSubmitIntentLatest',
			'label' => 'Intentions',
			'description' => 'Choose the "intention" you want to be passed to Marketo as the indicator of the action the user took.',
			'choices'    => array(
				array(
					'label' => 'Contact-Us', 'value' => 'Contact-Us'
				),
				array(
					'label' => 'Pricing-Inquiry', 'value' => 'Pricing-Inquiry'
				),
				array(
					'label' => 'Special-Inquiry', 'value' => 'Special-Inquiry'
				),
				array(
					'label' => 'Resource-Access', 'value' => 'Resource-Access'
				),
				array(
					'label' => 'Subscribe-Newsletter', 'value' => 'Subscribe-Newsletter'
				),
				array(
					'label' => 'Subscribe-Blog', 'value' => 'Subscribe-Blog'
				),
				array(
					'label' => 'Event-Registration', 'value' => 'Event-Registration'
				),
			)
		);

		$fields['form_options']['fields'][] = array( 
			'type' => 'radio', 
			'name' => 'hideLinks',
			'label' => 'Hide Links',
			'description' => 'Hide the "Privacy Policy" and "Terms of Use" links?',
			'horizontal'    => true,
			'default_value' => 0,
			'choices'    => array(
				array(
					'label' => 'Hide Links', 'value' => 1
				),
				array(
					'label' => 'Show Links', 'value' => 0
				)
			)
		);

		return $fields;
	}

	public function customEntryView( $form, $entry ) {

		$html = "";
		$hiddenFields = array(
			'Most_Recent_Inbound_Marketing_Code__c',
			'Most_Recent_Lead_Source__c',
			'Most_Recent_Location_Code__c',
			'Most_Recent_Cost_Code__c',
			'Original_Inbound_Marketing_Code__c',
			'Original_Lead_Source__c',
			'Original_Location_Code__c',
			'Original_Cost_Code__c',
			'curr_utm_campaign__c',
			'curr_utm_medium__c',
			'curr_utm_source__c',
			'orig_utm_campaign__c',
			'origutmmedium',
			'orig_utm_source__c',
		);

		$html .= '
		<br><br>
		<table cellspacing="0" class="entry-details-table id="extra-details">
			<thead>
				<tr>
					<th>Marketing Details</th>
				</tr>
			</thead>
			<tbody>';

		foreach ($hiddenFields as $field) {

			$meta_value = gform_get_meta( $entry['id'], $field );
			if ( empty($meta_value) ) {
				$meta_value = "--";
			}

			$html .= '
			<tr>
			<td colspan="1" style="font-size: 13px; line-height: 165%; padding: 12px 17px;"><strong>' . $field . '</strong></td>
			<td colspan="1" style="font-size: 13px; line-height: 165%; padding: 12px 17px;">' . $meta_value . '</td>
			</tr>';
			
		}
		
		$html .= '
			</tbody>
		</table>';

		echo($html);
	}

	/**
	 * Checks the marketo job table for any jobs that have not been 
	 * run yet and processes them
	 *
	 * @return void
	 */
	public function sendMarketoData() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'marketo_jobs';
		$host = getenv('MARKETO_HOST_KEY');

		GFCommon::log_debug("==>> Checking for jobs that need to be run");
		
		$newJobs = $wpdb->get_results("SELECT * FROM $table_name WHERE `job_status` != 'done'");

		foreach ($newJobs as $job) {
			GFCommon::log_debug("Running job ID " . $job->id . "...");

			$rawData = json_decode($job->job_data);
			$rawFields = json_decode($job->job_fields);
			$mktoTrk = $rawData->mktTrk;

			// Create the lead in Marketo
			$lead = $this->createLead($rawFields, $rawData, $host);

			if ( empty($lead['leadId']) ) {
				wp_mail(
					'tshipps@applause.com',
					"Marketo API Failure - Create Lead",
					"We tried to create/update a lead for job ID#" . $job->id . " but the API returned an error."
				);
				return false;
			}

			// If a leadID came back then we are all set
			$leadId = $lead['leadId'];
			//$savedLead = $wpdb->get_row("SELECT * FROM $table_name WHERE `lead_id` = $leadId");

			// Update our DB with the lead ID and new status
			$wpdb->update( $table_name, array( 'job_status' => 'processing', 'lead_id' =>  $leadId),
				array( 'id' => $job->id ),
				array( '%s', '%d' ), 
				array( '%s' )
			);

			// Now associate the lead
			$jobData = json_decode($job->job_data);
			$mktoTrk = $jobData->mktTrk;
			$associate = $this->associateLead($leadId, $mktoTrk, $host);

			if ( empty($associate['success']) ) {
				wp_mail(
					'tshipps@applause.com',
					"Marketo API Failure - Associate Lead",
					"We tried to associate a lead for job ID#" . $job->id . " but the API returned an error."
				);
				return false;
			}

			// Now add activity data to the lead
			$host 			= getenv('MARKETO_HOST_KEY');
			$customActivity = getenv('MARKETO_CUSTOM_ACTIVITY_TYPE_ID');
			$formIntent 	= $jobData->formSubmitIntent;
			$submittedFrom	= $jobData->submittedFrom;
			$urlData = array( 'access_token' => $this->newToken() );

			$activity = $this->addActivity( $host, $customActivity, $leadId, $submittedFrom, $formIntent, $urlData );

			if ( empty($activity['success']) ) {
				wp_mail(
					'tshipps@applause.com',
					"Marketo API Failure - Activity",
					"We tried to add activity to a lead for job ID#" . $job->id . " but the API returned an error."
				);
				return false;
			}

			$wpdb->update( $table_name, array( 'job_status' => 'done' ),
				array( 'id' => $job->id ),
				array( '%s', '%d' ), 
				array( '%s' )
			);
		}

		return true;
	}

	/**
	 * Generates a token for Marketo's API
	 *
	 * @return void
	 */
    public function newToken()
    {
		GFCommon::log_debug( "==>> Getting a new Marketo Token") ;
        
        $host 			= getenv('MARKETO_HOST_KEY');
        $clientId 		= getenv('MARKETO_CLIENT_ID');
        $clientSecret 	= getenv('MARKETO_CLIENT_SECRET');
		$customActivity = getenv('MARKETO_CUSTOM_ACTIVITY_TYPE_ID');
        $result 		= null;

		$urlData = array(
			'grant_type' => 'client_credentials',
			'client_id' => $clientId,
			'client_secret' => $clientSecret
		);
		$url = "https://" . $host . ".mktorest.com/identity/oauth/token?" . http_build_query($urlData);

		GFCommon::log_debug( 'gform_after_submission: body => ' . print_r( $urlData, true ) );
		$response = wp_remote_post( $url, array( 'body' => $urlData ) );
		GFCommon::log_debug( 'gform_after_submission: response => ' . print_r( $response, true ) );

		if ( $response['response']['code'] == 200 ) {
			// Success, we have a token! 
			$tokenData = json_decode($response['body']);
			$accessToken = $tokenData->access_token;
			$result = $accessToken;
		} else {
			// if ( getenv('ENVIRONMENT' == 'production' ) {
				wp_mail(
					'tshipps@applause.com',
					"Marketo API Failure - Token",
					"We tried to get a token from the Marketo API but it failed to return a token."
				);
			// }
		}

        return $result;
    }

	/**
	 * Creates or updates a lead in Marketo
	 *
	 * @param [type] $fields
	 * @param [type] $host
	 * @return void
	 */
	public function createLead($fields, $data, $host)
    {
        GFCommon::log_debug("Trying to create/update a lead...");
        $responseData = null;

		try {
			$guzzleClient = new \GuzzleHttp\Client();
            $urlData = array('access_token' => $this->newToken());

            $url = "https://" . $host . ".mktorest.com/rest/v1/leads.json?" . http_build_query($urlData);

            $postData = array(
              'action' => "createOrUpdate",
              'lookupField' => "email",
              'input' => array(
                $fields
              )
            );

            $reqOpts = new \GuzzleHttp\RequestOptions();
            $response = $guzzleClient->request(
                'post', $url, [ $reqOpts::JSON => $postData ]
            );

            $result = json_decode($response->getBody()->getContents(), true);
            $status = isset($result['success']) ? $result['success'] : false;

            $res = [
              'statusCode' => $response->getStatusCode(),
              'reason' => $response->getReasonPhrase(),
              'success' => $status,
              'urlData' =>  $urlData
            ];

            if (!empty($result['result']) && !empty($result['result'][0]['id'])) {
                $res['result'] = $result['result'];
                $res['leadId'] = $result['result'][0]['id'];
                $res['actionStatus'] = $result['result'][0]['status'];
            }

            if (isset($res['leadId']) && $res['leadId'] != '0') {
                // If the Lead ID exists, then we were successful
                GFCommon::log_debug("Lead #" . $res['leadId'] . " returned from the Marketo API!");
            }
            $responseData =  $res;

        } catch (RequestException $e) {
            $result = json_decode($e->getResponse()->getBody());

            if (!is_string($result)) {
                $result = var_export($result, true);
            }

            $responseData = false;
        }

        GFCommon::log_debug("Finished trying to create new lead.");
        return $responseData;
    }

	public function associateLead($leadId, $mktoTrk, $host)
    {
        GFCommon::log_debug("==> Begin associating lead #" . $leadId);

		global $wpdb;
		$table_name = $wpdb->prefix . 'marketo_jobs';
		
		$job = $wpdb->get_results("SELECT * FROM $table_name WHERE `lead_id` = $leadId");
		$host = getenv('MARKETO_HOST_KEY');

        try {
            $guzzleClient = new \GuzzleHttp\Client();
            $urlData = array(
              'access_token' => $this->newToken(),
              'cookie' => $mktoTrk
            );

            $url = "https://" . $host . ".mktorest.com/rest/v1/leads/" . $leadId . "/associate.json?" . http_build_query($urlData);

            $reqOpts = new \GuzzleHttp\RequestOptions();
            $response = $guzzleClient->request(
                'post', $url, [ $reqOpts::JSON => [] ]
            );

            $result = json_decode($response->getBody()->getContents(), true);
            GFCommon::log_debug("END: Associated lead #" . $leadId);

            return [
              'statusCode' => $response->getStatusCode(),
              'reason' => $response->getReasonPhrase(),
              'requestId' => $result['requestId'],
              'result' => $result['result'],
              'success' => $result['success'],
              'urlData' =>  $urlData
            ];

        } catch (RequestException $e){
            $result = json_decode($e->getResponse()->getBody());
            if (!is_string($result)) {
                $result = var_export($result, true);
            }

            GFCommon::log_error("Error associating lead.");
            GFCommon::log_error("Error: " . var_export($result, true));
        }

        return $result;
    }

	/**
     * Add activity to Marketo Lead
     *
     * @param $host             Hostname
     * @param $activityTypeId   Activity ID type from Marketo
     * @param $leadId           Lead ID
     * @param $submittedFrom    SubmittedFrom
     * @param $formSubmitIntent Form Intent
     * @param $urlData          Url Data
     *
     * @return $result
     */
    public function addActivity(
        $host,
        $activityTypeId,
        $leadId,
        $submittedFrom,
        $formSubmitIntent,
        $urlData = null
    ) {
        GFCommon::log_debug("BEGIN: Adding activity to lead #" . $leadId);

        try {
            $guzzleClient = new \GuzzleHttp\Client();

            if ($urlData == null) {
                $urlData = array(
                  'access_token' => $this->newToken()
                );
            }
            $url = "https://" . $host . ".mktorest.com/rest/v1/activities/external.json?" . http_build_query($urlData);

            $date = new \DateTime('now');
            $dateTime = $date->format('c');

            // primaryAttributeValue is limited to 255 chars and will error
            // remove any query string params and truncate if necessary
            $urlString = strtok( $submittedFrom, "?");
            $submittedFrom = (strlen($urlString) > 255) ? substr($urlString, 0, 252) . '...' : $urlString;

            $postData = array(
              'input' => array([
                'activityDate' => $dateTime,
                'activityTypeId' => $activityTypeId,
                'leadId' => $leadId,
                'primaryAttributeValue' => $submittedFrom,
                'attributes' => array([
                  'apiName' => 'formIntent',
                  'value' => $formSubmitIntent
                ])
              ])
            );

            $reqOpts = new \GuzzleHttp\RequestOptions();
            $response = $guzzleClient->request(
                'post', $url, [ $reqOpts::JSON => $postData ]
            );

            $result = json_decode($response->getBody()->getContents(), true);

            GFCommon::log_debug( "END: Added activity to lead #" . $leadId );

            return [
              'statusCode' => $response->getStatusCode(),
              'reason' => $response->getReasonPhrase(),
              'result' => $result['result'],
              'success' => $result['success'],
              'activityId' => $result['result'][0]['id'],
              'actionStatus' => $result['result'][0]['status']
            ];

        } catch (RequestException $e){
            $result = json_decode($e->getResponse()->getBody());
            if (!is_string($result)) {
                $result = var_export($result, true);
            }
            GFCommon::log_error("Error adding activity: " . $result);
            GFCommon::log_error("Error: " . $result);
        }

        return $result;
    }
	
}
