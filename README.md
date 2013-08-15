php-solusvm-api
===============

/**<br />
 * SolusVM XMLRPC API PHP Library<br />
 *<br />
 * PHP Library for easy integration of Solusvm <http://www.solusvm.com>.<br />
 *<br />
 * @category   PHP Libraries<br />
 * @package    Solusvm<br />
 * @author     Benton Snyder <introspectr3@gmail.com><br />
 * @copyright  2012 Noumenal Designs<br />
 * @license    GPLv3<br />
 * @link       http://www.noumenaldesigns.com<br />
 */<br />

<h4>Usage</h4>

 require('solus.php');<br />
 $solus = new Solus('https://solus.example.com:5656/api/admin', 'AFDi7342678A', 'SDFDJ83AF8AFA');<br />
 $clients = $solus->listClients();<br />
