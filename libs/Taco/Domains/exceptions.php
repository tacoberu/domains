<?php

/**
 * This file is part of the Taco\Dhé
 *
 * Copyright (c) 2011 Martin Takáč (http://taco-beru.name)
 */

namespace Taco\Domains;



/**
 * Záznam se nepodařilo nalézt.
 */
class NotFoundException extends \RuntimeException
{
}



/**
 * The exception that is thrown when a method call is invalid for the object's
 * current state, method has been invoked at an illegal or inappropriate time.
 */
class InvalidStateException extends \RuntimeException
{
}

