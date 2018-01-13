<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:47
 */

namespace Ousamox\MessagesManager\Models;


use Ousamox\MessagesManager\Services\OAuthorization;

class Channel extends AppModel
{
    protected $fillable = [
        'subject',
        'last_activity',
    ];

    protected $dates = [
        'last_activity',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('omm.models.channel.table_name');
    }

    public function getTable()
    {
        return config('omm.models.channel.table_name');
    }

    public function getLastActivityAttribute() {
        $sent_at = $this->attributes['last_activity'];
        if(is_object($sent_at)) {
            /** @var \Carbon\Carbon $sent_at */
            $sent_at = $sent_at->format("Y-m-d H:i:s");
        }
        return (strtotime($sent_at));
    }

    public function users() {
        return $this->belongsToMany(User::class, config('omm.models.session.table_name'), "channel_id", "user_id")->withTimestamps()->withPivot(['is_creator','share_location'])->using(Session::class);
    }

    public function seens() {
        return $this->hasMany(MessageSeen::class, "channel_id");
    }

    public function messages() {
        return $this->hasMany(Message::class, "channel_id");
    }

    public function isPrivileged() {
        if (OAuthorization::userAuthorized()) {
            return $this->users()->where(config('omm.models.user.table_name').'.id',OAuthorization::userAuthorized()->id)->count();
        }
        return 0;
    }

    public function isOwner() {
        if (OAuthorization::userAuthorized()) {
            return $this->users()->where(config('omm.models.user.table_name').'.id',OAuthorization::userAuthorized()->id)->where(config('omm.models.session.table_name').'.is_creator',1)->count();
        }
        return 0;
    }

    public function getOrderedMessages() {
        return $this->messages()->with(['files','user'])->orderBy('created_at','DESC');
    }

    public static function aaa() {
        return "aaabbb";
    }

}