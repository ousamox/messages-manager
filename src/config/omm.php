<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/6/18
 * Time: 12:14 PM
 */

return [

    // Models default configuration

    'models' => [
        'channel' => [
            'table_name' => 'OMM_CHANNELS',
            'class' => \Ousamox\MessagesManager\Models\Channel::class,
        ],
        'device' => [
            'table_name' => 'OMM_DEVICES',
            'class' => \Ousamox\MessagesManager\Models\Device::class,
        ],
        'message' => [
            'table_name' => 'OMM_MESSAGES',
            'class' => \Ousamox\MessagesManager\Models\Message::class,
        ],
        'message_seen' => [
            'table_name' => 'OMM_MESSAGE_SEENS',
            'class' => \Ousamox\MessagesManager\Models\MessageSeen::class,
        ],
        'message_file' => [
            'table_name' => 'OMM_MESSAGE_FILES',
            'class' => \Ousamox\MessagesManager\Models\MessageFile::class,
        ],
        'session' => [
            'table_name' => 'OMM_SESSIONS',
            'class' => \Ousamox\MessagesManager\Models\Session::class,
        ],
        'user' => [
            'table_name' => 'OMM_USERS',
            'class' => \Ousamox\MessagesManager\Models\User::class,
        ],
    ],


    // Channel duplication : This parameter allow/disallow duplication into channel (multiple channels with the same users)

    'channels_duplication' => env('OMM_CHANNELS_DUPLICATION',false),

];