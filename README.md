php-solusvm-api
===============

<h3>SolusVM XMLRPC API PHP Library</h3>

<p>Library class for easy interfacing with your <a href="http://www.solusvm.com">SolusVM Virtual Server Control Panel</a> allowing for simple integration of SolusVM functionality into your own applications.</p>

<p>@filename solus.php<br />@author Benton Snyder<br />@link <a href="http://noumenaldesigns.com" alt="Noumenal Designs">http://noumenaldesigns.com</a></p>

<p>Tested on SolusVM v1.12</p>

<h4>Usage</h4>

 require('solus.php');<br />
 $solus = new Solus('https://solus.example.com:5656/api/admin', 'AFDi7342678A', 'SDFDJ83AF8AFA');<br />
 $clients = $solus->listClients();<br />
