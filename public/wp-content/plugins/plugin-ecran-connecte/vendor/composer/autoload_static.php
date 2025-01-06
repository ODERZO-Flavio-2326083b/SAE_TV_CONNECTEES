<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5af73e98c2cd35777ac7e205f4a236df
{
    public static $prefixLengthsPsr4 = array (
        'v' => 
        array (
            'views\\' => 6,
        ),
        'm' => 
        array (
            'models\\' => 7,
        ),
        'c' => 
        array (
            'controllers\\rest\\' => 17,
            'controllers\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'views\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/views',
        ),
        'models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/models',
        ),
        'controllers\\rest\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/controllers/rest',
        ),
        'controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/controllers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5af73e98c2cd35777ac7e205f4a236df::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5af73e98c2cd35777ac7e205f4a236df::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5af73e98c2cd35777ac7e205f4a236df::$classMap;

        }, null, ClassLoader::class);
    }
}
