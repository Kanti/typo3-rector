<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\v12\v0\typoscript;

use Helmich\TypoScriptParser\Parser\AST\Operator\Assignment;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Rector\Core\Provider\CurrentFileProvider;
use Rector\Core\ValueObject\Application\File;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Collector\RemoveTypoScriptStatementCollector;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\AbstractTypoScriptRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-97927-RemovedTypoScriptOptionConfigdoctypeSwitch.html
 * @see \Ssch\TYPO3Rector\Tests\Rector\v12\v0\typoscript\RemoveConfigDoctypeSwitchRector\RemoveConfigDoctypeSwitchRectorTest
 */
final class RemoveConfigDoctypeSwitchRector extends AbstractTypoScriptRector
{
    /**
     * @readonly
     */
    private RemoveTypoScriptStatementCollector $removeTypoScriptStatementCollector;

    /**
     * @readonly
     */
    private CurrentFileProvider $currentFileProvider;

    public function __construct(
        RemoveTypoScriptStatementCollector $removeTypoScriptStatementCollector,
        CurrentFileProvider $currentFileProvider
    ) {
        $this->removeTypoScriptStatementCollector = $removeTypoScriptStatementCollector;
        $this->currentFileProvider = $currentFileProvider;
    }

    public function enterNode(Statement $statement): void
    {
        if (! $statement instanceof Assignment) {
            return;
        }

        if ($statement->object->absoluteName !== 'config.doctypeSwitch') {
            return;
        }

        $file = $this->currentFileProvider->getFile();

        if (! $file instanceof File) {
            return;
        }

        $this->removeTypoScriptStatementCollector->removeStatement($statement, $file);
        $this->hasChanged = true;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove config.doctypeSwitch', [new CodeSample(
            <<<'CODE_SAMPLE'
config.doctypeSwitch = 1
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
-
CODE_SAMPLE
        )]);
    }
}
