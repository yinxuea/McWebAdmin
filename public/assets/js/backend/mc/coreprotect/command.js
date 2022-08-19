define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mc/coreprotect/command/index' + location.search,
                    add_url: 'mc/coreprotect/command/add',
                    edit_url: 'mc/coreprotect/command/edit',
                    del_url: 'mc/coreprotect/command/del',
                    multi_url: 'mc/coreprotect/command/multi',
                    import_url: 'mc/coreprotect/command/import',
                    table: 'co_command',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'rowid',
                sortName: 'rowid',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'rowid', title: __('Rowid')},
                        {field: 'time', title: __('Time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'user', title: __('User')},
                        {field: 'wid', title: __('Wid'), searchList: {"1":'world',"2":'world_nether',"3":'world_the_end'}, formatter: Table.api.formatter.normal },
                        {field: 'x', title: __('X')},
                        {field: 'y', title: __('Y')},
                        {field: 'z', title: __('Z')},
                        {field: 'message', title: __('Message'), operate: 'LIKE'},
                        // {field: 'user.rowid', title: __('User.rowid')},
                        // {field: 'user.time', title: __('User.time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'user.user', title: __('User.user'), operate: 'LIKE'},
                        {field: 'user.uuid', title: __('User.uuid'), operate: 'LIKE'},
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
