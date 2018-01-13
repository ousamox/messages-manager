<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/6/18
 * Time: 11:34 AM
 */

namespace Ousamox\MessagesManager\Services;


use Illuminate\Database\Eloquent\Collection;
use Ousamox\MessagesManager\Models\Channel;

interface OMessageInterface
{
    /**
     * @return Collection
     */
    public static function getChannels();

    /**
     * @param Channel|int $channel
     * @return mixed
     */
    public static function getMessagesByChannel($channel);

    /**
     * @param array $messageData
     * @param array $channelData
     * @param array $toUsers
     * @param array $files
     * @return mixed
     */
    public static function sendMessageNewChannel($messageData, $channelData, $toUsers, $files = []);

    /**
     * @param array $messageData
     * @param Channel|int $channel
     * @param array $files
     * @return mixed
     */
    public static function sendMessageExistingChannel($messageData, $channel, $files = []);

    /**
     * @param Channel|int $channel
     * @param array $users
     * @return mixed
     */
    public static function addUsersToChannel($channel,$users);
}