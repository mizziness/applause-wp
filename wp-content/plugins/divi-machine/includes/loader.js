// External Dependencies
import $ from 'jquery';

// Internal Dependencies
import fields from './fields';

$(window).on('et_builder_api_ready', (event, API) => {
  API.registerModalFields(fields);
});
