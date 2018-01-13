<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:01
 */

namespace Ousamox\MessagesManager\Models;

class Device extends AppModel
{
    protected $fillable = [
        'user_id',
        'token',
        'device_UID',
        'name',
        'platform',
        'version',
        'brand',
        'token',
    ];

    const PLATFORMS = [
        'ios' => 'ios',
        'android' => 'android',
        'web' => 'web',
        'other' => 'other',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('omm.models.device.table_name');
    }

    public function getTable()
    {
        return config('omm.models.device.table_name');
    }

    // 1<-N

    public function user() {
        return $this->belongsTo(User::class, "user_id");
    }
}