<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Tests\Rector\__Major__\__Minor__\__Type__\__Test_Directory__;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class __Name__Test extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<array<string>>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
