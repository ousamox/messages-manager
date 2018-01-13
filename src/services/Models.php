<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/6/18
 * Time: 12:44 PM
 */

namespace Ousamox\MessagesManager\Services;

use Ousamox\MessagesManager\Models\AppModel;
use Ousamox\MessagesManager\Models\Channel;
use Ousamox\MessagesManager\Models\Device;
use Ousamox\MessagesManager\Models\Message;
use Ousamox\MessagesManager\Models\MessageFile;
use Ousamox\MessagesManager\Models\MessageSeen;
use Ousamox\MessagesManager\Models\Session;
use Ousamox\MessagesManager\Models\User;

class Models
{
    const CHANNEL = 'channel';
    const DEVICE = 'device';
    const MESSAGE = 'message';
    const MESSAGE_SEEN = 'message_seen';
    const MESSAGE_FILE = 'message_file';
    const SESSION = 'session';
    const USER = 'user';

    /**
     * @return Channel
     */
    public static function channel() {
        return self::getModel(self::CHANNEL);
    }

    /**
     * @return Device
     */
    public static function device() {
        return self::getModel(self::DEVICE);
    }

    /**
     * @return Message
     */
    public static function message() {
        return self::getModel(self::MESSAGE);
    }

    /**
     * @return MessageSeen
     */
    public static function messageSeen() {
        return self::getModel(self::MESSAGE_SEEN);
    }

    /**
     * @return MessageFile
     */
    public static function messageFile() {
        return self::getModel(self::MESSAGE_FILE);
    }

    /**
     * @return Session
     */
    public static function session() {
        return self::getModel(self::SESSION);
    }

    /**
     * @return User
     */
    public static function user() {
        return self::getModel(self::USER);
    }

    /**
     * @param $name
     * @return AppModel
     */
    public static function getModel($name) {
        $class_name = config('omm.models.'.$name.'.class');
        return $class_name;
    }
}