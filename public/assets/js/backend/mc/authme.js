define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mc/authme/index' + location.search,
                    add_url: 'mc/authme/add',
                    edit_url: 'mc/authme/edit',
                    del_url: 'mc/authme/del',
                    multi_url: 'mc/authme/multi',
                    import_url: 'mc/authme/import',
                    table: 'authme',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'realname', title: __('Realname'), operate: 'LIKE'},
                        {field: 'password', title: __('Password'), operate: 'LIKE'},
                        {field: 'ip', title: __('Ip'), operate: 'LIKE'},
                        {field: 'lastlogin', title: __('Lastlogin') ,operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'x', title: __('X'), operate:'BETWEEN'},
                        {field: 'y', title: __('Y'), operate:'BETWEEN'},
                        {field: 'z', title: __('Z'), operate:'BETWEEN'},
                        {field: 'world', title: __('World'), operate: 'LIKE'},
                        {field: 'regdate', title: __('Regdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'regip', title: __('Regip'), operate: 'LIKE'},
                        {field: 'yaw', title: __('Yaw'), operate:'BETWEEN'},
                        {field: 'pitch', title: __('Pitch'), operate:'BETWEEN'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'isLogged', title: __('Islogged')},
                        {field: 'hasSession', title: __('Hassession')},
                        {field: 'totp', title: __('Totp'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
