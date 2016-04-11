var listTicketTable = {};
(function ($, table) {
    var $table = $('#list-tickets-table');
    table.initVars = function () {
        table.vars = {
            filters: {},
            selections: [],
            token:$('meta[name="csrf-token"]').attr('content'),
        };
    };
    table.initVars();
    table.filter = function (args) {
        if (typeof args == 'object') {
            for (var key in args) {
                if (args.hasOwnProperty(key)) {
                    table.vars.filters[key] = args[key];
                }
            }
        } else {
            table.vars.filters = {};
        }
    };
    table.init = function () {
        $table.bootstrapTable({
            queryParams: table.queryParams,
            responseHandler: function (res) {
                $.each(res.rows, function (i, row) {
                    row.state = $.inArray(row.id, table.vars.selections) !== -1;
                });
                return res;
            },
            columns: [

                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle'
                }, {
                    title: 'Subject',
                    field: 'subject',
                    width: '50%',
                    sortable: true
                },
                {
                    title: 'Status',
                    field: 'status',
                    formatter: function (value) {
                        var statusValue = value.replace(/[^a-z0-9]/g, '');
                        statusValue = 'status' + '-' + statusValue;
                        return '<span class="' + statusValue + '">' + value + '</span>';
                    },
                    sortable: true
                },
                {
                    title: 'Importance',
                    field: 'importance',
                    sortable: true
                },
                {
                    title: 'Date',
                    field: 'created_at',
                    formatter:function (value){
                        var t = value.split(/[- :]/);
                        var date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                        return (date.getMonth()+1)+'/'+date.getDate()+'/'+date.getFullYear();
                    },
                    sortable: true
                },
                {
                    title: 'Action',
                    formatter: table.renderActions,
                    events: table.events
                }

            ]
        })
        ;


    };
    table.queryParams = function (params) {
        if (table.vars.filters) {
            return $.extend({}, params, table.vars.filters);
        }
        return params;
    };
    table.events = {
        'click .edit': function (e, value, row, index) {
            $('#myModal').modal('show');
            $('.modal-title').html("View Ticket");
            $('.form-control.id').val(row.id);
            $('.hidden-id').val(row.id);
            var t = row.created_at.split(/[- :]/);
            var new_date = t[1] + "/" + t[2] + "/" + t[0];
            $('.form-control.date').val(new_date);
            $('.subject').val(row.subject);
            $('.description').val(row.description);
            $('.importance').val(row.importance);
            $('.status').val(row.status);
        },
        'click .remove': function (e, value, row, index) {
            table.delete([row.id]).done(function(){
                table.refresh();
            });
        }
    };
    table.renderActions = function () {
        return [
            '<a class="edit" href="javascript:void(0)" title="Edit">',
            '<i class="glyphicon glyphicon-pencil"></i>',
            '</a>  ',
            '<a class="remove" href="javascript:void(0)" title="Remove">',
            '<i class="glyphicon glyphicon-remove"></i>',
            '</a>'
        ].join('');
    };
    table.setupToolbar = function () {
        var $remove = $('#remove').prop('disabled', 1);
        $table.on('check.bs.table uncheck.bs.table ' +
            'check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
            // save your data, here just save the current page
            table.vars.selections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });
        $remove.on('click', function () {

            var ids = getIdSelections();
            var confirm_mess = 'Are you sure to delete the selected ticket?';
            if (ids.length > 1) {
                confirm_mess = 'Are you sure to delete ' + ids.length + ' tickets';
            }
            if (!confirm(confirm_mess)) {
                return false;
            }
            //console.log(table.getOptions());
            table.delete(ids).done(function(){table.refresh()});
            $remove.prop('disabled', true);
        });
        var $status_filter = $('#status-filter');
        var $importance_filter = $('#importance-filter');
        $('select').prop('selectedIndex', 0);
        $status_filter.on('change', function () {
            table.filter({'status': $(this).val()});
            table.refresh();
        });
        $importance_filter.on('change', function () {
            table.filter({'importance': $(this).val()});
            table.refresh();
        });
    };
    table.refresh = function () {
        $table.bootstrapTable('refresh');
    };
    table.getOptions = function () {
        return $table.bootstrapTable('getOptions');
    };
    table.ajax = function (options) {
        options = $.extend({}, {
            headers: {
                'X-CSRF-TOKEN': table.vars.token
            },
            url: table.getOptions()['url']
        }, options);
        return $.ajax(options);
    };
    table.delete = function(ids){
        return table.ajax({type: "DELETE", url:table.getOptions()['url']+'?' + $.param({'tickets': ids})});
    };
    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }

    if ($table.length == 1) {
        table.init();
        table.setupToolbar();
    }

})(jQuery, listTicketTable);