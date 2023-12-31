<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc5a7ccf8b3bb44fd2f301ea2d503bdd0
{
    public static $prefixesPsr0 = array (
        'U' => 
        array (
            'Unirest\\' => 
            array (
                0 => __DIR__ . '/..' . '/mashape/unirest-php/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitc5a7ccf8b3bb44fd2f301ea2d503bdd0::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc5a7ccf8b3bb44fd2f301ea2d503bdd0::$classMap;

        }, null, ClassLoader::class);
    }
}
