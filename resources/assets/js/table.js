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
        if(typeof args=='object'){
            for(var key in args){
                if(args.hasOwnProperty(key)){
                    table.vars.filters[key]=args[key];
                }
            }
        }else {
            table.vars.filters ={};
        }
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
                    field: 'created_at',
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
            alert('You click edit action, row: ' + JSON.stringify(row));
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
        $status_filter=$('#status-filter');
        $importance_filter=$('#importance-filter');
        $status_filter.on('change',function(){
            table.filter({'status':$(this).val()});
            table.refresh();
        });
        $importance_filter.on('change',function(){
            table.filter({'importance':$(this).val()})
            table.refresh();
        });
    };
    table.refresh=function(){
        $table.bootstrapTable('refresh');
    };
    if($table.length==1) {
        table.init();
        table.setupToolbar();
    }

})(jQuery, listTicketTable);