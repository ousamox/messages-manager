<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:54
 */

namespace Ousamox\MessagesManager\Models;


class MessageSeen extends AppModel
{

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('omm.models.message_seen.table_name');
    }

    public function getTable()
    {
        return config('omm.models.message_seen.table_name');
    }

    protected $fillable = [
        'seen_from_lat',
        'seen_from_long',
    ];

    public function getCreatedAtAttribute() {
        $sent_at = $this->attributes['created_at'];
        if(is_object($sent_at)) {
            /** @var \Carbon\Carbon $sent_at */
            $sent_at = $sent_at->format("Y-m-d H:i:s");
        }
        return (strtotime($sent_at));
    }

    public function channel() {
        return $this->belongsTo(Channel::class, "channel_id");
    }

    public function seenBy() {
        return $this->belongsTo(User::class, "seen_by_id");
    }

}