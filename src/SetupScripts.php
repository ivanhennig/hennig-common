<?php

namespace Hennig\Common;

class SetupScripts
{
    static public function publishJS($event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        copy(__DIR__ . "/js/H.js", "{$vendorDir}/../H.js");

        $minifier = new \MatthiasMullie\Minify\JS(__DIR__ . "/js/H.js");
        $minifier->minify("{$vendorDir}/../H.min.js");
    }
}
