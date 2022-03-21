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

/* Define Paths */
define('__IMAGES_IN_MESSAGE__', ossn_route()->com . 'ImagesInMessage/');

/* Load Class */
require_once(__IMAGES_IN_MESSAGE__ . 'classes/ImagesInMessage.php');

function ImagesInMessage_page($pages) {
    $page = $pages[0];
    switch ($page) {
        case 'attachment':
            header('Content-Type: application/json');
            if (isset($_FILES['uploadImageInMessage']['tmp_name']) && ($_FILES['uploadImageInMessage']['error'] == UPLOAD_ERR_OK && $_FILES['file']['size'] !== 0) && ossn_isLoggedin()) {
                //code of comment picture preview ignores EXIF header #1056
                $OssnFile = new OssnFile;
                $OssnFile->resetRotation($_FILES['uploadImageInMessage']['tmp_name']);

                if (preg_match("/image/i", $_FILES['uploadImageInMessage']['type'])) {
                    $file = $_FILES['uploadImageInMessage']['tmp_name'];
                    $unique = time() . '-' . substr(md5(time()), 0, 6) . '.jpg';
                    $newfile = ossn_get_userdata("tmp/photos/{$unique}");
                    $dir = ossn_get_userdata("tmp/photos/");
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    } 
                    if (move_uploaded_file($file, $newfile)) {
                        $file = base64_encode(ossn_string_encrypt($newfile));
                        echo json_encode(array(
                            'file' => base64_encode($file),
                            'type' => 1
                        ));
                        exit;
                    } 
                }
            }
            echo json_encode(array(
                'type' => 0
            ));
            break;
        case 'staticimage':
            $image = base64_decode(input('image'));
            if (!empty($image)) {
                $file = ossn_string_decrypt(base64_decode($image));
                header('content-type: image/jpeg');
                $file = rtrim(ossn_validate_filepath($file), '/');

                $tmpphotos = ossn_get_userdata("tmp/photos/");
                $filename = str_replace($tmpphotos, '', $file);
                $file = $tmpphotos . $filename;
                //avoid slashes in the file. 
                if (strpos($filename, '\\') !== FALSE || strpos($filename, '/') !== FALSE) {
                    redirect();
                } else {
                    if (is_file($file)) {
                        echo file_get_contents($file);
                    } else {
                        redirect();
                    }
                }
            } else {
                ossn_error_page();
            }
            break;
        default:
            break;
    }
}

function imagesinmessage_messages_print($hook, $type, $return, $params) {
    
    if (strpos($return, '[image=') !== false) {  
        $text = substr($return,0, strpos($return,'[image='));
        $image = substr($return,strpos($return,'[image='),strpos($return,']',strpos($return,'[image=')));
        $image = str_replace('[image=','',$image);
        $image = str_replace(']','',$image);
        $return = $text . "<img src=\"". ossn_site_url('imagesinmessage/staticimage?image='.$image)."\" data-fancybox>";
    }
    return $return;
}

/**
 * Initialize the component.
 */
function images_in_message_init() {

    
    if (ossn_isLoggedin()) {
        //css
        ossn_extend_view('css/ossn.default', 'css/imagesinmessage');
    
        //js
        ossn_extend_view('js/ossn.site', 'js/imagesinmessage');
        
        //page
        ossn_register_page('imagesinmessage', 'ImagesInMessage_page');

        //action
        ossn_unregister_action('message/send');
        ossn_register_action('message/send', __IMAGES_IN_MESSAGE__ . 'actions/message/send.php');
    }
    // transform [image= tag in <img src=
    ossn_add_hook('message', 'print', 'imagesinmessage_messages_print');
}

ossn_register_callback('ossn', 'init', 'images_in_message_init', 300);