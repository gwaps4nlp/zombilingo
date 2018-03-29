<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameException extends NotFoundHttpException
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
         parent::__construct($message, $previous, $code);
    }
	
}