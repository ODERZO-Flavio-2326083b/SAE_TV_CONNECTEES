<?php

spl_autoload_register(
    function ($className) {
        $path = ABSPATH.TV_PLUG_PATH.'src/';

        $file = $path.$className . '.php';

        if (file_exists($file)) {
            include $file;
        }
    }
);
