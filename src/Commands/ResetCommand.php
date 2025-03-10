<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Filesystem\Filesystem;

use function Laravel\Prompts\intro;

class ResetCommand extends Command
{
    protected $signature = 'native:reset {--with-app-data : Clear the app data as well}';

    protected $description = 'Clear all build and dist files';

    public function handle(): int
    {
        intro('Clearing build and dist directories...');

        // Removing and recreating the native serve resource path
        $nativeServeResourcePath = realpath(__DIR__.'/../../resources/js/resources/app/');
        $this->line('Clearing: '.$nativeServeResourcePath);

        $filesystem = new Filesystem;
        $filesystem->remove($nativeServeResourcePath);
        $filesystem->mkdir($nativeServeResourcePath);

        // Removing the bundling directories
        $bundlingPath = base_path('build/');
        $this->line('Clearing: '.$bundlingPath);

        if ($filesystem->exists($bundlingPath)) {
            $filesystem->remove($bundlingPath);
        }

        // Removing the built path
        $builtPath = base_path('dist/');
        $this->line('Clearing: '.$builtPath);

        if ($filesystem->exists($builtPath)) {
            $filesystem->remove($builtPath);
        }

        if ($this->option('with-app-data')) {

            // Fetch last generated app name
            $packageJsonPath = __DIR__.'/../../resources/js/package.json';
            $packageJson = json_decode(file_get_contents($packageJsonPath), true);
            $appName = $packageJson['name'];

            $appDataPath = $this->appDataDirectory($appName);
            $this->line('Clearing: '.$appDataPath);

            if ($filesystem->exists($appDataPath)) {
                $filesystem->remove($appDataPath);
            }
        }

        return 0;
    }

    protected function appDataDirectory(string $name): string
    {
        /*
         * Platform	Location
         * macOS	~/Library/Application Support
         * Linux	$XDG_CONFIG_HOME or ~/.config
         * Windows	%APPDATA%
         */

        return match (PHP_OS_FAMILY) {
            'Darwin' => $_SERVER['HOME'].'/Library/Application Support/'.$name,
            'Linux' => $_SERVER['XDG_CONFIG_HOME'] ?? $_SERVER['HOME'].'/.config/'.$name,
            'Windows' => $_SERVER['APPDATA'].'/'.$name,
            default => $_SERVER['HOME'].'/.config/'.$name,
        };
    }
}
