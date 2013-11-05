<?php

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

//$kernel = new AppKernel('prod', false);
//$kernel = new AppKernel('dev', false);
$debug = false;
$kernel = new AppKernel('dev', $debug);
#$kernel = new AppKernel('prod', $debug);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$kernel->handle(Request::createFromGlobals())->send();
