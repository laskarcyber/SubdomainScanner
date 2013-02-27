<?php
require 'class.subdomainscanner.php';
use PUTS\SubdomainScanner\SubdomainScanner;

try {
  $scanner = new SubdomainScanner('http://google.com', 'list.txt');
	$scanner->startScan();
} catch(\Exception $e) {
	echo $e->getMessage() . PHP_EOL;
}
?>
