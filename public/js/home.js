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
        $('.subject').val("");
        $('.description').val("");
        $('.importance').val("low");
        $('.status').val("new");
        $('#filelist').empty();
        $('.file-array').empty();
    });
});