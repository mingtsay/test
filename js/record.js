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
    api({ action: 'record_get_list' }, function(data) {
        if (data.record_list) {
            var t = '<tr><th>' + (lang === 'zh-tw' ? '狀態' : 'Status') + '</th><th>' + (lang === 'zh-tw' ? '地點' : 'Place') + '</th><th>' + (lang === 'zh-tw' ? '日期' : 'Date') + '</th><th>' + (lang === 'zh-tw' ? '時段' : 'Time') + '</th></tr>',
                list = '<table class="table table-bordered table-striped"><thead>' + t + '</thead><tfoot>' + t + '</tfoot><tbody>';
            for (var i = 0; i < data.record_list.length; ++i) {
                var row = data.record_list[i], date = new Date(row.date), date_today = new Date(), in_past = date.getFullYear() < date_today.getFullYear() || date.getFullYear() === date_today.getFullYear() && (date.getMonth() < date_today.getMonth() || date.getMonth() === date_today.getMonth() && date.getDate() < date_today.getDate());
                list += '<tr>';
                list += '<td class="label-' + (row.status === 4 ? 'danger' : row.status > 1 ? 'default' : row.status ? 'success' : in_past ? 'warning' : 'primary') + '"><i class="fa fa-fw fa-' + (row.status === 4 ? 'times' : row.status > 1 ? 'trash' : row.status ? 'check' : in_past ? 'clock-o' : 'hourglass-start') + '"></i><span class="hidden-xs"> ' + (row.status === 4 ? (lang === 'zh-tw' ? '已駁回' : 'Rejected') : row.status > 1 ? (lang === 'zh-tw' ? '已取消' : 'Canceled') : row.status ? (lang === 'zh-tw' ? '已通過' : 'Accepted') : in_past ? (lang === 'zh-tw' ? '已過期' : 'Expired') : (lang === 'zh-tw' ? '審核中' : 'Pending')) + '</span></td>';
                list += '<td>' + row.classroom + '</td>';
                list += '<td>' + row.date + '</td>';
                list += '<td>' + row.time + '</td>';
                list += '</tr>';
            }
            list += '</tbody></table>';
            $('#record').html(list);
            $('#record table').DataTable(table_config);
        }
    });

    window.page = 'record';
});