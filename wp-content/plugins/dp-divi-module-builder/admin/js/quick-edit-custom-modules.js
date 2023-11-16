(function ($) {
    // we create a copy of the WP inline edit post function
    const $wp_inline_edit = inlineEditPost.edit;

    // and then we overwrite the function with our own code
    inlineEditPost.edit = function (id) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $wp_inline_edit.apply(this, arguments);

        // get the post ID
        let $post_id = 0;
        if (typeof (id) == 'object') {
            $post_id = parseInt(this.getId(id));
        }

        if ($post_id > 0) {
            const $edit_row = $('#edit-' + $post_id);
            // get the data
            let state = $('#module-' + $post_id).attr('data-value');
            if (state === 'on') {
                state = true;
            } else {
                state = false;
            }
            // populate the data
            $(':input[name="dp_dmb_quick_edit_checkbox_php_onoff"]', $edit_row).prop('checked', state);
        }
    };

})(jQuery);