(function ($) {
    'use strict';
    $('.dp_dmb_admin_page_clone_link').click(function (ev) {
        const module_id = $(this).attr('data-module');
        ev.preventDefault();
        $.ajax({
            type: 'POST',
            url: dpDmb.ajaxurl,
            data: {
                action: 'dp_dmb_clone_module',
                id: module_id,
            },
            beforeSend: function () {
                $(this).attr('disabled', true);
            },
        }).done(function (data, textStatus, jqXHR) {
            if (data !== 'OK') {
                alert(data);
            } else {
                location.reload();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).attr('disabled', false);
        });
    });

    $('.dp_dmb_admin_page_publish_link').click(function (ev) {
        const module_id = $(this).attr('data-module');
        ev.preventDefault();
        $.ajax({
            type: 'POST',
            url: dpDmb.ajaxurl,
            data: {
                action: 'dp_dmb_publish_module',
                id: module_id,
            },
            beforeSend: function () {
                $(this).attr('disabled', true);
            },
        }).done(function (data, textStatus, jqXHR) {
            if (data !== 'OK') {
                alert(data);
            } else {
                location.reload();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).attr('disabled', false);
        });
    });

    $('.dp_dmb_admin_page_delete_link').click(function (ev) {
        const module_id = $(this).attr('data-module');
        const module_name = $(this).attr('data-module-name');
        ev.preventDefault();
        if (window.confirm(dpDmb.delete_msg + module_name)) {
            $.ajax({
                type: 'POST',
                url: dpDmb.ajaxurl,
                data: {
                    action: 'dp_dmb_delete_module',
                    id: module_id
                },
                beforeSend: function () {
                    $(this).attr('disabled', true);
                },
            }).done(function (data, textStatus, jqXHR) {
                if (data !== 'OK') {
                    alert(data);
                } else {
                    location.reload();
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                location.reload();
            }).always(function () {
                $(this).attr('disabled', false);
            });
        }
    });

    $('#dp_dmb_deactivate_all_php').click(function (ev) {
        ev.preventDefault();
        $.ajax({
            type: 'POST',
            url: dpDmb.ajaxurl,
            data: {
                action: 'dp_dmb_off_all_modules',
            },
            beforeSend: function () {
                $(this).attr('disabled', true);
            },
        }).done(function (data, textStatus, jqXHR) {
            location.reload();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).attr('disabled', false);
        });
    });

    $('.dp_dmb_admin_page_php_on_link').click(function (ev) {
        ev.preventDefault();
        const module_id = $(this).attr('data-module');
        $.ajax({
            type: 'POST',
            url: dpDmb.ajaxurl,
            data: {
                action: 'dp_dmb_onoff_module',
                id: module_id,
                php: ''
            },
            beforeSend: function () {
                $(this).attr('disabled', true);
            },
        }).done(function (data, textStatus, jqXHR) {
            location.reload();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).attr('disabled', false);
        });
    });

    $('.dp_dmb_admin_page_php_off_link').click(function (ev) {
        ev.preventDefault();
        const module_id = $(this).attr('data-module');
        $.ajax({
            type: 'POST',
            url: dpDmb.ajaxurl,
            data: {
                action: 'dp_dmb_onoff_module',
                id: module_id,
                php: 'on'
            },
            beforeSend: function () {
                $(this).attr('disabled', true);
            },
        }).done(function (data, textStatus, jqXHR) {
            location.reload();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).attr('disabled', false);
        });
    });

    $('.dp_dmb_admin_page_update_link').click(function (ev) {
        ev.preventDefault();
        const module_id = $(this).attr('data-module');
        const module_name = $(this).attr('data-module-name');
        $('.dp_dmb_module_name').text(module_name);
        $('#dp_dmb_admin_page_update_modal_container').fadeIn('fast');
        $('.dp_dmb_close_btn').click(function () {
            $('#dp_dmb_admin_page_update_modal_container').fadeOut('fast');
            if ($(this).hasClass('trigger_reload')) {
                location.reload();
            }
        });
        $('#dp_dmb_form_update').submit(function (ev) {
                ev.preventDefault();
                const loader = $('.dp_dmb_loader');
                loader.fadeIn('fast');
                const msg_box = $('#dp_dmb_form_update_msg');
                msg_box.html('');
                const json_file = $('#dp_dmb_form_update_file')[0].files;
                const data = new FormData();
                data.append("action", "dp_dmb_process_update_file");
                data.append("module_id", module_id);
                data.append("replace_name", $('#dp_dmb_form_maintain_name').prop('checked'));
                $.each(json_file, function (key, value) {
                    data.append("file", value);
                });
                $.ajax({
                    type: 'POST',
                    url: dpDmb.ajaxurl,
                    data: data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        msg_box.fadeOut('fast');
                        $('#dp_dmb_form_update_button').attr('disabled', true);
                    },
                }).done(function (data, textStatus, jqXHR) {
                    loader.fadeOut('slow');
                    if (data !== 'OK') {
                        msg_box.append('<strong>' + data + '</strong>').fadeIn('slow');
                    } else {
                        msg_box.append('<strong>The module ' + module_name + ' has been sucessfully updated .</strong>').fadeIn('slow');
                        $('.dp_dmb_close_btn').addClass('trigger_reload');
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    loader.fadeOut('slow');
                    msg_box.append('<strong>' + errorThrown + '</strong>').fadeIn('slow');
                }).always(function () {
                    $('#dp_dmb_form_update_button').attr('disabled', false);
                });
            }
        );
    });
    /*
     * Update Settings
     */
    $('#dp_dmb_admin_page_settings_form').submit(function (ev) {
        ev.preventDefault();
        const data = new FormData($(this)[0]);
        $.ajax({
            type: 'POST',
            url: dpDmb.ajaxurl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(this).find('input[type=submit]').attr('disabled', true);
            }
        }).done(function (response) {
            if (response.status) {
                alert(response.message);
            }
        }).fail(function (errorThrown) {
            alert(errorThrown);
        }).always(function () {
            $(this).find('input[type=submit]').removeAttr('disabled');
        });
    });

})(jQuery);