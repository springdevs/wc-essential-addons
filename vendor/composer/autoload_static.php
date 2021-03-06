<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8eb13ae930a05be9f0ebad6b6247126a
{
    public static $files = array (
        'dcf224ddec21df76c5d0a1c43df607ae' => __DIR__ . '/..' . '/raveren/kint/init.php',
        '0ec37694f2662e7dff8b629aa9ac1fe0' => __DIR__ . '/../..' . '/includes/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SpringDevs\\WcEssentialAddons\\' => 29,
        ),
        'K' => 
        array (
            'Kint\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SpringDevs\\WcEssentialAddons\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'Kint\\' => 
        array (
            0 => __DIR__ . '/..' . '/raveren/kint/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8eb13ae930a05be9f0ebad6b6247126a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8eb13ae930a05be9f0ebad6b6247126a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8eb13ae930a05be9f0ebad6b6247126a::$classMap;

        }, null, ClassLoader::class);
    }
}
