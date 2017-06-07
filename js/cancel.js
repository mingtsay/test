var cancel_list = {}, cancel_id = -1;
var cancel = function(id) {
    var dialog_fields = $('#cancel_dialog .modal-body span');
    dialog_fields[0].className   = (cancel_list[id].status ? 'text-success' : 'text-primary');
    dialog_fields[0].textContent = (cancel_list[id].status ? (lang === 'zh-tw' ? '已通過' : 'Accepted') : (lang === 'zh-tw' ? '審核中' : 'Pending'));
    dialog_fields[1].textContent = cancel_list[id].seat;
    dialog_fields[2].textContent = cancel_list[id].date;
    dialog_fields[3].textContent = cancel_list[id].time;
    cancel_id = id;
    $('#cancel_dialog').modal('show');
};

$('#cancel_dialog .btn-danger').click(function() {
    api({ action: 'cancel', id: cancel_id }, function() { location = location; });
    cancel_id = -1;
});

$(function() {
    var table_config = { lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "顯示全部"]], order: [[ 2, "desc" ], [ 3, "asc" ], [ 0, "asc" ], [ 1, "asc" ]] };
    if (lang === 'zh-tw') table_config.language = {
        emptyTable:     "無資料可供顯示。",
        info:           "正在顯示第 _START_ 至 _END_ 筆資料，共 _TOTAL_ 筆",
        infoEmpty:      "無資料可供顯示。",
        infoFiltered:   "（從 _MAX_ 筆資料中篩選）",
        infoPostFix:    "",
        lengthMenu:     "每頁顯示 _MENU_ 筆資料",
        loadingRecords: "載入中…",
        processing:     "處理中…",
        search:         "篩選結果：",
        zeroRecords:    "找不到符合的結果。",
        paginate: { sPrevious: "&laquo; 上一頁", sNext: "下一頁 &raquo;" }
    };
    api({ action: 'cancel_get_list' }, function(data) {
        if (data.cancel_list) {
            var t = '<tr><th>' + (lang === 'zh-tw' ? '狀態' : 'Status') + '</th><th>' + (lang === 'zh-tw' ? '地點' : 'Place') + '</th><th>' + (lang === 'zh-tw' ? '日期' : 'Date') + '</th><th>' + (lang === 'zh-tw' ? '時段' : 'Time') + '</th><th>' + (lang === 'zh-tw' ? '取消' : 'Cancel') + '</th></tr>',
                list = '<table class="table table-bordered table-striped"><thead>' + t + '</thead><tfoot>' + t + '</tfoot><tbody>';
            for (var i = 0; i < data.cancel_list.length; ++i) {
                var row = data.cancel_list[i];
                list += '<tr>';
                list += '<td class="label-' + (row.status ? 'success' : 'primary') + '"><i class="fa fa-fw fa-' + (row.status ? 'check' : 'hourglass-start') + '"></i> ' + (row.status ? (lang === 'zh-tw' ? '已通過' : 'Accepted') : (lang === 'zh-tw' ? '審核中' : 'Pending')) + '</td>';
                list += '<td>' + row.seat + '</td>';
                list += '<td>' + row.date + '</td>';
                list += '<td>' + row.time + '</td>';
                list += '<td><a class="btn btn-xs btn-danger" href="javascript:cancel(' + row.id + ');" title="' + (lang === 'zh-tw' ? '取消' : 'Cancel') + '"><i class="fa fa-fw fa-trash"></i> ' + (lang === 'zh-tw' ? '取消' : 'Cancel') + '</a></td>';
                list += '</tr>';
                cancel_list[row.id] = {
                    status: row.status,
                    seat: row.seat,
                    date: row.date,
                    time: row.time
                };
            }
            list += '</tbody></table>';
            $('#cancel').html(list);
            $('#cancel table').DataTable(table_config);
        }
    });

    window.page = 'cancel';
});