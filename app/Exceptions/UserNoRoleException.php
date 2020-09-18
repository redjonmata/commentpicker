<?php


namespace App\Exceptions;

use Exception;

class UserNoRoleException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        \Log::debug('User Has No Role');
    }
}