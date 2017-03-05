<?php namespace Syscover\Core\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * Class CoreController
 * @package Syscover\Pulsar\Core
 */

abstract class CoreController extends BaseController
{
    public function __construct(Request $request)
    {

    }
}