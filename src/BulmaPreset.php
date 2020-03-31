<?php
namespace LaravelFrontendPresets\BulmaPreset;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Laravel\Ui\Presets\Preset;
use Symfony\Component\Finder\SplFileInfo;

class BulmaPreset extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::updatePackages();
        static::updateSass();
        static::updateBootstrapping();
        static::removeNodeModules();
    }
    public static function installAuth()
    {
        static::addAuthTemplates();
        static::updateWelcomePage();
        static::scaffoldController();
    }



        

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return [
            'laravel-mix' => '^5.0.1',
            'bulma' => '^0.8.0',
            'bulma-extensions' => '^6.2.7',
        ] + Arr::except($packages, [
            'bootstrap',
            'bootstrap-sass',
            'popper.js',
            'laravel-mix',
            ]);
    }

    /**
     * Update the Sass files for the application.
     *
     * @return void
     */
    protected static function updateSass()
    {
        // clean up orphan files
        $orphan_sass_files = glob(resource_path('/sass/*.*'));

        foreach($orphan_sass_files as $sass_file)
        {
            (new Filesystem)->delete($sass_file);
        }

        copy(__DIR__.'/bulma-stubs/initial-variables.sass', resource_path('sass/initial-variables.sass'));
        copy(__DIR__.'/bulma-stubs/bulma.sass', resource_path('sass/bulma.sass'));
        copy(__DIR__.'/bulma-stubs/bulma-extensions.sass', resource_path('sass/bulma-extensions.sass'));
        copy(__DIR__.'/bulma-stubs/app.scss', resource_path('sass/app.scss'));
    }


    /**
     * Update the bootstrapping files.
     *
     * @return void
     */
    protected static function updateBootstrapping()
    {
        $file = new Filesystem;

        $file->delete(resource_path('js/bootstrap.js'));
        $file->delete(resource_path('js/app.js'));

        copy(__DIR__.'/bulma-stubs/bootstrap.js', resource_path('js/bootstrap.js'));
        copy(__DIR__.'/bulma-stubs/app.js', resource_path('js/app.js'));
        copy(__DIR__.'/bulma-stubs/bulma-extensions.js', resource_path('js/bulma-extensions.js'));
    }


    /**
     * Update the default welcome page file with Bulma buttons.
     *
     * @return void
     */
    protected static function updateWelcomePage()
    {
        // remove default welcome page
        (new Filesystem)->delete(
            resource_path('views/welcome.blade.php')
        );

        // copy new one with Bulma buttons
        copy(__DIR__.'/bulma-stubs/views/welcome.blade.php', resource_path('views/welcome.blade.php'));
    }

    /**
     * Copy Bulma Auth view templates.
     *
     * @return void
     */

    protected static function scaffoldController()
    {
        if (! is_dir($directory = app_path('Http/Controllers/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(base_path('vendor/laravel/ui/stubs/Auth')))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });
    }
    
    protected static function addAuthTemplates()
    {
        // Add Home controller
        copy(__DIR__.'/bulma-stubs/Controllers/HomeController.php', app_path('Http/Controllers/HomeController.php'));

        // Add Auth route in 'routes/web.php'
        $auth_route_entry = "Auth::routes();\n\nRoute::get('/home', 'HomeController@index')->name('home');\n\n";
        file_put_contents('./routes/web.php', $auth_route_entry, FILE_APPEND);

        // Copy Bulma Auth view templates
        (new Filesystem)->copyDirectory(__DIR__.'/bulma-stubs/views', resource_path('views'));
    }
}
