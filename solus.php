<?php

/**
 * SolusVM XMLRPC API PHP Library
 *
 * PHP Library for easy integration of Solusvm <http://www.solusvm.com>.
 *
 * @category   PHP Libraries
 * @package    Solusvm
 * @author     Benton Snyder <introspectr3@gmail.com>
 * @copyright  2012 Noumenal Designs
 * @license    GPLv3
 * @website    <http://www.noumenaldesigns.com>
 */

class Solus
{
    private $url;
    private $id;
    private $key;

    /**
     * Public constructor
     *
     * @access         public
     * @param          str, str, str
     * @return
     */
    function __construct($url, $id, $key)
    {
        $this->url = $url;
        $this->id = $id;
        $this->key = $key;
    }

    /**
     * Executes xmlrpc api call with given parameters
     *
     * @access       private
     * @param        str, array
     * @return       str
     */
    private function execute($action, array $params)
    {
        // add $param data to POST variables
        foreach($params as $pKey => $pVal)
        {
            if(!is_int($pKey) && $pKey!="id" && $pKey!="key" && $pKey!="action")
                $postfields[$pKey] = $pVal;
        }

        // inject global POST vars
        $postfields["id"] = $this->id;
        $postfields["key"] = $this->key;
        $postfields["action"] = $action;

        // send request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $response = curl_exec($ch);

        // error handling
        if($response === false)
        	throw new Exception("Curl error: " . curl_error($ch));

        // cleanup
        curl_close($ch);
        return $response;
    }

    /**
     * Reboots specified vserver
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function reboot($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-reboot", array("vserverid"=>$serverID));
    }

    /**
     * Boots specified vserver
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function boot($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-boot", array("vserverid"=>$serverID));
    }

    /**
     * Shuts down specified vserver
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function shutdown($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-shutdown", array("vserverid"=>$serverID));
    }

    /**
     * Retrives list of available ISO images
     *
     * @access       public
     * @param        str
     * @return       str
     */
    public function listISO($type)
    {
        $validType = array("xen hvm", "kvm");
        if(in_array($type, $validType))
            return $this->execute("listiso", array("type"=>$type));
    }

    /**
     * Mounts ISO specified by its filename to vserver specified by ID
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function mountISO($serverID, $iso)
    {
        if(is_numeric($serverID) && in_array($iso, $this->listISO()))
            return $this->execute("vserver-mountiso", array("vserverid"=>$serverID, "iso"=>$iso));
    }

    /**
     * Unmounts the currently mounted ISO of a vserver specified by its ID
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function unmountISO($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-unmountiso", array("vserverid"=>$serverID));
    }

    /**
     * Updates the boot order of a vserver specified by its ID
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function changeBootOrder($serverID, $bootOrder)
    {
        $validOrder = array("cd", "dc", "c", "d");
        if(is_numeric($serverID) && in_array($bootOrder, $validOrder))
            return $this->execute("vserver-bootorder", array("vserverid"=>$serverID, "bootorder"=>$bootOrder));
    }

    /**
     * Retrieves VNC ip, port and password for vserver specified by its ID
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getVNC($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-vnc", array("vserverid"=>$serverID));
    }

    /**
     * Retrieves details of vserver specified by its ID
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getServerInfo($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-info", array("vserverid"=>$serverID));
    }

    /**
     * Retrieves server state information of vserver specified by its ID
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getServerState($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-infoall", array("vserverid"=>$serverID));
    }

    /**
     * Retrieves current status of vserver specified by ID
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getServerStatus($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-status", array("vserverid"=>$serverID));
    }

    /**
     * Authenticates client credentials
     *
     * @access       public
     * @param        str, str
     * @return       str
     */
    public function authenticateClient($username, $password)
    {
        if(ctype_alnum($username))
            return $this->execute("client-authenticate", array("username"=>$username, "password"=>$password));
    }

    /**
     * Updates hostname associated with vserver specified by its ID
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function changeHostname($serverID, $hostname)
    {
        if(is_numeric($serverID) && preg_match('/[\w-.]+/', $hostname))
            return $this->execute("vserver-hostname", array("vserverid"=>$serverID, "hostname"=>$hostname));
    }

    /**
     * Retrieves client list
     *
     * @access       public
     * @param
     * @return       str
     */
    public function listClients()
    {
        return $this->execute("client-list");
    }

    /**
     * Retrieves a list of virtual servers on specified node
     *
     * @access       public
     * @param         int
     * @return       str
     */
    public function listServers($nodeid)
    {
        if(is_numeric($nodeid))
            return $this->execute("node-virtualservers", array("nodeid"=>$nodeid));
    }

    /**
     * Determines if a vserver exists as specified by its ID
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function vserverExists($serverID)
    {
        if(is_numeric($serverID))
            return $this->execute("vserver-checkexists", array("vserverid"=>$serverID));
    }
}
