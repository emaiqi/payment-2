<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/27
 * Time: 10:11
 */

namespace king\payment\base;

class GatewayException extends \Exception
{
    public $row = [];

    public function __construct($message = "", $code = 0, $row = [])
    {
        parent::__construct($message, $code);

        $this->row = $row;
    }
}