<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\General;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Type\ObjectType;
use Rector\Core\NodeManipulator\ClassDependencyManipulator;
use Rector\Core\Rector\AbstractRector;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/DependencyInjection/Index.html
 * @see \Ssch\TYPO3Rector\Tests\Rector\General\InjectMethodToConstructorInjectionRector\InjectMethodToConstructorInjectionRectorTest
 */
final class InjectMethodToConstructorInjectionRector extends AbstractRector
{
    /**
     * @readonly
     */
    private ClassDependencyManipulator $classDependencyManipulator;

    public function __construct(ClassDependencyManipulator $classDependencyManipulator)
    {
        $this->classDependencyManipulator = $classDependencyManipulator;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            '',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
namespace App\Service;

use \TYPO3\CMS\Core\Cache\CacheManager;

class Service
{
    private CacheManager $cacheManager;

    public function injectCacheManager(CacheManager $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
namespace App\Service;

use \TYPO3\CMS\Core\Cache\CacheManager;

class Service
{
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        $injectMethods = array_filter(
            $node->getMethods(),
            static fn ($classMethod) => str_starts_with((string) $classMethod->name, 'inject')
        );

        if ($injectMethods === []) {
            return null;
        }

        foreach ($injectMethods as $injectMethod) {
            $params = $injectMethod->getParams();
            if ($params === []) {
                continue;
            }

            reset($params);

            /** @var Param $param */
            $param = current($params);

            if (! $param->type instanceof FullyQualified) {
                continue;
            }

            $paramName = $this->getName($param->var);

            if ($paramName === null) {
                continue;
            }

            $this->classDependencyManipulator->addConstructorDependency(
                $node,
                new PropertyMetadata($paramName, new ObjectType((string) $param->type), Class_::MODIFIER_PROTECTED)
            );
            $this->removeNodeFromStatements($node, $injectMethod);
        }

        return $node;
    }

    private function shouldSkip(Class_ $class): bool
    {
        return $class->getMethods() === [];
    }

    /**
     * @param Class_ | ClassMethod | Function_ $nodeWithStatements
     */
    private function removeNodeFromStatements(Node $nodeWithStatements, Node $toBeRemovedNode): void
    {
        foreach ((array) $nodeWithStatements->stmts as $key => $stmt) {
            if ($toBeRemovedNode !== $stmt) {
                continue;
            }

            $this->removeNode($stmt);
            unset($nodeWithStatements->stmts[$key]);
            break;
        }
    }
}
