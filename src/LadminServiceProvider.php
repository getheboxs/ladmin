<?php

namespace Hexters\Ladmin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Hexters\Ladmin\Helpers\Menu;
use Illuminate\Contracts\Auth\Authenticatable;
/**
 * Components
 */
use Hexters\Ladmin\Components\Card;
use Hexters\Ladmin\Components\Input;
use Hexters\Ladmin\Components\Menus\Sidebar;
use Hexters\Ladmin\Components\Menus\Toprightmenu;
use Hexters\Ladmin\Components\Cores\Breadcrumb;
use Hexters\Ladmin\Components\Datatables;


class LadminServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
    
        /**
         * Load view template
         */
        $this->loadViewsFrom( __DIR__ . '/../Resources/Views', 'ladmin');

        /**
         * Publish 
         * php artisan vendor:publish --tag=assets --force
         */
        $this->publishes([
            __DIR__ . '/../dist/app.js' => public_path('/js/ladmin/app.js'),
            __DIR__ . '/../dist/app.css' => public_path('/css/ladmin/app.css'),
            __DIR__ . '/../Resources/sass/' => base_path('/resources/sass/ladmin'),
            __DIR__ . '/../Resources/js/' => base_path('/resources/js/ladmin'),
        ], 'assets');

        /**
         * Merge asset
         */
        if(file_exists(base_path('webpack.mix.js'))) {
            try {
                $merge = __DIR__ . '/../webpack.merge.tmp';
                file_put_contents(base_path('webpack.mix.js'), $merge);
            } catch (\Exception $e) {}
        }

        /**
         * Publish 
         * php artisan vendor:publish --tag=core
         */
        $this->publishes([
            __DIR__ . '/Menus/' => app_path('/Menus'),
            __DIR__ . '/config/ladmin.php' => base_path('/config/ladmin.php'),
            __DIR__ . '/Http/Controllers/' => app_path('Http/Controllers/Administrator'),
            __DIR__ . '/Http/Middleware/LadminAuthenticate.php' => app_path('Http/Middleware/LadminAuthenticate.php'),
            __DIR__ . '/Repositories/' => app_path('Repositories'),
            __DIR__ . '/../Resources/Views/vendor/' => base_path('/resources/views/vendor/ladmin/')
        ], 'core');

        /**
         * Migration file
         */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        /**
         * View Component
         */
        $this->loadViewComponentsAs('ladmin', [
            Card::class,
            Input::class,
            Sidebar::class,
            Breadcrumb::class,
            Toprightmenu::class,
            Datatables::class
        ]);
        

        /**
         * definde gates
         */
        $menu = new Menu;
        foreach($menu->gates($menu->menus) as $gate) {
            Gate::define($gate, function(Authenticatable $user) use ($gate) {
                foreach($user->roles as $role) {
                    return in_array($gate, $role->gates);
                }
            });
        }
    }
}
