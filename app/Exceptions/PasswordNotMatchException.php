<?php


namespace App\Exceptions;

use Exception;

class PasswordNotMatchException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        \Log::debug('Password do not match');
    }
}