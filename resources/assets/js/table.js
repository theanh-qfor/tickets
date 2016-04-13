var listTicketTable = {};
(function ($, table) {
    var $table = $('#list-tickets-table');
    table.initVars = function () {
        table.vars = {
            filters: {},
            selections: [],
            token: $('meta[name="csrf-token"]').attr('content'),
            is_engineer: $table.data('is-engineer'),
            ajaxUrl:$table.data('ajax-url')
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
        var columns = [

            {
                field: 'state',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            }, {
                title: 'Subject',
                field: 'subject',
                width: '50%',
                valign: "middle",
                sortable: true
            },
            {
                title: 'Status',
                field: 'status',
                align: "center",
                valign: "middle",
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
                align: "center",
                valign: "middle",
                sortable: true
            }

        ];
        if (table.vars.is_engineer) {
            columns = columns.concat([{
                title: 'User',
                field: 'user_name',
                formatter: function (value) {
                    return value;
                },
                align: "center",
                valign: "middle",
                sortable: true
            },
                {
                    title: 'Assigned',
                    field: 'assigned_name',
                    formatter: function (value,object,col,html) {
                        if (!value) {
                            value = "&ndash;";
                        }
                        value = '<a class="inline-edit" data-id="'+object.id+'">' + value + '</a>';
                        return value;
                    },
                    align: "center",
                    valign: "middle",
                    class: 'col-assigned',
                    events: {
                    },
                    sortable: true
                }
            ]);
        }
        columns = columns.concat([
            {
                title: 'Date',
                field: 'created_at',
                formatter: function (value) {
                    var t = value.split(/[- :]/);
                    var date = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
                    return (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
                },
                sortable: true
            },
            {
                title: 'Action',
                formatter: table.renderActions,
                events: table.events
            }
        ]);
        $table.bootstrapTable({
            queryParams: table.queryParams,
            responseHandler: function (res) {
                $.each(res.rows, function (i, row) {
                    row.state = $.inArray(row.id, table.vars.selections) !== -1;
                });
                return res;
            },
            columns: columns


        });
        $table.on('click','.inline-edit',table.makeEditable);
        $('body').on('click','*',function(e){
            var clicked=$(e.currentTarget);
            if(clicked.is('.inline-edit')||clicked.closest('.select2-container--open').length>0) return true;
            if(clicked.closest('.assign-select-wrapper').length<=0){
                $('.assign-select-wrapper').each(function(){
                    var select=$(this).find('select');
                    select.select2('destroy');
                    $('.select2-container--open').remove();
                    var cell=select.closest('td');
                    cell.html(cell.data('html'));
                    $(this).remove();
                });
            }
        });

    };
    table.makeEditable=function(element){
        var cell=$(this).closest('td');
        if(!cell.data('ticket')) {
            cell.data('ticket',$(this).data('id'));
        }
        cell.data('html', cell.html());
        var ticket_id=cell.data('ticket');
        var id=cell.closest('tr').find();
        var select=$('<select>');
        var wrapped=$('<div class="assign-select-wrapper"></div>').on('click',function(e){
            e.stopPropagation();
        }).css({position:'absolute',left:-40,top:10}).html(select);

        cell.html(wrapped);
        select.select2({
            width:"160",
            style:{position:"absolute"},
            ajax: {
                url: table.vars.ajaxUrl+'/suggest/user',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function (data, page) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1

        });
        select.on('select2:select',function(e){
            table.ajax({
               type:"POST",
                url:table.vars.ajaxUrl+'/tickets/assign',
                data:{
                    user:e.params.data.id,
                    ticket:ticket_id
                }
            }).done(function(){
                cell.html('<a class="inline-edit">'+e.params.data.text+'</a>');
                wrapped.remove();
            });

        });
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
            if ($('#list-tickets-table').data("is-engineer") == "1"){
                $('.subject').prop("disabled", true);
                $('.description').prop("disabled", true);
                $('.importance').prop("disabled", true);
                $('.status').prop("disabled", true);
                $('input[type="submit"]').hide();
            }

            //get ticket file list and comment list via ajax
            $.ajax({

                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url : window.location.href + "get_files_and_comments",
                data: {id: row.id},
                dataType: 'json',
                success: function (data) {
                    for (i = 0; i < data.files.length; i++) {
                        document.getElementById('filelist').innerHTML += '<a id="' + data.files[i].id + '" href="'+ data.files[i].file_path + data.files[i].file_name + '">' + data.files[i].file_name + '</a></br>';
                    }
                    for (i = 0; i < data.comments.length; i++) {
                        $('.more-comments').prepend("" +
                            "<div class='comment-form'>" +
                            "<div class='comment-author col-lg-3'>" +data.comments[i].username + "</div>" +
                            "<div class='comment-content col-lg-9'>" +
                            "<span class='comment-time'>" +data.comments[i].created_at + "</span>"+
                            data.comments[i].comment +
                            "</div></div>");
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        },
        'click .remove': function (e, value, row, index) {
            table.delete([row.id]).done(function () {
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
            table.delete(ids).done(function () {
                table.refresh()
            });
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
    table.delete = function (ids) {
        return table.ajax({type: "DELETE", url: table.getOptions()['url'] + '?' + $.param({'tickets': ids})});
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