<?php
namespace Apie\Cms\Translator;

use Apie\Cms\MenuStructure\MainMenuBuilder;
use Apie\Common\Concerns\ExpandsTranslationWithBoundedContext;
use Apie\Common\MenuStructure\MenuNode;
use Apie\Common\Translator\TranslationStringProviderInterface;
use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\MenuHeader;
use Apie\Core\Translator\ValueObjects\TranslationString;

class GetTranslationsFromMenu implements TranslationStringProviderInterface
{
    use ExpandsTranslationWithBoundedContext;

    public function __construct(private readonly MainMenuBuilder $menuBuilder)
    {
    }

    public function provideStringTranslations(ApieContext $apieContext): TranslationStringSet
    {
        $root = $this->menuBuilder->buildMenu($apieContext);
        /** @var array<int, TranslationString|MenuHeader> $alreadyFound */
        $alreadyFound = [];
        return new TranslationStringSet(
            iterator_to_array(
                $this->iterateOverTranslationWithAllResources(
                    $apieContext,
                    $this->collect($root, $alreadyFound),
                    true
                )
            )
        );
    }

    /**
     * @param array<int, TranslationString|MenuHeader>& $alreadyFound
     * @return array<int, TranslationString|MenuHeader>
     */
    private function collect(MenuNode $menuNode, array& $alreadyFound): array
    {
        $translation = is_string($menuNode->name) ? new TranslationString($menuNode->name ? : 'apie.menu.home') : $menuNode->name->withoutBoundedContextId()->withoutResourceIdentifier();
        $alreadyFound[] = $translation;
        if ($translation instanceof MenuHeader) {
            $alreadyFound[] = $translation->withAuthenticated(true);
            $alreadyFound[] = $translation->withAuthenticated(false);
        }

        foreach ($menuNode->children as $child) {
            $this->collect($child, $alreadyFound);
        }

        return $alreadyFound;
    }
}
