<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/7/18
 * Time: 5:41 PM
 */

namespace Ousamox\MessagesManager\Services;


use Ousamox\MessagesManager\Models\User;

class OAuthorization
{

    public static function userAuthorized() {
        return self::currentUser();
    }

    /**
     * @param $users array|int
     * @return bool
     */
    public static function usersExist($users) {
        if (is_int($users)) {
            return (Models::user()::find(1)) ? true : false;
        } elseif (is_array($users)) {
            $count = Models::user()::whereIn('id',$users)->count();
            return ($count == count($users)) ? true : false;
        } else {
            return false;
        }
    }

    public static function messageValidation($message) {
        if (!isset($message['content'])) {
            return false;
        }
        return true;
    }

    /**
     * @return User
     */
    public static function currentUser() {
        return \Auth::user();
        // For test
        // return Models::user()::find(1);
    }

}