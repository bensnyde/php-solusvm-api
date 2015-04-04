php-solusvm-api
===============

PHP Library for SolusVM's XMLRPC API

	https://documentation.solusvm.com/display/DOCS/API

 * @author     Benton Snyder
 * @website    http://www.bensnyde.me
 * @created    12/22/2012
 * @updated    4/2/2015

##### Usage

```
require('solus.php');
$solus = new Solus('https://solus.example.com:5656/api/admin', 'AFDi7342678A', 'SDFDJ83AF8AFA');
$clients = $solus->listClients();
```