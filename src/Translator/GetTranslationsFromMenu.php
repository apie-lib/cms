<?php
namespace Apie\Cms\Translator;

use Apie\Cms\MenuStructure\MainMenuBuilder;
use Apie\Common\Concerns\ExpandsTranslationWithBoundedContext;
use Apie\Common\MenuStructure\MenuNode;
use Apie\Common\Translator\TranslationStringProviderInterface;
use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\MenuHeader;

class GetTranslationsFromMenu implements TranslationStringProviderInterface
{
    use ExpandsTranslationWithBoundedContext;

    public function __construct(private readonly MainMenuBuilder $menuBuilder)
    {
    }

    public function provideStringTranslations(ApieContext $apieContext): TranslationStringSet
    {
        $root = $this->menuBuilder->buildMenu($apieContext);
        /** @var array<int, MenuHeader> $alreadyFound */
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
     * @param array<int, MenuHeader>& $alreadyFound
     * @return array<int, MenuHeader>
     */
    private function collect(MenuNode $menuNode, array& $alreadyFound): array
    {
        $translation = $menuNode->name->withoutBoundedContextId()->withoutResourceIdentifier();
        $alreadyFound[] = $translation;
        $alreadyFound[] = $translation->withAuthenticated(true);
        $alreadyFound[] = $translation->withAuthenticated(false);

        foreach ($menuNode->children as $child) {
            $this->collect($child, $alreadyFound);
        }

        return $alreadyFound;
    }
}
