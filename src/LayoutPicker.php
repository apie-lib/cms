<?php
namespace Apie\Cms;

use Apie\HtmlBuilders\Enums\LayoutEnum;
use Psr\Http\Message\ServerRequestInterface;

final class LayoutPicker
{
    public function pickLayout(ServerRequestInterface $request): LayoutEnum
    {
        $params = $request->getQueryParams();
        if (is_string($params['layout'] ?? null)) {
            $enum = LayoutEnum::tryFrom($params['layout']);
            if ($enum) {
                return $enum;
            }
        }

        return LayoutEnum::LAYOUT;
    }
}
