<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:05
 */

namespace Ousamox\MessagesManager\Models;

class User extends AppModel
{

    const DEFAULT_PHOTO = [
        'placeholder'
    ];
    const PHOTO_PATH = 'profiles/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'role',
        'password',
        'photo',
        'locale',
        'remember_token',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'password',
        'last_login',
        'remember_token',
//        'pivot',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = [
        'last_login',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'photos',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('omm.models.user.table_name');
    }

    public function getTable()
    {
        return config('omm.models.user.table_name');
    }

    public function getPhotosAttribute(){
        $photo = $this->imageName();
        $extension = $this->imageExtension();
        return [
            'origin' => $this->photo,
            'xs' => self::getImageURL($photo,'_xs', $extension),
            's' => self::getImageURL($photo,'_s', $extension),
            'm' => self::getImageURL($photo,'_m', $extension),
            'l' => self::getImageURL($photo,'_l', $extension),
            'xl' => self::getImageURL($photo,'_xl', $extension),
        ];
    }

    public static function getImageURL($hashName,$size = "", $extension = "jpg"){
        if (in_array($hashName, self::DEFAULT_PHOTO)){
            $size = "";
        }
        return '/' . self::PHOTO_PATH . $hashName . $size . '.' . $extension;

    }

    private function imageName(){
        $last = explode('/',$this->photo);
        return explode('.',end($last))[0];
    }

    private function imageExtension(){
        $last = explode('/',$this->photo);
        $temp = explode('.',end($last));
        return $temp[count($temp) - 1];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function channels()
    {
        return $this->belongsToMany(Channel::class, config('omm.models.session.table_name'), "user_id", "channel_id")->withTimestamps()->withPivot(['is_creator','share_location'])->using(Session::class);
    }

}