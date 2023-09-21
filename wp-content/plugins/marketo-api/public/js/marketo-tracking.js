
// *****************************************************************************
// These methods are for getting and setting the localStorage values that are
// consumed by our forms
// *****************************************************************************

const targetParams = [
  'mc', 'ls', 'lc', 'cc', 'utm_campaign', 'utm_medium', 'utm_source'
];

const getFilteredParams = function()  {
  var searchParams = new URLSearchParams(window.location.search);
  var filteredParams = [];

  searchParams.forEach((value,key) => {
    // Only include params that are allowed and unique
    // gclid will only ever have one value, so add it manually
    let findKey = key.replace("curr_", "");
    if ( targetParams.includes( findKey ) ) {
      if ( !filteredParams.includes( findKey ) ) {
        filteredParams[ findKey ] = value;
      };
    } else {
      if ( key == "gclid" ) {
        filteredParams[ findKey ] = value;
      }
    }
  });

  return filteredParams;
};

function checkStorage( param ) {
  return window.localStorage.getItem( param );
};

async function setStorage( param, value, prefix = '') {
  if ( prefix.length > 0 ) {
    window.localStorage.setItem( prefix + param, value );
  } else {
    window.localStorage.setItem( param, value );
  }
  window.localStorage.setItem( "lastChecked", new Date());
};

async function handleLegacyCookies() {
  var cookieNames = [ 'utm_c', 'utm_o' ];

  cookieNames.forEach((item, i) => {
    var getOldCookie = Cookies.get( item );
    getOldCookie = getOldCookie ? JSON.parse( getOldCookie ) : "";

    for (const [key, value] of Object.entries(getOldCookie)) {
      setStorage( key, value, "orig_");
      setStorage( key, value, "curr_" );
    };

  });
};

async function handleMarketoCookie() {
  var cookieName = '_mkto_trk';
  var getCookie = Cookies.get( cookieName );
  getCookie = (typeof getCookie != 'undefined') && getCookie != null ? getCookie : null;
  setStorage( '_mkto_trk', getCookie );
};

async function handleParams( currentParams ) {
  // Check if localStorage is still fresh, as in last changed less than 90 days ago
  var today = new Date();
  var lastChecked = new Date(checkStorage("lastChecked"));
  var expiresOn = new Date();
  expiresOn.setDate( lastChecked.getDate() + 90 );

  var isFresh = expiresOn > today;

  // Iterate through whitelisted params and check for original values in localStorage
  for (const [param, value] of Object.entries( currentParams )) {
    let prefixes = [ 'curr_', 'orig_' ];

    if ( param == "gclid" ) {
      // GCLID is a special case and needs special handling
      // We just re-save it time because there is no history
      setStorage( param, value );
    } else {
      // Not GCLID params go here!
      let findOriginal = checkStorage( prefixes[1] + param );
      let findCurrent = checkStorage( prefixes[0] + param );

      if ( findOriginal != null ) {
        // Data already exists
        if ( !isFresh ) {
          // If the data has expired, set the Orig to the Current
          // And set the Current to the new value in the url
          setStorage( param, findCurrent.replace('curr_',''), "orig_" );
          setStorage( param, value, "curr_" );
        } else {
          // Values have not expired, but we have new params, let's just update
          // the current values.
          setStorage( param, value, "curr_");
        }
      } else {
        // Original is empty, so save new values for both.
        setStorage( param, value, "orig_");
        setStorage( param, value, "curr_");
      };
    }

  };
};

function mktoinit() {
  // window.applause.log( "--------- init! ---------");
  handleLegacyCookies()
    .then( handleMarketoCookie() )
    .then( function() {
      let currentParams = getFilteredParams();
      let paramCount = Object.keys(currentParams).length;
      if ( paramCount > 0 ) {
        handleParams( currentParams );
      }
    })
    .then( function() {
      // Finished saving to localStorage, so clean up legacy window.cookies
      // If the cookie was made manually, we may not be able to delete it the
      // easy way, so let's just do it the vanilla way.
      document.cookie = 'utm_o=; Max-Age=0; path=/; domain=.' + window.location.hostname;
      document.cookie = 'utm_c=; Max-Age=0; path=/; domain=.' + window.location.hostname;
    });
};
