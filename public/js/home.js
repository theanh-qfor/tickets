$(function() {
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',

        browse_button : 'browse', // you can pass in id...

        url : $("#browse").data("href"),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        init: {
            PostInit: function() {
                document.getElementById('filelist').innerHTML = '';
            },

            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                    up.start();
                });
            },

            FileUploaded: function(up, file, object) {
                if (object.response){
                    var json_object = JSON.parse(object.response);
                    console.log(json_object);
                    $('.file-array').append('<input type="hidden" name="qty[]" value="' + json_object.id + '" />');
                }
            }
        }
    });

    uploader.init();

    $('button[data-dismiss="modal"]').click(function(){
        $('.modal-title').html("Add Ticket");
        $('.form-control.id').val("");
        $('.hidden-id').val("");
        $('.form-control.date').val(getTodayDate());
        $('.subject').val("");
        $('.description').val("");
        $('.importance').val("low");
        $('.status').val("new");
        $('#filelist').empty();
        $('.file-array').empty();
    });
    function getTodayDate(){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        if(dd<10) {
            dd='0'+dd
        }

        if(mm<10) {
            mm='0'+mm
        }

        today = mm+'/'+dd+'/'+yyyy;
        return today;
    }
});