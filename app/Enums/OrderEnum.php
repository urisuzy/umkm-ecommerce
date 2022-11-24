<?php

namespace App\Enums;

use ReflectionClass;

final class OrderEnum
{
  const UNPAID = 'unpaid';
  const PAID = 'paid';
  const SENT = 'sent';
  const RECEIVED = 'received';
  const DONE = 'done';

  static function getConstants()
  {
    $oClass = new ReflectionClass(__CLASS__);
    return $oClass->getConstants();
  }
}
