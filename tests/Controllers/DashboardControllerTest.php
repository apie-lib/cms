<?php
namespace Apie\Tests\Cms;

use Apie\Cms\Controllers\DashboardController;
use Apie\Common\ActionDefinitionProvider;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Fixtures\BoundedContextFactory;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Factories\ResourceActionFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;

class DashboardControllerTest extends TestCase
{
    use ProphecyTrait;

    protected function givenAGetRequest(string $uri): ServerRequestInterface
    {
        $factory = new Psr17Factory();
        return $factory->createServerRequest('GET', $uri)
            ->withHeader('Accept', 'application/json')
            ->withAttribute('boundedContextId', 'default');
    }

    /**
     * @test
     */
    public function it_generates_html()
    {
        $renderer = $this->prophesize(ComponentRendererInterface::class);
        $renderer->render(Argument::type(Layout::class))
            ->shouldBeCalled()
            ->willReturn('<html></html>');
        $testItem = new DashboardController(
            new ComponentFactory(
                new ApplicationConfiguration(),
                BoundedContextFactory::createHashmap(),
                FormComponentFactory::create(),
                new ResourceActionFactory(new ActionDefinitionProvider())
            ),
            new ContextBuilderFactory(),
            $renderer->reveal()
        );
        $request = $this->givenAGetRequest('/');
        $response = $testItem($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
