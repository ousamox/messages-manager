<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/7/18
 * Time: 10:36 AM
 */

namespace Ousamox\MessagesManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppPivot
 * @package App\Models\Generic
 *
 * @method static AppModel find(int $id)
 * @method static Builder where(string $column, mixed $operatorOrValue, mixed $valueIfNoOperator = null)
 * @method static Builder whereIn(string $column, array $value)
 * @method static Builder whereHas($relation, \Closure $callback = null, $operator = '>=', $count = 1)
 * @method static AppModel create(array $attributes)
 * @method static AppModel select($columns = ['*'])
 */
class AppPivot extends Pivot
{
    use SoftDeletes;

    protected $connection = "mysql";

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}