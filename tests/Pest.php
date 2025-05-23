<?php

use Native\Electron\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;

uses(TestCase::class)
    ->group('feature')
    ->in('Feature');

uses(TestCase::class)
    ->group('unit')
    ->in('Unit');

function testsDir(string $path = ''): string
{
    return __DIR__.'/'.$path;
}

function createFiles(array|string $paths): array
{
    $paths = (array) $paths;
    $filesystem = new Filesystem;

    foreach ($paths as $path) {
        $filesystem->dumpFile($path, '');
    }

    return $paths;
}
