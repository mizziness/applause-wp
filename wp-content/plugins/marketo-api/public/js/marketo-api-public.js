(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	$(function() {

		mktoinit();

		const shortcodes = [
			'curr_mc', 'curr_ls', 'curr_lc', 'curr_cc', 'orig_mc', 'orig_ls', 'orig_lc', 'orig_cc',
			'curr_utm_campaign', 'curr_utm_medium', 'curr_utm_source', 'orig_utm_campaign', 'orig_utm_medium', 'orig_utm_source',
			'gclid'
		];

		let goLinks = $("a[href^='https://go.applause.com']");
		
		$(goLinks).each(function(){
			$(this).attr('rel', 'noopener');
			
			// Save original href for easier parsing and just in case
			if ( !$(this).attr('data-href') ) {
				$(this).attr('data-href', $(this).attr('href')).attr('href', '');
			};
		
			//let params = JSON.parse(currentCookie);
			//let keys = Object.keys(window.localStorage);
			let url = new URL($(this).attr('data-href'));
		
			shortcodes.forEach((key) => {
				let value = localStorage.getItem(key);
				let cleanedKey = key.replaceAll('curr_', '');
				if ( value != null && value != "" ) {
					url.searchParams.append(cleanedKey, value);
				}
			});
		
			// Set the href to the old value + the cookie's parameters
			$(this).attr('href', url.toString());
		});

		const hiddenValues = [
			['Most_Recent_Inbound_Marketing_Code__c', 'curr_mc'],
			['Most_Recent_Lead_Source__c', 'curr_ls'],
			['Most_Recent_Location_Code__c', 'curr_lc'],
			['Most_Recent_Cost_Code__c', 'curr_cc'],
			['Original_Inbound_Marketing_Code__c', 'orig_mc'],
			['Original_Lead_Source__c', 'orig_ls'],
			['Original_Location_Code__c', 'orig_lc'],
			['Original_Cost_Code__c', 'orig_cc'],
			['curr_utm_campaign__c', 'curr_utm_campaign'],
			['curr_utm_medium__c', 'curr_utm_medium'],
			['curr_utm_source__c', 'curr_utm_source'],
			['orig_utm_campaign__c', 'orig_utm_campaign'],
			['origutmmedium', 'orig_utm_medium'],
			['orig_utm_source__c', 'orig_utm_source'],
			['GCLID__c', 'gclid'],
			['_mkto_trk', '_mkto_trk'],
			['successAction', 'successAction'],
			['redirectUrl', 'redirectUrl']
		];

		let htmlBlock = "";

		$("form.marketo-form").each(function(){

			hiddenValues.forEach( element => {
				let key = element[0];
				let value = element[1];
				let data = localStorage.getItem( value ) || "";

				let newInput = `<input id="${value}" class="${key}" data-sc="${value}" name="${key}" readonly type="hidden" value="${data}"></input>`;
				htmlBlock += newInput;
			});

			$(this).find(".gform_fields").prepend( htmlBlock );

			let mkto = Cookies.get('_mkto_trk');
			if ( mkto != null && mkto != "" ) {
				$("input#_mkto_trk").val( mkto );
			}

		});

	});


})( jQuery );
