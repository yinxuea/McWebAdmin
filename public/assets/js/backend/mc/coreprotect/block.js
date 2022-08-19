define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mc/coreprotect/block/index' + location.search,
                    add_url: 'mc/coreprotect/block/add',
                    edit_url: 'mc/coreprotect/block/edit',
                    del_url: 'mc/coreprotect/block/del',
                    multi_url: 'mc/coreprotect/block/multi',
                    import_url: 'mc/coreprotect/block/import',
                    table: 'co_block',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'rowid',
                sortName: 'rowid',
                fixedColumns: true,
                fixedRightNumber: 1,
                clickToSelect:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'rowid', title: __('Rowid')},
                        {field: 'time', title: __('Time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'wid', title: __('Wid')},
                        {field: 'x', title: __('X')},
                        {field: 'y', title: __('Y')},
                        {field: 'z', title: __('Z')},
                        {field: 'type', title: __('Type')},
                        {field: 'data', title: __('Data')},
                        {field: 'meta', title: __('Meta')},
                        {field: 'blockdata', title: __('Blockdata')},
                        {field: 'action', title: __('Action'),
                            searchList: {0:'左键破坏',1:'右键放置'},
                            formatter: Table.api.formatter.flag
                        },
                        //{field: 'user.time', title: __('User.time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'user.user', title: __('User.user'), operate: 'LIKE'},
                        {field: 'user.uuid', title: __('User.uuid'), operate: 'LIKE'},
                        // {field: 'world.id', title: __('World.id')},
                        {field: 'world.world', title: __('World.world'),
                            searchList: {"1":'world1',"2":'world_nether',"3":'world_the_end'},
                            operate: 'LIKE'
                        },
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
