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
                    $('.file-array').append('<input type="hidden" name="qty[]" value="' + json_object.id + '" />');
                }
            }
        }
    });

    uploader.init();

//    $("#filelist").pluploadQueue({
//        // General settings
//        runtimes : 'html5',
//        url : 'upload.php',
//        max_file_size : '10mb',
//        chunk_size : '1mb',
//        unique_names : true,
//        dragdrop : true,
//        multiple_queues : false,
//        multi_selection : false,
//        max_file_count : 100,
//
//
//        init : {
//            FilesAdded: function(up, files) {
//                document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
//                up.start();
//            },
//            UploadComplete: function(up, files) {
//                $.each(files, function(i, file) {
//                    // Do stuff with the file. There will only be one file as it uploaded straight after adding!
//                });
//            }
//        }
//    });
});