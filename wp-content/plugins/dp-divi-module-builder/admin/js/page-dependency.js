(function ($) {
    'use strict';
    /*
     * Prevent unwanted characters on the dependency name
     */
    $(document.body).on('keydown', '.dp_dmb_external_name > div.cmb-td > input', function (e) {
        if (e.key.replace(/[^a-z0-9_]+/, "") === "") {
            e.preventDefault();
        }
    });

})(jQuery);

