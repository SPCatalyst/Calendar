(function ($) {

    $.modal.defaults.escapeClose = false;
    $.modal.defaults.clickClose = false;

    function init_scripts() {
        $('.spcc-select').select2();
        $('.spcc-colorpicker').wpColorPicker();
    }

    function init_datepickers() {
        var $datepickers = $('.datetimepicker');
        $datepickers.each(function () {
            $(this).datepicker({
                language: 'en',
            });
        });
    }

    /**
     * Notice
     * @param type
     * @param message
     * @param errors
     */
    function make_notice(type, message, errors) {
        var errors_html = '';
        if (errors) {
            errors_html += '<ul>';
            for (var i in errors) {
                errors_html += '<li>' + errors[i] + '</li>';
            }
            errors_html += '</ul>';
        }
        return '<div class="notice notice-' + type + ' is-dismissible">\n' +
            '<p>' + message + '</p>' + errors_html + '\n' +
            '</div>'
    }

    init_datepickers();

    $('#createEventForm').submit(function (e) {

        var $statusWrapper = $(this).find('.status-wrapper');
        var $submitBtn = $(this).find('button[type=submit]');
        var form = this;
        var formData = new FormData(form);
        $.ajax({
            url: SPCC.ajax_url + '?action=spcc_create_event&nonce=' + SPCC.nonce,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $statusWrapper.html('');
                $submitBtn.addClass('loading');
            },
            success: function (response) {
                var message;
                if (response.success) {
                    message = '<div class="notice notice-success is-dismissible">';
                    message += '<p>' + response.data.message + '</p>';
                    message += '</div>';
                    form.reset();

                    var item = response.data.item;

                    var row = '<tr>\n' +
                        '    <th scope="row" class="check-column">\n' +
                        '        <input type="checkbox" name="event_id[]" value="' + item.ID + '">\n' +
                        '    </th>\n' +
                        '    <td class="title column-title has-row-actions column-primary" data-colname="Title"><a href="#"><strong>' + item.post_title + '</strong></a>\n' +
                        '        <div class="row-actions"><span class="edit"><a href="#" data-id="' + item.ID + '" class="event-action event-edit" title="Edit this item">Edit</a> | </span><span class="delete"><a href="#" data-id="' + item.ID + '" class="event-action event-delete submitdelete" title="Delete this item">Delete</a></span></div>\n' +
                        '        <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>\n' +
                        '        <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>\n' +
                        '    </td>\n' +
                        '    <td class="start column-start" data-colname="Start">' + item.meta.event_start + '</td>\n' +
                        '    <td class="end column-end" data-colname="End">' + item.meta.event_end + '</td>\n' +
                        '    <td class="venue column-venue" data-colname="Venue">' + item.meta.event_venue + '</td>\n' +
                        '</tr>';

                    $('#the-list').prepend(row);

                } else {
                    message = '<div class="notice notice-error is-dismissible">';
                    message += '<p>' + response.data.message + '</p>';
                    message += '<ul>';
                    for (var i in response.data.errors) {
                        message += '<li>' + response.data.errors[i] + '</li>';
                    }
                    message += '</ul>';
                    message += '</div>';

                }
                $statusWrapper.html(message);
                $submitBtn.removeClass('loading');

            },
            complete: function () {
                $submitBtn.removeClass('loading');
            },
            error: function () {
                alert("error in ajax form submission");
                $submitBtn.removeClass('loading');
            }
        });
        return false;

    });

    $(document).on('submit', '#editEventForm', function (e) {
        var $statusWrapper = $(this).find('.status-wrapper');
        var $submitBtn = $(this).find('button[type=submit]');
        var form = this;
        var formData = new FormData(form);
        $.ajax({
            url: SPCC.ajax_url + '?action=spcc_update_event&nonce=' + SPCC.nonce,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $statusWrapper.html('');
                $submitBtn.addClass('loading');
            },
            success: function (response) {
                var message;
                if (response.success) {
                    message = '<div class="notice notice-success is-dismissible">';
                    message += '<p>' + response.data.message + '</p>';
                    message += '</div>';
                } else {
                    message = '<div class="notice notice-error is-dismissible">';
                    message += '<p>' + response.data.message + '</p>';
                    message += '<ul>';
                    for (var i in response.data.errors) {
                        message += '<li>' + response.data.errors[i] + '</li>';
                    }
                    message += '</ul>';
                    message += '</div>';
                }
                $statusWrapper.html(message);
                $submitBtn.removeClass('loading');
            },
            complete: function () {
                $submitBtn.removeClass('loading');
            },
            error: function () {
                alert("error in ajax form submission");
                $submitBtn.removeClass('loading');
            }
        });
        return false;
    });

    $(document).on('submit', '#deleteEventForm', function (e) {
        var $statusWrapper = $(this).find('.status-wrapper');
        var $submitBtn = $(this).find('button[type=submit]');
        var form = this;
        var formData = new FormData(form);
        var event_id = formData.get('event_id');
        $.ajax({
            url: SPCC.ajax_url + '?action=spcc_delete_event&nonce=' + SPCC.nonce,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $statusWrapper.html('');
                $submitBtn.addClass('loading');
            },
            success: function (response) {
                var message;
                if (response.success) {
                    message = '<div class="notice notice-success is-dismissible">';
                    message += '<p>' + response.data.message + '</p>';
                    message += '</div>';
                    var $cb = $('#the-list').find('input[value=' + event_id + '].event_checkbox');
                    if ($cb.length > 0) {
                        var $row = $cb.closest('tr');
                        $row.detach().remove();
                    }
                } else {
                    message = '<div class="notice notice-error is-dismissible">';
                    message += '<p>' + response.data.message + '</p>';
                    message += '<ul>';
                    for (var i in response.data.errors) {
                        message += '<li>' + response.data.errors[i] + '</li>';
                    }
                    message += '</ul>';
                    message += '</div>';
                }
                $statusWrapper.html(message);
                $submitBtn.removeClass('loading');
            },
            complete: function () {
                $submitBtn.removeClass('loading');
            },
            error: function () {
                alert("error in ajax form submission");
                $submitBtn.removeClass('loading');
            }
        });
        return false;
    });

    init_scripts();

    $(document).on('change', '.spcc-conditional', function (e) {
        var value = $(this).val();
        var $target = $($(this).data('target'));
        var hideif = $(this).data('target-hideifvalue');
        if (value === hideif) {
            $target.hide();
        } else {
            $target.show();
        }
    });


    // Event edit
    $(document).on('change', '#createEventForm #attendance', function (e) {

        var value = $(this).val();

        $.ajax({
            type: 'POST',
            url: SPCC.ajax_url + '?action=spcc_render_form_dynamic&nonce=' + SPCC.nonce,
            cache: false,
            data: {attendance: value},
            success: function (response) {
                $('.form-attendance-dependant').html(response);
            },
            error: function () {
                alert('HTTP error.');
            }
        })

    });

    // Login Form
    $(document).on('submit', '#form-login', function (e) {

        var data = $(this).serialize();

        var $self = $(this);
        var $wrap = $self.closest('.section-wrap');
        var $formWrap = $wrap.find('.section-form');
        var $formStatus = $wrap.find('.section-status');

        $.ajax({
            type: 'POST',
            url: SPCC.ajax_url + '?action=spcc_login&nonce=' + SPCC.nonce,
            data: data,
            beforeSend: function () {
                $formStatus.html('');
                $self.addClass('spcc-loading');
            },
            success: function (response) {
                if (response.success) {
                    $formWrap.html(response.data.html);
                    init_scripts();
                } else {
                    var notice = make_notice('error', response.data.message, response.data.errors);
                    $formStatus.html(notice);
                }
                $self.removeClass('spcc-loading');
            },
            error: function () {
                alert('HTTP Error.');
                $self.removeClass('spcc-loading');
            },
            complete: function() {
                $self.removeClass('spcc-loading');
            }
        });

        return false;
    });

    // Register Form
    $(document).on('submit', '#form-register', function (e) {

        var data = $(this).serialize();

        var $self = $(this);
        var $wrap = $self.closest('.section-wrap');
        var $formWrap = $wrap.find('.section-form');
        var $formStatus = $wrap.find('.section-status');

        $.ajax({
            type: 'POST',
            url: SPCC.ajax_url + '?action=spcc_register&nonce=' + SPCC.nonce,
            data: data,
            beforeSend: function () {
                $formStatus.html('');
                $self.addClass('spcc-loading');
            },
            success: function (response) {
                if (response.success) {
                    $formWrap.html(response.data.html);
                    init_scripts();
                } else {
                    var notice = make_notice('error', response.data.message, response.data.errors);
                    $formStatus.html(notice);
                }
                $self.removeClass('spcc-loading');
            },
            error: function () {
                $self.removeClass('spcc-loading');
                alert('HTTP Error.');
            },
            complete: function() {
                $self.removeClass('spcc-loading');
            }
        });

        return false;
    });

    // Settings Form
    $(document).on('submit', '#form-settings', function (e) {

        var data = new FormData(this);


        var $self = $(this);
        var $wrap = $self.closest('.section-wrap');
        var $formWrap = $wrap.find('.section-form');
        var $formStatus = $wrap.find('.section-status');

        $.ajax({
            type: 'POST',
            url: SPCC.ajax_url + '?action=spcc_settings_save&nonce=' + SPCC.nonce,
            data: data,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $formStatus.html('');
                $self.addClass('spcc-loading');
            },
            success: function (response) {
                var notice;
                if(response.success) {
                   notice = make_notice('success',response.data.message);
                } else {
                    notice = make_notice('error', response.data.message, response.data.errors);
                }
                $formStatus.html(notice);
                setTimeout(function(){
                    $self.removeClass('spcc-loading');
                }, 2000);
            },
            error: function () {
                setTimeout(function(){
                    $self.removeClass('spcc-loading');
                }, 2000);
                alert('HTTP Error.');
            },
            complete: function() {
                setTimeout(function(){
                    $self.removeClass('spcc-loading');
                }, 2000);
            }
        });

        return false;
    });


    // Switch forms
    $(document).on('click', '.spcc-form-switch', function (e) {
        var $login_wrap = $('#login-wrap');
        var $register_wrap = $('#register-wrap');
        $login_wrap.hide();
        $register_wrap.hide();
        var next = $(this).data('nextform');
        if (next === 'login') {
            $login_wrap.show();
        } else {
            $register_wrap.show();
        }
    });

    $(document).on('click', '.spcc-disconnect', function(e){
       if(confirm('Are you sure you want to disconnect from the community calendar')) {
           $.ajax({
               url: SPCC.ajax_url + '?action=spcc_disconnect&nonce=' + SPCC.nonce,
               type: 'POST',
               success: function() {
                   window.location.reload();
               }
           })
       }
    });

    $(document).on('submit', '#requestAccessForm', function(){

        var $self = $(this);
        var data = $self.serialize();

        $.ajax({
            url: SPCC.ajax_url + '?action=spcc_request_access&nonce=' + SPCC.nonce,
            type: 'POST',
            data: data,
            success: function(response) {
                if(response.success) {
                    $self.html('<p>'+response.data.message+'</p>');
                    $('.spcc-request-access-info').hide();
                } else {
                    alert(response.data.errors[0]);
                }
            },
            error: function() {
                alert('HTTP Error');
            }
        });

        return false;
    });

    $(document).on($.modal.OPEN, function(event, modal) {
        init_datepickers();
    });

})(jQuery);
