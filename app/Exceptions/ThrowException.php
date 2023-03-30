<?php

namespace App\Exceptions;

use Exception;

class ThrowException extends Exception
{
    public function __construct($message = '', $code = 500) {
        $this->message = $message;
        $this->code = $code;

        $this->trow();
    }

    function trow(){
        throw new Exception($this->message, $this->code);
    }
}
