(function ($) {
    'use strict';
    let has_childs = false;

    const originalMethod = $.fn.removeClass;
    $.fn.removeClass = function () {
        const result = originalMethod.apply(this, arguments);
        if (arguments[0] === "closed") {
            $(this).trigger('OpeningCMBBox');
        }
        return result;
    };

    $('.postbox.cmb2-postbox').bind('OpeningCMBBox', function () {
        let editor = $(this).find('.CodeMirror')[0];
        if (editor !== undefined) {
            editor = editor.CodeMirror;
            editor.refresh();
        }
    });

    $(document).on('change', '.cmb2_select', function () {
        const select_value = $(this).val();
        const cmb2_parent = $(this).parents('.dp_dmb_field_fieldtype');
        const dmb_design = cmb2_parent.siblings('.dp_dmb_field_show_design');
        const dmb_hide = cmb2_parent.siblings('.dp_dmb_field_hide_content');
        const dmb_custom_css = cmb2_parent.siblings('.dp_dmb_field_custom_css');
        const dmb_button_url = cmb2_parent.siblings('.dp_dmb_field_buttom_permalink');
        const dmb_select_option = cmb2_parent.siblings('.dp_dmb_field_select_options');
        const dmb_select_values = cmb2_parent.siblings('.dp_dmb_field_select_options_values');
        const dmb_select_function = cmb2_parent.siblings('.dp_dmb_field_select_options_function');
        const dmb_date_format = cmb2_parent.siblings('.dp_dmb_field_allow_date_format');
        const dmb_default_text = cmb2_parent.siblings('.dp_dmb_field_default_text');
        const dmb_default_textarea = cmb2_parent.siblings('.dp_dmb_field_default_textarea');
        const dmb_default_button = cmb2_parent.siblings('.dp_dmb_field_default_button');
        const dmb_default_color = cmb2_parent.siblings('.dp_dmb_field_default_color');
        const dmb_default_state = cmb2_parent.siblings('.dp_dmb_field_default_state');
        const dmb_default_image = cmb2_parent.siblings('.dp_dmb_field_default_image');
        const dmb_default_image_url = cmb2_parent.siblings('.dp_dmb_field_default_image_url');
        const dmb_checkbox_values = cmb2_parent.siblings('.dp_dmb_field_checkbox_values');
        const dmb_checkbox_process = cmb2_parent.siblings('.dp_dmb_field_checkbox_process');
        const dmb_responsive_field = cmb2_parent.siblings('.dp_dmb_field_responsive');
        dmb_design.hide();
        dmb_hide.hide();
        dmb_custom_css.hide();
        dmb_select_option.hide();
        dmb_button_url.hide();
        dmb_select_values.hide();
        dmb_select_function.hide();
        dmb_date_format.hide();
        dmb_default_color.hide();
        dmb_default_state.hide();
        dmb_default_text.hide();
        dmb_default_textarea.hide();
        dmb_default_button.hide();
        dmb_default_image.hide();
        dmb_default_image_url.hide();
        dmb_checkbox_values.hide();
        dmb_checkbox_process.hide();
        dmb_responsive_field.hide();
        switch (select_value) {
            case 'text':
                dmb_design.fadeIn();
                dmb_hide.fadeIn();
                dmb_custom_css.fadeIn();
                dmb_default_text.fadeIn();
                dmb_responsive_field.fadeIn();
                break;
            case 'textarea':
                dmb_design.fadeIn();
                dmb_hide.fadeIn();
                dmb_custom_css.fadeIn();
                dmb_default_textarea.fadeIn();
                dmb_responsive_field.fadeIn();
                break;
            case 'link':
                dmb_design.fadeIn();
                dmb_hide.fadeIn();
                dmb_custom_css.fadeIn();
                break;
            case 'color':
                dmb_default_color.fadeIn();
                dmb_responsive_field.fadeIn();
                break;
            case 'image':
                dmb_custom_css.fadeIn();
                dmb_default_image.fadeIn();
                dmb_responsive_field.fadeIn();
                dp_dmb_default_image_check();
                break;
            case 'yesno':
                dmb_default_state.fadeIn();
                dmb_responsive_field.fadeIn();
                break;
            case 'icon':
                dmb_custom_css.fadeIn();
                dmb_responsive_field.fadeIn();
                break;
            case 'button':
                dmb_button_url.fadeIn();
                dmb_custom_css.fadeIn();
                dmb_default_button.fadeIn();
                break;
            case 'select':
                dmb_select_option.fadeIn();
                dmb_select_values.fadeIn();
                dmb_select_function.fadeIn();
                break;
            case 'date_picker':
                dmb_date_format.fadeIn();
                break;
            case 'upload':
                dmb_responsive_field.fadeIn();
                break;
            case 'video':
                dmb_responsive_field.fadeIn();
                break;
            case 'audio':
                dmb_responsive_field.fadeIn();
                break;
            case 'checkbox':
                dmb_checkbox_values.fadeIn();
                //dmb_checkbox_process.fadeIn();
                break;
            default:
                break;
        }
    });

    function dp_dmb_trigger_changes_on_types() {
        $('.cmb2_select').each(function () {
            $(this).change();
        });
    }

    function dp_dmb_change_field_identifier_text() {
        $('.cmb-group-title').each(function () {
            const field_label = $(this).parent('div').find('div.dp_dmb_field_label').find('input').val();
            if (field_label !== '') {
                $(this).text(field_label);
            }
        });
    }

    function dp_dmb_default_image_check() {
        $('.dp_dmb_field_default_image input').each(function () {
            if ($(this).prop('checked')) {
                $(this).closest('.dp_dmb_field_default_image').next().fadeIn();
            } else {
                $(this).closest('.dp_dmb_field_default_image').next().hide();
            }
        });
    }

    dp_dmb_trigger_changes_on_types();
    dp_dmb_change_field_identifier_text();
    dp_dmb_default_image_check();

    $('.dp_dmb_field_default_image input').change(function () {
        dp_dmb_default_image_check();
    });

    $(document).on('click', '.button-secondary', function () {
        dp_dmb_trigger_changes_on_types();
        dp_dmb_change_field_identifier_text();
    });

    $(document).on('click', 'button.cmb-remove-group-row', function () {
        dp_dmb_change_field_identifier_text();
    });

    $(document).on('focusout', '.dp_dmb_field_label input', function () {
        dp_dmb_change_field_identifier_text();
    });

    /*
     * Add keyboard buttons for fields
     */
    const HTMLKeys = $('.cmb2-id--dp-dmb-htmlbox-keyboard > div > p');
    const PHPKeys = $('.cmb2-id--dp-dmb-htmlbox-keyboard2 > div > p');
    const childHTMLKeys = $('.cmb2-id--dp-dmb-htmlchildbox-keyboard > div > p');
    const childPHPKeys = $('.cmb2-id--dp-dmb-htmlchildbox-keyboard2 > div > p');
    $('.dp_dmb_field_identifier > div > input').each(function () {
        if ($(this).val() !== '') {
            const identifier = $(this).parents('.dp_dmb_field_identifier');
            const type = identifier.siblings('.dp_dmb_field_fieldtype').find('.cmb2_select').val();
            if (identifier.siblings('.dp_dmb_field_child').children('div').children('input').attr('checked')) {
                has_childs = true;
                childHTMLKeys.append('<span class="dp_dmb_kb_btn child" data-type="identifier">' + $(this).val() + '</span>');
                childPHPKeys.append('<span class="dp_dmb_kb_btn child" data-type="identifier-php">' + $(this).val() + '</span>');
                if (type === 'checkbox') {
                    childPHPKeys.append('<span class="dp_dmb_kb_btn child" data-type="identifier-php">' + $(this).val() + '_labels</span>');
                }
            } else {
                HTMLKeys.append('<span class="dp_dmb_kb_btn" data-type="identifier">' + $(this).val() + '</span>');
                PHPKeys.append('<span class="dp_dmb_kb_btn" data-type="identifier-php">' + $(this).val() + '</span>');
                if (type === 'checkbox') {
                    PHPKeys.append('<span class="dp_dmb_kb_btn" data-type="identifier-php">' + $(this).val() + '_labels</span>');
                }
            }
        }
    });

    if (has_childs) {
        HTMLKeys.append('<span class="dp_dmb_kb_btn" data-type="identifier">repeat_fields</span>');
        PHPKeys.append('<span class="dp_dmb_kb_btn" data-type="php">Repeat Fields</span>');
    }

    if ($('#_dp_dmb_fieldbox_checkbox_tiny_mce').attr('checked')) {
        if (has_childs) {
            childHTMLKeys.append('<span class="dp_dmb_kb_btn child" data-type="identifier">tiny_mce</span>');
            childPHPKeys.append('<span class="dp_dmb_kb_btn child" data-type="php">TinyMCE</span>');
        } else {
            HTMLKeys.append('<span class="dp_dmb_kb_btn" data-type="identifier">tiny_mce</span>');
            PHPKeys.append('<span class="dp_dmb_kb_btn" data-type="php">TinyMCE</span>');
        }
    }

    $('.dp_dmb_kb_btn').click(function () {
        const button = $(this);
        let text = "";
        const val = button.text();
        if (button.attr('data-type') === 'identifier') {
            text = '%%' + val + '%%';
        } else if (button.attr('data-type') === 'identifier-php') {
            text = '$' + val;
        } else if (button.attr('data-type') === 'php') {
            switch (val) {
                case 'Post Loop':
                    text = "<?php \n\
if ( have_posts() ) : \n\
while ( have_posts() ) : the_post(); \n\
?>\n\
<h2><?php  the_title(); ?></h2>\n\
<?php \n\
endwhile; \n\
endif;\n\
?>";
                    break;
                case 'New Query':
                    text = "<?php \n\
// the query\n\
$the_query = new WP_Query( $args );\n\
if ( $the_query->have_posts() ) : \n\
// the loop \n\
while ( $the_query->have_posts() ) : $the_query->the_post(); \n\
?>\n\
<h2><?php  the_title(); ?></h2>\n\
<?php \n\
endwhile; \n\
// end of the loop \n\
wp_reset_postdata();\n\
else :?>\n\
<p><?php  _e( 'Sorry, no posts matched your criteria.' ); ?></p>\n\
<?php endif;\n\
?>";
                    break;
                case 'Divi Excerpt':
                    text = "<?php \n\
if (function_exists('truncate_post')) {\n\
truncate_post(270);\n\
} else {\n\
echo get_the_excerpt();\n\
}\n\
?>";
                    break;
                case 'Repeat Fields':
                    text = "echo $this->content;"
                    break;
                case 'TinyMCE':
                    text = "echo '<div class=\"dp_field_tinymce\">\'.$this->content.\'</div>\';"
                    break;
            }
        }
        const editor = $(this).parents('.cmb-row').siblings('.cmb-row.cmb-type-textarea-code').find('.CodeMirror')[0].CodeMirror;
        const selection = editor.getSelection();
        if (selection.length > 0) {
            editor.replaceSelection(text);
        } else {
            const doc = editor.getDoc();
            const cursor = doc.getCursor();
            const pos = {
                line: cursor.line,
                ch: cursor.ch
            };
            doc.replaceRange(text, pos);
        }
    });

    $(document.body).on('keydown', '.dp_dmb_field_identifier > div.cmb-td > input', function (e) {
        if (e.key.replace(/[^a-z0-9_]+/, "") === "") {
            e.preventDefault();
        }
    });

    $(document.body).on('keydown', '.dp_dmb_field_fieldgroup > div.cmb-td > input', function (e) {
        if (e.key.replace(/[^A-Za-z0-9 ]+/, "") === "") {
            e.preventDefault();
        }
    });

    $(document.body).on('keydown', '.dp_dmb_field_select_options_function > div.cmb-td > input', function (e) {
        if (e.key.replace(/[^a-z0-9_]+/, "") === "") {
            e.preventDefault();
        }
    });

    /*
     * Expand/Contract functions
     */

    $('.dmb-expand-panel, .dmb-contract-panel').click(function () {
        let height;
        const action = $(this);
        const inside = action.parents('div.inside');
        const hidden = inside.find('.cmb-type-textarea-code').prevAll();
        if (action.parents('.cmb-row').hasClass("cmb2-id--dp-dmb-htmlbox-textarea-html-output") || action.parents('.cmb-row').hasClass("cmb2-id--dp-dmb-htmlchildbox-textarea-htmlchild-output")) {
            height = "50vh";
        } else {
            height = "85vh";
        }
        const editor = inside.find('.CodeMirror')[0].CodeMirror;
        if (action.hasClass('dmb-expand-panel')) {
            $('body').addClass('dp-dmb-noscroll');
            action.hide().siblings('.dmb-contract-panel').show();
            hidden.hide();
            inside.addClass('dmb-inside-expand');
            editor.setSize(null, height);
        } else {
            $('body').removeClass('dp-dmb-noscroll');
            action.hide().siblings('.dmb-expand-panel').show();
            hidden.show();
            inside.removeClass('dmb-inside-expand');
            editor.setSize(null, "300");
        }
    });

})(jQuery);