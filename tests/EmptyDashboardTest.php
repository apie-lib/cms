<?php
namespace Apie\Tests\Cms;

use Apie\Cms\EmptyDashboard;
use PHPUnit\Framework\TestCase;

class EmptyDashboardTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_converts_into_an_empty_string()
    {
        $testItem = new EmptyDashboard();
        $this->assertEquals('', $testItem->__toString());
    }
}
