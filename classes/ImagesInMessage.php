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

class ImagesInMessage extends OssnMessages {
    
    /**
     * Send message
     *
     * @params integer $from: User 1 guid
     * @params integer $to User 2 guid
     * @params string $message Message
     *
     * @return boolean
     */
    public function send($from, $to, $message) {
        
        if (!strlen($message) || empty($from) || empty($to)) {
            return false;
        }
        $this->data->is_deleted_from = false;
        $this->data->is_deleted_to = false;

        $message = trim(strip_tags($message));

        $params['into'] = 'ossn_messages';
        $params['names'] = array(
            'message_from',
            'message_to',
            'message',
            'time',
            'viewed'
        );
        $params['values'] = array(
            (int) $from,
            (int) $to,
            $message,
            time(),
            '0'
        );
        if ($this->insert($params)) {
            $this->lastMessage = $this->getLastEntry();
            if (isset($this->data) && is_object($this->data)) {
                foreach ($this->data as $name => $value) {
                    $this->owner_guid = $this->lastMessage;
                    $this->type = 'message';
                    $this->subtype = $name;
                    $this->value = $value;
                    $this->add();
                }
            }
            $params['message_id'] = $this->lastMessage;
            $params['message_from'] = $from;
            $params['message_to'] = $to;
            $params['message'] = $message;
            ossn_trigger_callback('message', 'created', $params);
            return $this->lastMessage;
        }
        return false;
    }
    
}

?>