(function ($) {

    function init_datepickers() {
        var $datepickers = $('.datetimepicker');
        $datepickers.each(function () {
            $(this).datepicker({
                language: 'en',
            });
        });
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
                    var $cb = $('#the-list').find('input[value='+event_id+'].event_checkbox');
                    if($cb.length>0) {
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

    // Open modal in AJAX callback
    $('.event-edit').click(function (event) {
        init_datepickers();
    });

    $('.spcc-select').select2();
    $('.spcc-colorpicker').wpColorPicker();

    $('.spcc-conditional').on('change', function(e){
        var value = $(this).val();
        var $target = $($(this).data('target'));
        var hideif = $(this).data('target-hideifvalue');
        if( value === hideif) {
            $target.hide();
        } else {
            $target.show();
        }
    });


    // Event edit
    $(document).on('change', '#createEventForm #attendance', function(e){

        var value = $(this).val();

        $.ajax({
            type: 'POST',
            url: SPCC.ajax_url + '?action=spcc_render_form_dynamic&nonce=' + SPCC.nonce,
            cache: false,
            data: {attendance: value},
            success: function(response) {
                $('.form-attendance-dependant').html(response);
            },
            error: function() {
                alert('HTTP error.');
            }
        })

    })

})(jQuery);
