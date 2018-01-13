<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:53
 */

namespace Ousamox\MessagesManager\Models;

class Session extends AppPivot
{
    public function __construct($attributes = [])
    {
        parent::__construct($attributes = []);
        $this->table = config('omm.models.session.table_name');
    }

    public function getTable()
    {
        return config('omm.models.session.table_name');
    }

    protected $fillable = [
        'is_creator',
        'share_location'
    ];

    protected $hidden = [
        'channel_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}