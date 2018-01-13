<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/6/18
 * Time: 11:29 AM
 */

namespace Ousamox\MessagesManager\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Ousamox\MessagesManager\Models\Channel;
use Ousamox\MessagesManager\Models\Message;
use Ousamox\MessagesManager\Models\User;

class OMessage implements OMessageInterface
{

    public static function response($code,$message,$data = null) {
        return new Collection([
            'success' => ($data) ? true : false,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * @return Collection
     */
    public static function getChannels()
    {
        try {
            if (OAuthorization::userAuthorized()) {
                $channels = Models::channel()::with([
                    'users' => function ($q) {
                        $q->where(config('omm.models.user.table_name').'.id','<>',1);
                    }
                ])
                    ->whereHas('users',function ($q) {
                        $q->where(config('omm.models.user.table_name').'.id',1);
                    })
                    ->orderBy('last_activity','DESC')
                    ->get();
                return self::response(200,'OK',$channels->toArray());
            } else {
                return self::response(401,'Unauthorized');
            }
        } catch (QueryException $e) {
            return self::response(500,'Error! Please verify that you have already migrate the package tables with correct column names, and applying modification in config/omm.php (To publish our files into your project : php artisan vendor:publish --tag=omm)');
        }
    }

    /**
     * @param Channel|int $channel
     * @return mixed
     */
    public static function getMessagesByChannel($channel)
    {
        try {
            if (is_int($channel)) {
                /** @var Channel $channel */
                $channel = Models::channel()::find($channel);
            }
            if ($channel) {
                if ($channel->isPrivileged()) {
                    return self::response(200,'OK',$channel->getOrderedMessages()->get()->toArray());
                } else {
                    return self::response(401,'Unauthorized');
                }
            } else {
                return self::response(404,'Channel not found');
            }
        } catch (QueryException $e) {
            return self::response(500,'Error! Please verify that you have already migrate the package tables with correct column names, and applying modification in config/omm.php (To publish our files into your project : php artisan vendor:publish --tag=omm)');
        }
    }

    /**
     * @param array $messageData
     * @param array $channelData
     * @param array|Collection $toUsers
     * @param array $files
     * @return mixed
     */
    public static function sendMessageNewChannel($messageData, $channelData, $toUsers, $files = [])
    {
        try {
            if (OAuthorization::userAuthorized() && OAuthorization::usersExist($toUsers) && OAuthorization::messageValidation($messageData)) {
                /** @var User $currentUser */
                $currentUser = OAuthorization::currentUser();
                $user_ids = [$currentUser->id => ['is_creator' => 1]];
                foreach ($toUsers as $user) {
                    if (!is_int($user) && isset($user->id)) {
                        $user = $user->id;
                    }
                    if ($user != $currentUser->id) {
                        $user_ids[$user] = ['is_creator' => 0];
                    }
                }
                if (count($user_ids) > 1) {
                    if (!config('omm.channels_duplication',false)) {
                        // Disallow duplication
                        $concernedChannels = $currentUser->channels()->with([
                            'users' => function ($q) use ($currentUser) {
                                $q->select(config('omm.models.user.table_name').'.id');
                            }
                        ])->get();
                        $existInLoop = 0;
                        foreach ($concernedChannels as $concernedChannel) {
                            $usersChannel = $concernedChannel->users->pluck('id')->all();
                            // count(array_intersect($search_this, $all)) == count($search_this)
                            if (count(array_intersect(array_keys($user_ids), $usersChannel)) != count($user_ids)) {
                                $existInLoop++;
                            }
                        }
                        if ($existInLoop != $concernedChannels->count()) {
                            return self::response(401,'Unauthorized : Duplicate channels detected');
                        }

                    }

                    $channelClass = Models::channel();

                    $dataChannel = ($channelData) ?? [];
                    $dataChannel['last_activity'] = Carbon::now();
                    /** @var Channel $channel */
                    $channel = new $channelClass($dataChannel);
                    $channel->save();
                    // Create session to relate channel with users
                    $channel->users()->sync($user_ids);

                } else {
                    return self::response(401,'Unauthorized : Channel must have at least 2 users');
                }

                // Create Message
                $messageClass = Models::message();
                $dataMessage = ($messageData) ?? [];
                /** @var Message $message */
                $message = new $messageClass($dataMessage);
                $message->channel()->associate($channel);
                $message->user()->associate($currentUser);
                $message->save();

                // Documents
                if (isset($dataMessage['content'])) {
                    preg_match("/(?:\w+(?:\W+|$)){0,4}/", $dataMessage['content'], $matches);
                    $title = $matches[0].' ...';
                } else {
                    $title = "--";
                }
                if ($files) {
                    $returned = Models::messageFile()::upload([
                        'documents' => $files,
                        'titre' => $title,
                        'message_id' => $message->id
                    ]);
                }

                $channelInfo = Models::channel()::with(['users'])->where('id',$channel->id)->first();
                return self::response(200,'Channel created',$channelInfo->toArray());
            } else {
                if (!OAuthorization::usersExist($toUsers)) {
                    return self::response(404,'Error : Send Message to no-existing user(s)');
                } else {
                    return self::response(401,'Unauthorized');
                }
            }
        } catch (QueryException $e) {
            return self::response(500,'Error! Please verify that you have already migrate the package tables with correct column names, and applying modification in config/omm.php (To publish our files into your project : php artisan vendor:publish --tag=omm)');
        }
    }

    /**
     * @param array $messageData
     * @param Channel|int $channel
     * @param array $files
     * @return mixed
     */
    public static function sendMessageExistingChannel($messageData, $channel, $files = [])
    {
        try {
            if (OAuthorization::userAuthorized() && OAuthorization::messageValidation($messageData)) {
                /** @var User $currentUser */
                $currentUser = OAuthorization::currentUser();
                if ($channel) {
                    if (is_int($channel)) {
                        /** @var Channel $channel */
                        $channel = Models::channel()::find($channel);
                    }
                    if ($channel && get_class($channel) == get_class(Models::channel())) {
                        if ($channel->isPrivileged()) {
                            // Update last activaty channel to get it ordered correctly
                            $channel->last_activity = Carbon::now();
                            $channel->save();
                        } else {
                            return self::response(401, 'Unauthorized');
                        }
                    } else {
                        return self::response(404, 'Channel not found');
                    }
                } else {
                    return self::response(404, 'Channel not found');
                }

                // Create Message
                $messageClass = Models::message();
                $dataMessage = ($messageData) ?? [];
                /** @var Message $message */
                $message = new $messageClass($dataMessage);
                $message->channel()->associate($channel);
                $message->user()->associate($currentUser);
                $message->save();

                // Documents
                if (isset($dataMessage['content'])) {
                    preg_match("/(?:\w+(?:\W+|$)){0,4}/", $dataMessage['content'], $matches);
                    $title = $matches[0].' ...';
                } else {
                    $title = "--";
                }
                if ($files) {
                    $returned = Models::messageFile()::upload([
                        'documents' => $files,
                        'titre' => $title,
                        'message_id' => $message->id
                    ]);
                }

                $channelInfo = Models::channel()::with(['users'])->where('id',$channel->id)->first();
                return self::response(200,'OK',$channelInfo->toArray());
            } else {
                return self::response(401,'Unauthorized');
            }
        } catch (QueryException $e) {
            return self::response(500,'Error! Please verify that you have already migrate the package tables with correct column names, and applying modification in config/omm.php (To publish our files into your project : php artisan vendor:publish --tag=omm)');
        }
    }

    /**
     * @param Channel|int $channel
     * @param array $users
     * @return mixed
     */
    public static function addUsersToChannel($channel, $users)
    {
        try {
            if (OAuthorization::userAuthorized() && OAuthorization::usersExist($users)) {
                if (!$users) {
                    return self::response(404,'Error : Send Message to empty users list');
                }

                if ($channel) {
                    if (is_int($channel)) {
                        /** @var Channel $channel */
                        $channel = Models::channel()::find($channel);
                    }
                    if ($channel && get_class($channel) == get_class(Models::channel())) {
                        if ($channel->isOwner()) {
                            // Update last activaty channel to get it ordered correctly
                            $channel->last_activity = Carbon::now();
                            $channel->save();
                        } else {
                            return self::response(401, 'Unauthorized');
                        }
                    } else {
                        return self::response(404, 'Channel not found');
                    }
                } else {
                    return self::response(404, 'Channel not found');
                }

                $user_ids = [];
                foreach ($users as $user) {
                    $user_ids[$user] = ['is_creator' => 0];
                }

                $channel->users()->syncWithoutDetaching($user_ids);

                $channelInfo = Models::channel()::with(['users'])->where('id',$channel->id)->first();
                return self::response(200,'OK',$channelInfo->toArray());
            } else {
                if (!OAuthorization::usersExist($users)) {
                    return self::response(404,'Error : Send Message to no-existing user(s)');
                } else {
                    return self::response(401,'Unauthorized');
                }
            }
        } catch (QueryException $e) {
            return self::response(500,'Error! Please verify that you have already migrate the package tables with correct column names, and applying modification in config/omm.php (To publish our files into your project : php artisan vendor:publish --tag=omm)');
        }
    }
}