<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\v8\v0;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/8.0/Deprecation-73516-VariousGeneralUtilityMethods.html
 * @see \Ssch\TYPO3Rector\Tests\Rector\v8\v0\GetFileAbsFileNameRemoveDeprecatedArgumentsRector\GetFileAbsFileNameRemoveDeprecatedArgumentsRectorTest
 */
final class GetFileAbsFileNameRemoveDeprecatedArgumentsRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->nodeTypeResolver->isMethodStaticCallOrClassMethodObjectType(
            $node,
            new ObjectType('TYPO3\CMS\Core\Utility\GeneralUtility')
        )) {
            return null;
        }

        if (! $this->isName($node->name, 'getFileAbsFileName')) {
            return null;
        }

        if (count($node->args) === 1) {
            return null;
        }

        $node->args = [$node->args[0]];

        return $node;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove second and third argument of GeneralUtility::getFileAbsFileName()', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use TYPO3\CMS\Core\Utility\GeneralUtility;
GeneralUtility::getFileAbsFileName('foo.txt', false, true);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use TYPO3\CMS\Core\Utility\GeneralUtility;
GeneralUtility::getFileAbsFileName('foo.txt');
CODE_SAMPLE
            ),
        ]);
    }
}
