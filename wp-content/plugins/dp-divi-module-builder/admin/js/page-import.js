(function ($) {
    'use strict';
    $("#dp_dmb_library_modules_tabs").tabs();

    $('#dp_dmb_form_import').submit(function (ev) {
        ev.preventDefault();
        const loader = $('.dp_dmb_loader');
        const msg_box = $('#dp_dmb_loader_wrapper');
        const msg = $('#dp_dmb_form_import_msg');
        const button = $('#dp_dmb_form_import_button');
        const actions = $('#dp_dmb_form_import_actions');
        const data = new FormData($(this)[0]);
        data.append("action", "dp_dmb_process_import_file");
        $.ajax({
            type: 'POST',
            url: dpDmbImport.ajaxurl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function () {
                loader.show();
                msg_box.css({display: "flex"});
                msg.find('p').text('');
                button.attr('disabled', true);
            }
        }).done(function (response) {
            showMessage(loader, msg, response.message);
            if (response.status === 1) {
                const edit_link = 'post.php?post=' + response.data.id + '&action=edit';
                actions.find('.dp_dmb_admin_page_publish_link').attr('data-module', response.data.id).show();
                actions.find('.dp_dmb_admin_page_edit_link').prop('href', edit_link).show();
            } else {
                actions.find('.dp_dmb_admin_page_publish_link').removeAttr('data-module').hide();
                actions.find('.dp_dmb_admin_page_edit_link').prop('href', '#').hide();
            }
        }).fail(function (errorThrown) {
            showMessage(loader, msg, errorThrown);
        }).always(function () {
            button.removeAttr('disabled');
        });
    });

    $(".dp_dmb_library_import").click(function (ev) {
        ev.preventDefault();
        const id = $(this).attr('data-module-id');
        const module_data = JSON.parse(dpDmbModules['modules'][id].file_content);
        const loader = $('.dp_dmb_loader');
        const msg_box = $('#dp_dmb_loader_wrapper');
        const msg = $('#dp_dmb_form_import_msg');
        const actions = $('#dp_dmb_form_import_actions');
        $.ajax({
            type: 'POST',
            url: dpDmbImport.ajaxurl,
            data: {
                action: "dp_dmb_process_import_module_library",
                module_data: module_data
            },
            beforeSend: function () {
                loader.show();
                msg_box.css({display: "flex"});
                msg.find('p').text('');
                $('.dp_dmb_library_import').attr('disabled', true);
            },
        }).done(function (response) {
            showMessage(loader, msg, response.message);
            if (response.status === 1) {
                const edit_link = 'post.php?post=' + response.data.id + '&action=edit';
                actions.find('.dp_dmb_admin_page_publish_link').attr('data-module', response.data.id).show();
                actions.find('.dp_dmb_admin_page_edit_link').prop('href', edit_link).show();
            } else {
                actions.find('.dp_dmb_admin_page_publish_link').removeAttr('data-module').hide();
                actions.find('.dp_dmb_admin_page_edit_link').prop('href', '#').hide();
            }
        }).fail(function (errorThrown) {
            showMessage(loader, msg, errorThrown);
        }).always(function () {
            $('.dp_dmb_library_import').removeAttr('disabled');
        });
    });

    $('.dp_dmb_admin_page_close_link').click(function (ev) {
        ev.preventDefault();
        $('#dp_dmb_loader_wrapper').fadeOut('slow');
        $('#dp_dmb_form_import_msg').hide();
    });

    function showMessage(loader, msg_obj, msg) {
        loader.fadeOut('slow', function () {
            msg_obj.find('p').append(msg);
            msg_obj.fadeIn('slow');
        });
    }

    $('.dp_dmb_admin_page_publish_link').click(function (ev) {
        const module_id = $(this).attr('data-module');
        ev.preventDefault();
        $.ajax({
            type: 'POST',
            url: dpDmbImport.ajaxurl,
            data: {
                action: 'dp_dmb_process_import_publish_module',
                id: module_id
            },
            beforeSend: function () {
                $(this).attr('disabled', true);
            }
        }).done(function (response) {
            if (response.status) {
                window.open(dpDmbImport.settingsurl, "_self");
            } else {
                alert(response.message);
            }
        }).fail(function (errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).removeAttr('disabled');
        });
    });

    $("#dp_dmb_library_filter").change(function () {
        const category = $(this).val();
        if (category !== "all") {
            $("#dp_dmb_library_modules_tabs").find('div.dp_dmb_module').each(function () {
                if ($(this).attr("data-cat") === category) {
                    $(this).removeClass("cat-hide");
                } else {
                    $(this).addClass("cat-hide");
                }
            });
        } else {
            $("#dp_dmb_library_modules_tabs").find('div.dp_dmb_module').each(function () {
                if ($(this).hasClass("cat-hide")) {
                    $(this).removeClass("cat-hide");
                }
            });
        }
    });

    $("#dp_dmb_library_search").keyup(function (ev) {
        const search_term = $(this).val().toLowerCase();
        if (ev.keyCode === 13) {
            $("#dp_dmb_library_modules_tabs").find('div.dp_dmb_module').each(function () {
                const title = $(this).find(".dp_dmb_library_title").text().toLowerCase();
                const description = $(this).find(".dp_dmb_library_description").text().toLowerCase();
                if (title.includes(search_term) || description.includes(search_term)) {
                    if ($(this).hasClass("search-hide")) {
                        $(this).removeClass("search-hide");
                    }
                } else {
                    $(this).addClass("search-hide");
                }
            });
        } else if (search_term === "") {
            $("#dp_dmb_library_modules_tabs").find('div.dp_dmb_module').each(function () {
                if ($(this).hasClass("search-hide")) {
                    $(this).removeClass("search-hide");
                }
            });
        }
    });

})(jQuery);