<?php
namespace Apie\Cms\Translator;

use Apie\Cms\MenuStructure\MainMenuBuilder;
use Apie\Common\MenuStructure\MenuNode;
use Apie\Common\Translator\TranslationStringProviderInterface;
use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\TranslationString;

class GetTranslationsFromMenu implements TranslationStringProviderInterface
{
    public function __construct(private readonly MainMenuBuilder $menuBuilder)
    {
    }

    public function provideStringTranslations(ApieContext $apieContext): TranslationStringSet
    {
        $root = $this->menuBuilder->buildMenu($apieContext);
        /** @var array<int, TranslationString> $alreadyFound */
        $alreadyFound = [];
        return new TranslationStringSet($this->collect($root, $alreadyFound));
    }

    /**
     * @param array<int, TranslationString>& $alreadyFound
     * @return array<int, TranslationString>
     */
    private function collect(MenuNode $menuNode, array& $alreadyFound): array
    {
        //$alreadyFound[] = new TranslationString($menuNode->name);
        foreach ($menuNode->children as $child) {
            $this->collect($child, $alreadyFound);
        }

        return $alreadyFound;
    }
}
