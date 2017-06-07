window.status_detail = function(id) {
    api({ action: 'status_detail', id: id }, function(data) {
        console.log(data);
        var dialog_fields = $('#status_detail .modal-body span');
        dialog_fields[0].textContent = data.seat;
        dialog_fields[1].textContent = data.date;
        dialog_fields[2].textContent = data.time;
        dialog_fields[3].textContent = data.organization;
        dialog_fields[4].textContent = data.applicant;
        $('#status_detail').modal('show');
    });
};
var guest = true;
$(function() {
    var tables = {}, dates_active = [], dates_not_allowed = [], set_table = function(d) {
        var date = moment(d).format('YYYY-MM-DD');
        $('#status_calendar span').text(date);
        $('#status tbody').html(tables[date] ? tables[date] : tables.blank);
    };

    api({ action: 'status_get' }, function(data) {
        var html = '';
        for (var c in data.seat) {
            if (data.seat.hasOwnProperty(c)) {
                html += '<tr><th>' + data.seat[c] + '</th>' + '<td>&nbsp;</td>'.repeat(14) + '</tr>';
            }
        }
        tables.blank = html;
        for (var d in data.status) {
            if (data.status.hasOwnProperty(d)) {
                var html = '';
                for (var c in data.seat) {
                    if (data.seat.hasOwnProperty(c)) {
                        html += '<tr><th>' + data.seat[c] + '</th>';
                        for (var t in data.status[d][c]) {
                            if (data.status[d][c].hasOwnProperty(t)) {
                                html += (data.status[d][c][t] === -1 ? '<td>&nbsp;</td>' : '<td class="label-' + (data.status[d][c][t] === 0 ? 'primary' : data.status[d][c][t] === -2 ? 'danger' : 'success') + '" title="' + (data.status[d][c][t] === 0 ? (lang === 'zh-tw' ? '審核中' : 'Pending') : data.status[d][c][t] === -2 ? (lang === 'zh-tw' ? '不開放' : 'Not Allowed') : (lang === 'zh-tw' ? '已通過' : 'Accepted')) + '">' + (data.status[d][c][t] > 0 ? '<a href="javascript:status_detail(' + data.status[d][c][t] + ');">' : '') + '<i class="fa fa-fw fa-' + (data.status[d][c][t] === 0 ? 'hourglass-start' : data.status[d][c][t] === -2 ? 'ban' : 'check') + '"></i>' + (data.status[d][c][t] > 0 ? '</a>' : '') + '</td>');
                                if (dates_active.indexOf(d) === -1 && data.status[d][c][t] >= 0) dates_active.push(d);
                                if (dates_not_allowed.indexOf(d) === -1 && data.status[d][c][t] === -2) dates_not_allowed.push(d);
                            }
                        }
                        html += '</tr>';
                    }
                }
                tables[d] = html;
            }
        }
        $('#status_calendar div.table').datepicker({
            format: "yyyy-mm-dd",
            weekStart: 0,
            startDate: moment().format('YYYY-MM-DD'),
            // endDate: moment().add(29, 'days').format('YYYY-MM-DD'),
            // maxViewMode: 0,
            todayBtn: "linked",
            language: lang === 'zh-tw' ? 'zh-TW' : undefined,
            beforeShowDay: function (date) {
                var d = moment(date).format('YYYY-MM-DD');
                return { classes: (dates_active.indexOf(d) === -1 ? '' : ' status-active') + (dates_not_allowed.indexOf(d) === -1 ? '' : ' status-not-allowed') };
            }
        }).datepicker('setDate', moment().format('YYYY-MM-DD')).on('changeDate.datepicker', function(event) {
            set_table(moment(event.date));
        });
        set_table(moment());
    });

    window.page = 'status';
});