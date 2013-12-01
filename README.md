php-solusvm-api
===============

SolusVM XMLRPC API PHP Library

PHP Library for easy integration of Solusvm <http://www.solusvm.com>.

 * @category   PHP Libraries
 * @package    Solusvm
 * @author     Benton Snyder <introspectr3@gmail.com>
 * @copyright  2012 Noumenal Designs
 * @license    GPLv3
 * @website    <http://www.noumenaldesigns.com>

<h4>Usage</h4>

 require('solus.php');<br />
 $solus = new Solus('https://solus.example.com:5656/api/admin', 'AFDi7342678A', 'SDFDJ83AF8AFA');<br />
 $clients = $solus->listClients();<br />
