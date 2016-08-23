<?php

/**
 * Created by PhpStorm.
 * User: oshry
 * Date: 19/08/2016
 * Time: 3:28 PM
 */
namespace Common;
class CommonMethods{
    public function isAjaxing(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return true;
        }
        return false;
    }
}