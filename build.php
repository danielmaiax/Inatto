<?php

//print phpinfo();
//print (getcwd());
//print_r($_SERVER);
//exit;
//print dirname(__FILE__);
//exit;

//
//$srcRoot = __DIR__."/src/commerce";
$buildRoot =  __DIR__."/build";

// remove phar anterior
if (file_exists($buildRoot . "/Inatto.phar")) unlink($buildRoot . "/Inatto.phar");

//
$phar = new Phar($buildRoot . "/Inatto.phar", FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, "Inatto.phar");
$phar->compressFiles(Phar::GZ);
$phar->setDefaultStub(null, null);
$phar->buildFromDirectory(dirname(__FILE__) . '/src/');
$phar->setStub(($phar->createDefaultStub('Kernel/Core/Loader.php')));

//
echo 'runned';
exit;

//
//$phar->setStub($phar->createDefaultStub('cli/index.php', 'www/index.php'));
//$phar->setStub($phar->createDefaultStub("index.php"));
//copy($srcRoot . "/config.ini", $buildRoot . "/config.ini");