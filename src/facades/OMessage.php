<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/6/18
 * Time: 12:02 PM
 */

namespace Ousamox\MessagesManager\Facades;


use Illuminate\Support\Facades\Facade;

class OMessage extends Facade
{
    protected static function getFacadeAccessor() {
        return 'omm-message';
    }
}