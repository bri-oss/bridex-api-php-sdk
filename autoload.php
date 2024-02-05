<?php

spl_autoload_register(function ($class) {
  // Map the namespace to the corresponding directory
  $namespace = 'BRI\\';
  $baseDir = __DIR__ . '/../briapi-sdk/src/';

  $class = str_replace($namespace, '', $class);
  $classPath = str_replace('\\', '/', $class);

  $file = $baseDir . $classPath . '.php';

  if (file_exists($file)) {
    require $file;
  }
});
