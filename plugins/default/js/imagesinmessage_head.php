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
        if ($('.message-form-form').length) {
            var $inputCamera =
                    '<div class="image-message-add-photo" onclick="document.getElementById(\'uploadImageInMessage\').click();">' +
                    '    <form id="message-image" class="ossn-form" method="post" enctype="multipart/form-data">' +
                    '        <fieldset>' +
                    '            <input type="hidden" name="ossn_ts" value="<?php echo $timestamp; ?>">' +
                    '            <input type="hidden" name="ossn_token" value="<?php echo $token; ?>">' +
                    '            <input type="file" id="uploadImageInMessage" name="uploadImageInMessage" class="overflow-hidden" accept="image/*" style="display: contents; position: relative; top: -1000px;">' +
                    '        </fieldset>' +
                    '        <i class="fa fa-camera"></i>' +
                    '    </form>' +
                    '</div>';
            $($inputCamera).prependTo('.message-form-form .controls');
            $('<div class="image-data"></div><input type="hidden" name="image-attachment"/>').insertAfter('.ossn-message-pling');
            $('#message-append-'+$to).animate({ scrollTop: $('#message-append-'+$to)[0].scrollHeight+1000}, 1000);
            Ossn.SentImageInMessage();
        }

        // issue #9
        $('#message-append-'+$to).imagesLoaded( function() {
            $('#message-append-'+$to).animate({ scrollTop: $('#message-append-'+$to)[0].scrollHeight+1000}, 1000);
        });
    });
</script>