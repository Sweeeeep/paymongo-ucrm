<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

(static function(){
    $builder = new \DI\ContainerBuilder();
    $container = $builder->build();
    $plugin = $container->get(\Sweeeeep\PaymongoUcrm\Plugin::class);
    $request = $container->get(Illuminate\Http\Request::class);
    try{
        $plugin->run($request->capture());
    } catch (Exception $e){
        echo '<pre>' . var_export($e->getMessage(), true) . '</pre>';
    }
})();