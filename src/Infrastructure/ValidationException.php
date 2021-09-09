<?php
declare(strict_types=1);

namespace App\Infrastructure;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ValidationException extends BadRequestException
{

}