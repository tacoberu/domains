<?php
/**
 * This file is part of the Domains (https://github.com/tacoberu/domains)
 *
 * Copyright (c) 2004 Martin Takáč (http://martin.takac.name)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
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
