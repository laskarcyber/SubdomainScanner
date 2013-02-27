# Subdomain Scanner
#### Intended to be run via CLI

### Usage
1: Include the class into your script:
```php
<?php
require 'class.subdomainscanner.php';
?>
```

2: Make a new instance of the class:
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

3: Set the list and start the scan:
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

4: ???

5: MAGIC
