# PhpOrient

[![Build Status](https://travis-ci.org/Ostico/PhpOrient.svg?branch=master)](https://travis-ci.org/Ostico/PhpOrient)

A PHP driver good enough for OrientDB that uses binary protocol.

> **status: RC-1**
> Please [report any bugs](https://github.com/Ostico/PhpOrient/issues) you find so that we can improve the library for everyone.

#### Requires
- PHP Version >= 5.4
- Orientdb version 1.7.4 or later.

##### PhpOrient works even on 32bit and 64bit platforms.

###### Warning, if you use a 32bit platform, you must use one of these libraries into YOUR application to avoid the loss of significant digits with Java long integers. Furthermore, these php modules should be loaded to achieve better driver performances on these systems.
- [BCMath Arbitrary Precision Mathematics](http://php.net/manual/en/refs.math.php) (Faster, recommended)
- [GNU Multiple Precision](http://php.net/manual/en/book.gmp.php)

## Installation

Main public repository of OrientDB-PHP is hosted at [https://github.com/Ostico/PhpOrient.git](https://github.com/Ostico/PhpOrient.git).

To install most recent version of library, just type
    
    git clone https://github.com/Ostico/PhpOrient.git

where you want its file to be located.

You can also want to get latest stable version, so check out Downloads section. Stables are marked with tags.

If you have not already installed globally, you have to download composer. Just run this command inside your PhpOrient directory.
```bash
php -r "readfile('https://getcomposer.org/installer');" | php
```
Now get the required libraries:
```bash
php composer.phar --no-dev install
```

## Usage
PhpOrient specify autoload information, Composer generates a vendor/autoload.php file. You can simply include this file and you will get autoloading for free and declare the use of PhpOrient Client with fully qualified name.

```php
require "vendor/autoload.php";
use PhpOrient\Client;
```


    Work in progress

## Contributions

- Fork the project.
- Make your changes.
- Add tests for it. This is important so I don’t break it in a future version unintentionally.
- Send me a pull request.
- ???
- PROFIT

# License

Apache License, Version 2.0, see [LICENSE.md](./LICENSE.md).
