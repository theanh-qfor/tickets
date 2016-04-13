$(function() {
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',

        browse_button : 'browse', // you can pass in id...

        url : $("#browse").data("href"),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        multipart_params: {},
        init: {
            PostInit: function() {
                document.getElementById('filelist').innerHTML = '';
            },

            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    var ticket_id = $('.hidden-id').val();
                    up.settings.multipart_params["ticket_id"] = ticket_id;
                    up.start();
                });
            },

            FileUploaded: function(up, file, object) {
                if (object.response){
                    var json_object = JSON.parse(object.response);
                    console.log(json_object);
                    document.getElementById('filelist').innerHTML += '<a id="' + json_object.id + '" href="'+ json_object.file_path + json_object.file_name + '">' + json_object.file_name + '</a></br>';
                    $('.file-array').append('<input type="hidden" name="qty[]" value="' + json_object.id + '" />');
                }
            }
        }
    });

    uploader.init();

    $('#myModal').on('hidden.bs.modal', function () {
        $('.modal-title').html("Add Ticket");
        $('.form-control.id').val("");
        $('.hidden-id').val("");
        $('.form-control.date').val(getTodayDate());
        $('.form-control.comment').val("");
        $('.subject').val("");
        $('.description').val("");
        $('.importance').val("low");
        $('.status').val("new");
        $('#filelist').empty();
        $('.file-array').empty();
        $('.more-comments').empty();
        $('.comment-array').empty();
        $('.subject').prop("disabled", false);
        $('.description').prop("disabled", false);
        $('.importance').prop("disabled", false);
        $('.status').prop("disabled", false);
        $('input[type="submit"]').show();
    });

    $('#comment-post').click(function(){
        var comment_content = $('.comment-container .comment').val();
        if (comment_content == ''){
            alert('Enter your message !');
            return;
        }
        var data = {
            comment : comment_content
        }
        if ($('.hidden-id').val() != ""){
            data.ticket_id = $('.hidden-id').val();
        }
        $.ajax({

            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : $(this).data("href"),
            data: data,
            dataType: 'json',
            success: function (data) {
                $('.more-comments').prepend("" +
                    "<div class='comment-form'>" +
                    "<div class='comment-author col-lg-3'>" +data.username + "</div>" +
                    "<div class='comment-content col-lg-9'>" +
                    "<span class='comment-time'>" +data.created_at + "</span>"+
                    data.comment +
                    "</div></div>");
                $('.comment-array').append('<input type="hidden" name="comments[]" value="' + data.id + '" />');
                $('.comment-container .comment').val("");

            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
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