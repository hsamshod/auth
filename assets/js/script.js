var Validator = {
    isEmpty: function(value) {
        return (!value || value.length === 0);
    },

    isAlphanum: function(value) {
        if (this.isEmpty(value)) {
            return false;
        }

        return value.replace(/[^a-zA-Z0-9_]/, '').length === value.length;
    },

    isDate: function(value) {
        var date = new Date(value)
        return value === date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
    },

    hasLength: function(value, length) {
        return value.toString().length >= length;
    },

    isSame: function(val1, val2) {
        return val1 === val2;
    },

    isImage: function(file_field_id) {
        var files = $(file_field_id).get(0).files;
        if (files) {
            if (files.length) {
                var file = files[0];
                return ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].indexOf(file.type) !== -1
            } else {
                return true
            }
        }

        console.log('File api not supported');
        return true
    }
};

function days_in_month (month, year) {
    return new Date(year, month, 0).getDate();
}

$(document).ready(function () {
    $('#bday-month, #bday-year').change(function () {
        $('.calendar-day').show()

        days = days_in_month($('#bday-month').val(), $('#bday-year').val());
        for (i = days + 1; i <= 31; i++) {
            $('#day-' + i).hide();
        }

        if ($('#bday-day').val() > days) {
            $('#bday-day').val(days)
        }
    });

    $('.auth-form-tab').click(function() {
        var clicked_tab = $(this);
        if (clicked_tab.hasClass('active')) {
            return;
        }

        $('.auth-form-tab').removeClass('bg-dark text-light active');
        clicked_tab.addClass('bg-dark text-light active');

        var content_id = clicked_tab.attr('id');
        $('.form-content').addClass('d-none');
        $('#' + content_id + '-content').removeClass('d-none');
    });

    $('#login-submit').click(function (ev) {
        var has_errors = false;
        var form_id = '#login-form-content ';
        $(form_id + ' .invalid-feedback').addClass('d-none');
        $(form_id + ' .is-invalid').removeClass('is-invalid');;

        var fields = ['#login-username', '#login-password'];
        fields.forEach(function (field) {
            field = form_id + ' ' + field;
            if (Validator.isEmpty($(field).val())) {
                $(field + '-empty').removeClass('d-none').show();
                $(field).addClass('is-invalid');
                has_errors = true
            }
        });

        if (has_errors) {
            ev.preventDefault();
            return;
        }

        $(form_id).addClass('was-validated');
    });

    $('#register-submit').click(function (ev) {
        var has_errors = false;
        var form_id = '#register-form-content ';
        $(form_id + ' .invalid-feedback').addClass('d-none');
        $(form_id + ' .is-invalid').removeClass('is-invalid');

        var field = form_id + ' #register-login';
        if (Validator.isEmpty($(field).val())) {
            $(field + '-empty').removeClass('d-none').show();
            $(field).addClass('is-invalid');
            has_errors = true
        } else if (!Validator.isAlphanum($(field).val())) {
            $(field + '-alphanum').removeClass('d-none').show();
            $(field).addClass('is-invalid');
            has_errors = true;
        }

        field = form_id + ' #register-password';
        if (Validator.isEmpty($(field).val())) {
            $(field + '-empty').removeClass('d-none').show();
            $(field).addClass('is-invalid');
            has_errors = true;
        } else if (!Validator.hasLength($(field).val(), 6)) {
            $(field + '-length').removeClass('d-none').show();
            $(field).addClass('is-invalid');
            has_errors = true;
        }
        if (!Validator.isSame($(field).val(), $(field + '-repeat').val())) {
            $(field + '-same').removeClass('d-none').show();
            $(field + '-repeat').addClass('is-invalid');
            has_errors = true;
        }

        field = form_id + ' #user-photo';
        if (!Validator.isImage(field)) {
            $(form_id + ' #image-format').removeClass('d-none').show();
            $(field).addClass('is-invalid');
            has_errors = true;
        }

        day = form_id + ' #bday-day';
        month = form_id + ' #bday-month';
        year = form_id + ' #bday-year';
        if (!Validator.isDate($(year).val() + '-' + $(month).val() + '-' + $(day).val())) {
            $(form_id + ' #register-date').removeClass('d-none').show();
            $(year + ',' + month + ',' + day).addClass('is-invalid');
        }

        if (has_errors) {
            ev.preventDefault();
        } else {
            $(form_id).addClass('was-validated')
        }
    });
});