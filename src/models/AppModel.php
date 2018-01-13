<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 17:46
 */

namespace Ousamox\MessagesManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppModel
 * @property int id
 * @package Ousamox\MessagesManager\Models
 */
class AppModel extends Model
{
    use SoftDeletes;

    protected $connection = "mysql";

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}