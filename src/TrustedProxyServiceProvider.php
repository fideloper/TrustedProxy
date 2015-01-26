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
        $filePath = realpath(dirname(__FILE__));
        $packageConfigFileSrc = $filePath.'/trusted-proxy.php';
        $packageConfigFileDest = base_path('config/trusted-proxy.php');

        $this->publishes([
            $packageConfigFileSrc => $packageConfigFileDest,
        ]);
    }
}