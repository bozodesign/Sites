<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4107cfe0f2af5b08e87d5c11398c4fbb
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4107cfe0f2af5b08e87d5c11398c4fbb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4107cfe0f2af5b08e87d5c11398c4fbb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
