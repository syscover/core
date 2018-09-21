<?php namespace Syscover\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Syscover\Core\Traits\CanManageCrud;
use Syscover\Core\Traits\CanManageDataLang;

/**
 * Class Model
 * @package Syscover\Pulsar\Core
 */

class CoreModel extends BaseModel
{
    use CanManageCrud, CanManageDataLang;
}