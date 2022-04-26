<?php
/**
 * Open Source Social Network
 *
 * @package   ImagesInMessage
 * @author    Rafael Amorim <amorim@rafaelamorim.com.br>
 * @copyright (C) Rafael Amorim
 * @license   OSSNv4  http://www.opensource-socialnetwork.org/licence/
 * @link      https://www.rafaelamorim.com.br/
 */

$timestamp = time();
$token = ossn_generate_action_token($timestamp);

?>
//<script>
    
    Ossn.SentImageInMessage = function () {
        $(document).ready(function () {
            $("#uploadImageInMessage").on('change', function (event) {
                event.preventDefault();
                if ($('input[name="image-attachment"]').val().length > 0 ){
                    Ossn.DeleteImage();
                }

                var formData = new FormData($('#message-image')[0]);
                $.ajax({
                    url: Ossn.site_url + 'imagesinmessage/attachment',
                    type: 'POST',
                    data: formData,
                    async: true,
                    beforeSend: function () {
                        Ossn.DisableSendButton();
                        $('.controls').find('.image-data')
                                .html('<img src="' + Ossn.site_url + 'components/ImagesInMessage/images/loading.gif" style="width:30px;border:none;height: initial;" />');
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (callback) {
                        if (callback['type'] == 1) {

                            //this will work only for Chrome
                            $(window).on('beforeunload', function ()
                            {
                                Ossn.DeleteImage(false);
                            });

                            //this will work for other browsers
                            $(window).on("unload", function ()
                            {
                                Ossn.DeleteImage(false);
                            });

                            $('.controls').
                            each(function () {
                                $(this).find('input[name="image-attachment"]').val(callback['file']);
                                
                                $(this)
                                .find('.image-data')
                                .html('<div class="row">' + 
                                        '    <div class="col-2">&nbsp;</div>' +
                                        '    <div class="col-8">' + 
                                        '        <img src="' + Ossn.site_url + 'imagesinmessage/staticimage?image=' + callback['file'] + '" />'+
                                        '    </div>' +
                                        '    <div class="col-2">' +
                                        '        <i class="fa fa-trash-alt" onclick="Ossn.DeleteImage();"></i>' +
                                        '    </div>' +
                                        '</div>');
                            });
                        }
                        if (callback['type'] == 0) { 
                            $('.controls').each(function () {
                                Ossn.CleanFileBox();
                            });
                            $(window).unbind('beforeunload');
                            $(window).unbind('unload');
                        }
                    },
                    error: function (callbackError) {
                        Ossn.CleanFileBox();
                        $(window).unbind('beforeunload');
                        $(window).unbind('unload');
                        Ossn.MessageBox('syserror/unknown');
                    },
                    complete: function () {
                        Ossn.EnableSendButton();
                    },
                });
            });
        });
    };

    Ossn.DeleteImage = function (asyncOption = true) {
        $(document).ready(function () {
            $.ajax({
                url: Ossn.site_url + 'imagesinmessage/removeFile?image=' + $('input[name="image-attachment"]').val(),
                type: 'POST',
                async: asyncOption,
                cache: false,
                contentType: false,
                processData: false,
                success: function (callback) {
                    Ossn.CleanFileBox();
                    $(window).unbind('beforeunload');
                    $(window).unbind('unload');
                },
                error: function (callbackError) {
                    $(this).find('input[name="image-attachment"]').val(''); 
                    $(this).find('.image-data').html(''); 
                    $(window).unbind('beforeunload');
                    $(window).unbind('unload');
                    Ossn.MessageBox('syserror/unknown');
                },
            });
        });
    };

    Ossn.CleanFileBox = function(){
        $('.controls').find('input[name="image-attachment"]').val(''); 
        $('.controls').find('.image-data').html(''); 
    };

    Ossn.DisableSendButton = function (){
        $('.controls .btn.btn-primary').prop('disabled',true);
    }
    
    Ossn.EnableSendButton = function (){
        $('.controls .btn.btn-primary').prop('disabled',false);
    }

//</script>