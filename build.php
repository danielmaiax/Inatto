<?php

//print phpinfo();
//print (getcwd());
//print_r($_SERVER);
//exit;
//print dirname(__FILE__);
//exit;

//
//$srcRoot = __DIR__."/src/commerce";

//
if (!isLocal()) die('not local');

//
$buildRoot = __DIR__ . "/build";

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

function isLocal()
{
    //
    $ip = $_SERVER['REMOTE_ADDR'];
    if (in_array($ip, ["127.0.0.1", "::1"])) return true; //ipv4 e ipv6
    if (in_array(Text::left($ip, 7), ["192.168"])) return true; //rede local
    if (in_array(Text::left($ip, 6), ["172.17"])) return true; //rede local
    if (in_array(Text::left($ip, 5), ["10.0."])) return true; //rede local

    //
    return false;
}