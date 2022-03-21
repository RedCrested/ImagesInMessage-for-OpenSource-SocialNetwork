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
<script>
    $(document).ready(function () {
        var $to = $("input[name=to]").val();
        console.log("to:"+$to);
        //Ossn.RegisterStartupFunction(function () {
            if ($('.message-form-form').length) {
                var $inputCamera =
                        '<div class="image-message-add-photo" onclick="document.getElementById(\'uploadImageInMessage\').click();">' +
                        '    <form id="message-image" class="ossn-form" method="post" enctype="multipart/form-data">' +
                        '        <fieldset>' +
                        '            <input type="hidden" name="ossn_ts" value="<?php echo $timestamp; ?>">' +
                        '            <input type="hidden" name="ossn_token" value="<?php echo $token; ?>">' +
                        '            <input type="file" id="uploadImageInMessage" name="uploadImageInMessage" class="overflow-hidden" style="display: contents; position: relative; top: -1000px;">' +
                        '        </fieldset>' +
                        '        <i class="fa fa-camera"></i>' +
                        '    </form>' +
                        '</div>';
                $($inputCamera).prependTo('.message-form-form .controls');
                $('<div class="image-data"></div><input type="hidden" name="image-attachment"/>').insertAfter('.ossn-message-pling');
                $('#message-append-'+$to).animate({ scrollTop: $('#message-append-'+$to)[0].scrollHeight+500}, 1000);
                Ossn.SentImageInMessage();
            }
        //});
    });

    Ossn.SentImageInMessage = function () {
        $(document).ready(function () {
            $("#uploadImageInMessage").on('change', function (event) {
                event.preventDefault();
                var formData = new FormData($('#message-image')[0]);
                $.ajax({
                    url: Ossn.site_url + 'imagesinmessage/attachment',
                    type: 'POST',
                    data: formData,
                    async: true,
                    beforeSend: function () {
                        $('.controls').find('.image-data')
                                .html('<img src="' + Ossn.site_url + 'components/ImagesInMessage/images/loading.gif" style="width:30px;border:none;height: initial;" />');
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (callback) {
                        if (callback['type'] == 1) {
                            $('.controls').each(function () {
                                $(this).find('input[name="image-attachment"]').val(callback['file']);
                                $(this).find('.image-data')
                                        .html('<img src="' + Ossn.site_url + 'imagesinmessage/staticimage?image=' + callback['file'] + '" />');
                            });
                        }
                        if (callback['type'] == 0) {
                            $('.controls').each(function () {
                                $(this).find('input[name="image-attachment"]').val('');
                            });
                            Ossn.MessageBox('syserror/unknown');
                        }
                    },
                });
            });
        });
    };
</script>