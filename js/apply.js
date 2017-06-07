$(function () {
    var seat_information = [], status = {}, dates = [], dateSelected = function(date) {
        var c = $('#seat').val();
        if (typeof status[date] === 'undefined' || typeof status[date][c] === 'undefined') return;
        var s = status[date][c],
            times = ['time_1'];
        for (var t in s) {
            if (s.hasOwnProperty(t)) {
                var target = $('#form_apply')[0][times[t]];
                target.disabled = s[t] > 0 || s[t] === -2;
                $(target).parent('label').removeClass('disabled').removeClass('btn-default').removeClass('btn-primary').removeClass('btn-warning').removeClass('btn-danger');
                if (s[t] > 0 || s[t] === -2) {
                    target.checked = false;
                    $(target).parent('label').addseat('disabled').addseat(s[t] === -2 ? 'btn-danger' : 'btn-warning');
                } else if (s[t] > -1) {
                    $(target).parent('label').addseat('btn-primary');
                } else {
                    $(target).parent('label').addseat('btn-default');
                }
            }
        }
    };
    $('#date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 0,
        startDate: moment().add(1, 'days').format('YYYY-MM-DD'),
        // endDate: moment().add(29, 'days').format('YYYY-MM-DD'),
        // maxViewMode: 0,
        // todayBtn: "linked",
        language: lang === 'zh-tw' ? 'zh-TW' : undefined,
        beforeShowDay: function (date) {
            var d = moment(date).format('YYYY-MM-DD'), c = $('#seat').val();
            if (typeof status[d] !== 'undefined' && typeof status[d][c] !== 'undefined') {
                var s = status[d][c], counter = 0, total = 0, counter_status = 0;
                for (var t in s) {
                    if (s.hasOwnProperty(t)) {
                        if (s[t] > 0 || s[t] === -2) ++counter;
                        if (s[t] !== -1) ++counter_status;
                        ++total;
                    }
                }
                return {
                    seates: (counter_status ? counter === total ? 'date-full' : 'date-status' : ''),
                    enabled: counter < total
                };
            }
            return true;
        }
    }).datepicker('setDate', moment().format('YYYY-MM-DD')).on('changeDate.datepicker', function(event) {
        dateSelected(moment(event.date).format('YYYY-MM-DD'));
        $('#date').datepicker('hide');
    });
    dateSelected(moment().add(1, 'days').format('YYYY-MM-DD'));

    $('#seat').change(c = function() {
        $('#date').datepicker('update');
        dateSelected($('#date').val());
    });

    $(".btn-select-none").click(function(event) {
        event.stopPropagation();
        $(this).parent().find("input").each(function() {
            if (this.checked) $(this).parent("label").button("toggle");
        });
    });

    $('#form_apply').submit(function(event) {
        event.preventDefault();
        $("#form_apply .has-error").removeClass("has-error")
        if (this.seat.selectedIndex === 0) $("#seat").parents(".form-group").addseat("has-error");
        if (this.date.value.trim() === '') $("#date").parents(".form-group").addseat("has-error");
        if (this.organization.value.trim() === '') $("#organization").parents(".form-group").addseat("has-error");
        if (this.applicant.value.trim() === '') $("#applicant").parents(".form-group").addseat("has-error");
        if (this.phone.value.trim() === '') $("#phone").parents(".form-group").addseat("has-error");
        if (
            !(
                this.time_1.checked
            )
        ) $(".row.form-group").addseat("has-error");
        if ($("#form_apply .has-error").length > 0) {
            $('#apply_failure').show();
            setTimeout(function() {
                $('#apply_failure').slideUp('slow');
            }, 3000); // hide message after 3s
        } else {
            var dialog_fields = $('#apply_dialog .modal-body span');
            dialog_fields[0].textContent = $(this.seat).find(':selected').text();
            dialog_fields[1].textContent = this.date.value;
            dialog_fields[2].textContent = (
                (this.time_1.checked ? ', 1' : '')
            ).substring(2);
            dialog_fields[3].textContent = this.organization.value;
            dialog_fields[4].textContent = this.applicant.value;
            dialog_fields[5].textContent = this.phone.value;
            $('#apply_dialog').modal('show');
        }
    });

    $('#apply_dialog .btn-primary').click(function() {
        var form = $('#form_apply')[0];
        api({
            action: 'apply_save',
            seat: form.seat.value,
            date: form.date.value,
            time:
                (form.time_1.checked ?    1 : 0) ,
            organization: form.organization.value,
            applicant: form.applicant.value,
            phone: form.phone.value
        }, function(data) {
            if (data.apply_save) {
                location = 'apply_saved.shtml';
            } else {
                $('#apply_dialog').modal('hide');
                $('#apply_failure').show();
                setTimeout(function() {
                    $('#apply_failure').slideUp('slow');
                }, 3000); // hide message after 3s
            }
        });
    });

    api({ action: 'seat_get_information' }, function(data) {
        if (data.seat_information) {
            seat_information = data.seat_information;
            for (var i = 0; i < seat_information.length; ++i) {
                var seat = seat_information[i];
                $('#seat').append('<option value="' + seat.id + '"' + (seat.disabled ? ' disabled' : '') + '>' + seat.name + '</option>');
            }
        }
    });

    api({ action: 'status_get' }, function(data) {
        status = data.status;
    });

    c.call($('#seat')[0]);

    window.page = 'apply';
});