<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$compiler = app('blade.compiler');
$src = file_get_contents($root.'/resources/views/home.blade.php');
$out = $compiler->compileString($src);
file_put_contents($root.'/storage/framework/home_compiled_debug.php', $out);
echo $root.'/storage/framework/home_compiled_debug.php';
