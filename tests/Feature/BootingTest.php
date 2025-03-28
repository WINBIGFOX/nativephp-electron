<?php

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\ExpectationFailedException;

use function Orchestra\Testbench\remote;

it('can boot up the app', function () {
    Artisan::call('native:install', ['--force' => true, '--no-interaction' => true]);

    $output = '';

    $process = remote('native:serve --no-dependencies --no-interaction -v');
    $process->start(function ($type, $line) use (&$output) {
        // echo $line; // Uncomment this line to debug
        $output .= $line;
    });

    // $process->wait(); // Uncomment this line to debug

    try {
        retry(10, function () use ($output) {
            // Wait until port 8100 is open
            dump('Waiting for port 8100 to open...');

            $fp = @fsockopen('127.0.0.1', 8100, $errno, $errstr, 1);
            if ($fp === false) {
                throw new Exception(sprintf(
                    'Port 8100 is not open yet. Output: "%s", Errstr: "%s"',
                    $output,
                    $errstr
                ));
            }
        }, 5000);
    } finally {
        $process->stop();
    }

    try {
        expect($output)->toContain('Running the dev script with npm');
    } catch (ExpectationFailedException) {
        throw new ExpectationFailedException(sprintf(
            '"%s" does not match the expected output.',
            $output,
        ));
    }
});
