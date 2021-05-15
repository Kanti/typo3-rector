<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use Rector\NodeNameResolver\NodeNameResolver;

final class ClassConstAnalyzer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver
    ) {
    }

    /**
     * Detects "SomeClass::class"
     */
    public function isClassConstReference(Expr $expr, string $className): bool
    {
        if (! $expr instanceof ClassConstFetch) {
            return false;
        }

        if (! $this->nodeNameResolver->isName($expr->name, 'class')) {
            return false;
        }

        return $this->nodeNameResolver->isName($expr->class, $className);
    }
}
