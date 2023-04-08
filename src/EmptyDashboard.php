<?php
namespace Apie\Cms;

use Stringable;

class EmptyDashboard implements Stringable
{
    public function __toString(): string
    {
        return '';
    }
}