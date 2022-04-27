<?php
/**
 * Open Source Social Network
 *
 * @package   ImagesInMessage
 * @author    Rafael Amorim <amorim@rafaelamorim.com.br>
 * @copyright (C) Rafael Amorim
 * @license   OSSNv4  http://www.opensource-socialnetwork.org/licence/
 * @link      https://www.rafaelamorim.com.br/
 * 
 * Parts of code in this component are from OssnComments, created by 
 * @author    Open Social Website Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * 
 */

/* Define Paths */
define('__IMAGES_IN_MESSAGE__', ossn_route()->com . 'ImagesInMessage/');

//Load Class 
if (com_is_active('OssnMessages')){  //Error when disable OssnMessage component bug - #5
    require_once(__IMAGES_IN_MESSAGE__ . 'classes/ImagesInMessage.php');
} 

function ImagesInMessage_page($pages) {
    $page = $pages[0];
    switch ($page) {
        case 'attachment':
            if (isset($_FILES['uploadImageInMessage'])){  // Warning when selecting an image #6
                header('Content-Type: application/json');
                if (isset($_FILES['uploadImageInMessage']['tmp_name']) && ($_FILES['uploadImageInMessage']['error'] == UPLOAD_ERR_OK && $_FILES['uploadImageInMessage']['size'] !== 0) && ossn_isLoggedin()) {
                    //code of comment picture preview ignores EXIF header #1056
                    $OssnFile = new OssnFile;
                    $OssnFile->resetRotation($_FILES['uploadImageInMessage']['tmp_name']);

                    if (preg_match("/image/i", $_FILES['uploadImageInMessage']['type'])) {
                        $file = $_FILES['uploadImageInMessage']['tmp_name'];
                        $unique = time() . '-' . substr(md5(time()), 0, 6) . '.jpg';
                        $newfile = ossn_get_userdata("messages/photos/".ossn_loggedin_user()->guid."/{$unique}");  // issue #1
                        $dir = ossn_get_userdata("messages/photos/".ossn_loggedin_user()->guid);
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
            } else {
                error_log('Input uploadImageInMessage is not set');
            }
            break;
        case 'staticimage':
            $image = base64_decode(input('image'));
            if (!empty($image)) {
                $file = ossn_string_decrypt(base64_decode($image));
                header('content-type: image/jpeg');
                $file = rtrim(ossn_validate_filepath($file), '/');

                if (is_file($file)) {
                    echo file_get_contents($file);
                } else {
                    redirect();
                }
                
            } else {
                ossn_error_page();
            }
            break;
        case 'removeFile':
            $image = input('image');
            
            if (ImagesInMessage_DeleteFile($image)){
                echo json_encode(array(
                    'type' => 1
                ));
                exit;
            } else{ 
                echo json_encode(array(
                    'type' => 0
                ));
                exit;
            }
            break;
        default:
            break;
        }
    }
    
    

    function ImagesInMessage_messages_print($hook, $type, $return, $params) {
    if (strpos($return, '[image=') !== false) {  
        $text = substr($return,0, strpos($return,'[image='));
        $image = ImagesInMessage_GetFileName($return);
        $return = $text . "<img src=\"". ossn_site_url('imagesinmessage/staticimage?image='.$image)."\" data-fancybox>";
    }
    return $return;
}

function ImagesInMessage_GetFileName($message){
    $fileName = substr($message,strpos($message,'[image='),strpos($message,']',strpos($message,'[image=')));
    $fileName = str_replace('[image=','',$fileName);
    $fileName = str_replace(']','',$fileName);
    return $fileName;
}

function ImagesInMessage_DeleteFile($filename){
    $file = base64_decode($filename);
    $file = ossn_string_decrypt(base64_decode($file));
    if (unlink($file)){
        return true;
    } else {
        return false;
    }
}

/**
 * Delete images sent for user in messages/photos folder
 *
 * @param string $callback Name of callback
 * @param string $type Callback type
 * @param array $params Arrays or Objects
 *
 * @return void
 * @access private
 */
function ImageInMessage_UserDelete($callback, $type, $params) {
    
    $imagesInMessage = new ImagesInMessage();
    if (isset($params['entity']->guid)) {
        $dir = ossn_get_userdata("messages/photos/".$params['entity']->guid);
        if (file_exists($dir)){
            $imagesInMessage->deleteDir($dir);
        }
    }
}


/**
 * Initialize the component.
 */
function ImagesInMessage_init() {

    //js to fix #9
    ossn_new_external_js('imagesinmessage.imagesloaded.pkgd.min.js', ossn_add_cache_to_url('components/ImagesInMessage/vendors/imagesloaded.pkgd.min.js'));
    ossn_load_external_js('imagesinmessage.imagesloaded.pkgd.min.js');

    //Error when disable OssnMessage component bug - #5
    if (com_is_active('OssnMessages') && ossn_isLoggedin()) {
        //css
        ossn_extend_view('css/ossn.default', 'css/imagesinmessage');
    
        //js
        ossn_extend_view('ossn/site/head', 'js/imagesinmessage_head');
        ossn_extend_view('js/ossn.site', 'js/imagesinmessage');
        
        
        //page
        ossn_register_page('imagesinmessage', 'ImagesInMessage_page');

        //callbacks
        ossn_register_callback('user', 'delete', 'ImageInMessage_UserDelete');

        //action
        ossn_unregister_action('message/send');
        ossn_unregister_action('message/delete');
        ossn_register_action('message/send', __IMAGES_IN_MESSAGE__ . 'actions/message/send.php');
        ossn_register_action('message/delete', __IMAGES_IN_MESSAGE__ . 'actions/message/delete.php');
    }
    // transform [image= tag in <img src=
     ossn_add_hook('message', 'print', 'ImagesInMessage_messages_print');
}

ossn_register_callback('ossn', 'init', 'ImagesInMessage_init', 300);