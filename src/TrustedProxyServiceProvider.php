<?php  namespace Fideloper\Proxy;

use Illuminate\Support\ServiceProvider;

class TrustedProxyServiceProvider extends ServiceProvider {

    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $packageConfigFileSrc = __DIR__.'/trusted-proxy.php';
        $packageConfigFileDest = config_path('trusted-proxy.php');

        $this->publishes([
            $packageConfigFileSrc => $packageConfigFileDest,
        ]);
    }
}
