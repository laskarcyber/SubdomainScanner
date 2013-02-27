# Subdomain Scanner
> This application is intended to be run via CLI using the command "php run.php"
>
> list.txt may need to be chmodded to 755 (Non-Winblows users)

#### Usage
__1:__ Include the class into your script:
```php
<?php
require 'class.subdomainscanner.php';
?>
```

__2:__ Make a new instance of the class:
```php
<?php
require 'class.subdomainscanner.php';
try {
  $scanner = new SubdomainScanner('http://google.ca'); // This will start a new scanner on the domain "google.ca"
} catch(Exception $e) { // catch any exceptions.
  echo $e->getMessage() . PHP_EOL;
}
?>
```

__3:__ Set the list and start the scan:
```php
<?php
require 'class.subdomainscanner.php';
try {
  $scanner = new SubdomainScanner('http://google.ca'); // This will start a new scanner on the domain "google.ca"
  $scanner->setList('list.txt'); // Set the list to list.txt
  $scanner->startScan(); // start the scan
} catch(Exception $e) { // catch any exceptions.
  echo $e->getMessage() . PHP_EOL;
}
?>
```

__4:__ ???

__5:__ ***MAGIC***

#### Permutation vs list
Like others, this script has the ability to make a permutative pattern (a, b .. aa, ab, ac etc...)

While using a word list may be more efficient (less requests, less bandwidth, less everything, faster), sometimes a permutative pattern may be ideal if you don't have any idea what you're looking for.

Beware, permutation is **SLOW**.
