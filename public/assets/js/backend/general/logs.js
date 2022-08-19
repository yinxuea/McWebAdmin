define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload', 'jstree'], function ($, undefined, Backend, Table, Form, Upload) {
    var elTree = $('#treeview');// 目录元素
    var table = $("#table"); // 表格元素

    var Controller = {
        // 是否启动过表格
        startTable: false,

        action: {
            'index': "general/logs/index",
            // 操作的文件路径
            'filePath': '',
        },

        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'general/logs/index',
                    del_url: 'general/logs/del?file_paths'+Controller.action.filePath,
                    table: '',
                    detail_url: 'general/logs/index'
                },
                search: false,
                advancedSearch: false,
                pagination: false,
                responseHandler: function (res) {
                    var info = res.info;
                    document.getElementById('info-size').innerHTML = info.size;
                    document.getElementById('info-update_time').innerHTML = info.update_time;
                    return res;
                },
                columns: [
                    [
                        {checkbox: true},
                        {
                            field: 'id',
                            title: 'Id',
                            sortable: true,
                            operate: false,
                        },
                        {
                            field: 'level',
                            title: 'Level',
                            searchList: {
                                'error': 'ERROR',
                                'notice': 'NOTICE',
                                'info': 'INFO',
                                'debug': 'DEBUG',
                                'sql': 'SQL'
                            },
                            formatter: function (value) {
                                var label_class;
                                switch (value) {
                                    case 'ERROR':
                                        label_class = 'danger';
                                        break;
                                    case 'NOTICE':
                                        label_class = 'warning';
                                        break;
                                    default:
                                        label_class = 'info';
                                        break;
                                }
                                return '<span class="label label-' + label_class + '">' + value + '</span>';
                            }
                        },
                        {
                            field: 'method',
                            title: __('Method'),
                            searchList: {
                                'get': 'GET',
                                'post': 'POST',
                                'put': 'PUT',
                                'patch': 'PATCH',
                                'delete': 'DELETE',
                                'copy': 'COPY',
                                'head': 'HEAD',
                                'options': 'OPTIONS',
                                'purge': 'PURGE',
                                'view': 'VIEW',
                            },
                        },
                        {
                            field: 'url',
                            title: __('Url'),
                            formatter: function (value) {
                                var str = value.substr(0, 50);
                                var tail = value.length > 50 ? '...' : '';
                                str += tail;
                                // return '<a class="btn-dialog" href="' + value + '" title="Url">' + str + '</a>';
                                return '<a class="" href="javascript:;" title="Url">' + str + '</a>';
                            },
                        },
                        {
                            field: 'time',
                            title: __('Time'),
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime,
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Controller.api.events.operate, formatter: Table.api.formatter.operate, buttons: [
                            {
                                name: 'detail',
                                text: '详情',
                                title: '详情',
                                icon: 'fa fa-list',
                                classname: 'btn btn-xs btn-success btn-detail',
                            },
                        ]}
                    ]
                ],
            });

            Controller.api.bindevent();
        },

        api: {
            events: {
                operate: {
                    'click .btn-delone': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var top = $(that).offset().top - $(window).scrollTop();
                        var left = $(that).offset().left - $(window).scrollLeft() - 260;
                        if (top + 154 > $(window).height()) {
                            top = top - 154;
                        }
                        if ($(window).width() < 480) {
                            top = left = undefined;
                        }
                        Layer.confirm(
                            __('Are you sure you want to delete this item?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},
                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');
                                Table.api.multi("del", row[options.pk], table, that);
                                Layer.close(index);
                            }
                        );
                    },
                    'click .btn-detail': function (e, value, row, index) {
                        var content = '<div style="white-space: pre-wrap;background: #333;color: #fff; padding: 10px;padding-top: 3px;min-height: 600px;">' + row.content + '</div>';
                        layer.open({
                            type: 1,
                            title: '详细信息',
                            fixed: true, //不固定
                            maxmin: true,
                            area: ['800px', '600px'],
                            content: content,
                            shade: 0
                        });
                    }
                }
            },

            // 修改表格数据
            diyForm: function (file_paths) {
                var params = '?file_paths=' + file_paths;
                var url = Controller.action.index + params;
                if (file_paths.indexOf('_cli.log') > -1) {
                    Fast.api.ajax(url, function (data) {
                        var content = '<div style="white-space: pre-wrap;background: #333;color: #fff; padding: 10px;padding-top: 3px;min-height: 600px;">' + data + '</div>';
                        layer.open({
                            type: 1,
                            title: 'cli日志信息',
                            fixed: true, //不固定
                            maxmin: true,
                            area: ['800px', '600px'],
                            content: content,
                            shade: 0
                        });
                    });
                    return true;
                }
                if (this.startTable) {
                    table.bootstrapTable('refresh', {url: url});
                } else {
                    table.bootstrapTable({
                        url: url,
                    });
                    Table.api.bindevent(table);
                    this.startTable = true;
                }
            },

            bindevent: function () {
                elTree.jstree("destroy");
                Controller.api.rendertree(nodeData);
                $(document).on("click", "#expandall", function () {
                    elTree.jstree($(this).prop("checked") ? "open_all" : "close_all");
                });
                // 开关目录
                $(document).on("click", "a.btn-channel", function () {
                    $("#right-content").toggleClass("col-md-9", $("#left-content").hasClass("hidden"));
                    $("#left-content").toggleClass("hidden");
                });
            },

            rendertree: function (content) {
                elTree
                    .on("changed.jstree", function (e, data) {
                        if (data.action == 'model' || (data.node && data.node.type == 'file')) {
                            Controller.action.filePath = data.selected[0];
                            Controller.api.diyForm(data.selected[0]);
                            Table.api.init({
                                extend: {
                                    del_url: encodeURI('general/logs/del?file_paths=' + Controller.action.filePath),
                                }
                            });
                        }
                    })
                    .jstree({
                        "themes": {"stripes": true},
                        "checkbox": {
                            "keep_selected_style": false,
                        },
                        "types": {
                            "folder": {
                                "icon": "jstree-folder",
                            },
                            "file": {
                                "icon": "jstree-file",
                            }
                        },
                        "plugins": ["types"],
                        "core": {
                            'check_callback': false,
                            "data": content
                        }
                    });
            }
        }
    };
    return Controller;
});