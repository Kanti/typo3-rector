<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\v8\v4;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/8.4/Deprecation-78193-ExtensionManagementUtilityextRelPath.html
 * @see \Ssch\TYPO3Rector\Tests\Rector\v8\v4\ExtensionManagementUtilityExtRelPathRector\ExtensionManagementUtilityExtRelPathRectorTest
 */
final class ExtensionManagementUtilityExtRelPathRector extends AbstractRector
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
            new ObjectType('TYPO3\CMS\Core\Utility\ExtensionManagementUtility')
        )) {
            return null;
        }

        if (! $this->isName($node->name, 'extRelPath')) {
            return null;
        }

        $node->name = new Identifier('extPath');

        return $this->nodeFactory->createStaticCall(
            'TYPO3\CMS\Core\Utility\PathUtility',
            'getAbsoluteWebPath',
            [$node]
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Substitute ExtensionManagementUtility::extRelPath()', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$relPath = ExtensionManagementUtility::extRelPath('my_extension');
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$relPath = PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('my_extension'));
CODE_SAMPLE
            ),
        ]);
    }
}
