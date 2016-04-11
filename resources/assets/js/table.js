var listTicketTable = {};
(function ($, table) {
    var $table = $('#list-tickets-table');
    table.initVars=function(){
        table.vars={
            filters:{}
        };
    };
    table.initVars();
    table.filter=function(args){
        table.vars.filters=args;
    };
    table.init = function () {
        $table.bootstrapTable({
            queryParams:table.queryParams,
            columns: [

                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle'
                }, {
                    title: 'ID',
                    field: 'id',
                    visible: false
                }, {
                    title: 'Subject',
                    field: 'subject',
                    width: '50%',
                    sortable: true
                },
                {
                    title: 'Status',
                    field: 'status',
                    formatter:function(value){
                        var statusValue=value.replace(/[^a-z0-9]/g,'');
                        statusValue='status'+'-'+statusValue;
                        return '<span class="'+statusValue+'">'+value+'</span>';
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
                    field: 'date',
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
    table.queryParams=function(params){
        if(table.vars.filters){
            return $.extend({},params,table.vars.filters);
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
            $table.bootstrapTable('remove', {
                field: 'id',
                values: [row.id]
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
    table.setupToolbar=function(){
        $table.on('check.bs.table uncheck.bs.table ' +
            'check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
            // save your data, here just save the current page
            selections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });
    };

    table.init();

})(jQuery, listTicketTable);