<?php

/**
 * PHP Library for SolusVM's XMLRPC API
 *
 *  https://documentation.solusvm.com/display/DOCS/API
 *
 * @author     Benton Snyder
 * @website    http://www.bensnyde.me
 * @created    12/22/2012
 * @updated    4/2/2015
 */

class Solus {
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
    function __construct($url, $id, $key) {
        $this->url = $url;
        $this->id = $id;
        $this->key = $key;
    }

    /**
     * Executes xmlrpc api call with given parameters
     *
     * @access       private
     * @param        array
     * @return       str
     */
    private function execute(array $params) {
        $params["id"] = $this->id;
        $params["key"] = $this->key;
        $params["rdtype"] = "json";

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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch);

        if($response === false)
            throw new Exception("Curl error: " . curl_error($ch));

        curl_close($ch);
        return $response;
    }

    /**
     * Get serial console details
     *
     *  https://documentation.solusvm.com/display/DOCS/Serial+Console
     */
    public function console($serverID, $access, $time) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-console", "vserverid"=>$serverID, "access"=>$access, "time"=>$time));
    }

    /**
     * Disable TUN/TAP
     *
     *  https://documentation.solusvm.com/pages/viewpage.action?pageId=558494
     */
    public function disableTUN($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-tun-disable", "vserverid"=>$serverID));
    }

    /**
     * Enable TUN/TAP
     *
     *  https://documentation.solusvm.com/pages/viewpage.action?pageId=558498
     */
    public function enableTUN($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-tun-enable", "vserverid"=>$serverID));
    }

    /**
     * PAE Enable/Disable
     *
     *  https://documentation.solusvm.com/pages/viewpage.action?pageId=558505
     */
    public function paestatus($serverID, $pae) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-pae", "vserverid"=>$serverID, "pae"=>$pae));
    }

    /**
     * Reboots specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Reboot+Virtual+Server
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function reboot($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-reboot", "vserverid"=>$serverID));
    }

    /**
     * Boots specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Boot+Virtual+Server
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function boot($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-boot", "vserverid"=>$serverID));
    }

    /**
     * Shuts down specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Shutdown+Virtual+Server
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function shutdown($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-shutdown", "vserverid"=>$serverID));
    }

    /**
     * Retrives list of available ISO images
     *
     *  https://documentation.solusvm.com/display/DOCS/List+ISO+Images
     *
     * @access       public
     * @param        str
     * @return       str
     */
    public function listISO($type) {
        if(!in_array($type, array("xen hvm", "kvm", "xen", "openvz")))
            throw new Exception("Invalid Type");

        return $this->execute(array("action"=>"listiso", "type"=>$type));
    }

    /**
     * Mounts ISO specified by its filename to vserver specified by ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Mount+ISO
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function mountISO($serverID, $iso) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-mountiso", "vserverid"=>$serverID, "iso"=>$iso));
    }

    /**
     * Unmounts the currently mounted ISO of a vserver specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Unmount+ISO
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function unmountISO($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-unmountiso", "vserverid"=>$serverID));
    }

    /**
     * Updates the boot order of a vserver specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Boot+Order
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function changeBootOrder($serverID, $bootOrder) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(!in_array($bootOrder, array("cd", "dc", "c", "d")))
            throw new Exception("Invalid bootorder");

        return $this->execute(array("action"=>"vserver-bootorder", "vserverid"=>$serverID, "bootorder"=>$bootOrder));
    }

    /**
     * Retrieves VNC ip, port and password for vserver specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/VNC+Info
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getVNC($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-vnc", "vserverid"=>$serverID));
    }

    /**
     * Retrieves details of vserver specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Virtual+Server+Information
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getServerInfo($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-info", "vserverid"=>$serverID));
    }

    /**
     * Retrieves server state information of vserver specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Virtual+Server+State
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getServerState($serverID,$nostatus,$nographs) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-infoall", "vserverid"=>$serverID, "nostatus"=>$nostatus, "nographs"=>$nographs));
    }

    /**
     * Retrieves current status of vserver specified by ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Virtual+Server+Status
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function getServerStatus($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-status", "vserverid"=>$serverID));
    }

    /**
     * Authenticates client credentials
     *
     *  https://documentation.solusvm.com/display/DOCS/Client+Authenticate
     *
     * @access       public
     * @param        str, str
     * @return       str
     */
    public function authenticateClient($username, $password) {
        if(!ctype_alnum($username))
            throw new Exception("Invalid Username");

        return $this->execute(array("action"=>"vserver-authenticate", "username"=>$username, "password"=>$password));
    }

    /**
     * Updates hostname associated with vserver specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Hostname
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function changeHostname($serverID, $hostname) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(!preg_match('/[\w-.]+/', $hostname))
            throw new Exception("Invalid Hostname");

        return $this->execute(array("action"=>"vserver-hostname", "vserverid"=>$serverID, "hostname"=>$hostname));
    }

    /**
     * Retrieves client list
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Clients
     *
     * @access       public
     * @param
     * @return       str
     */
    public function listClients() {
        return $this->execute(array("action"=>"client-list"));
    }

    /**
     * Retrieves a list of virtual servers on specified node
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Virtual+Servers
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function listServers($nodeid) {
        if(!is_numeric($nodeid))
            throw new Exception("Invalid NodeID");

        return $this->execute(array("action"=>"node-virtualservers", "nodeid"=>$nodeid));
    }

    /**
     * Determines if a vserver exists as specified by its ID
     *
     *  https://documentation.solusvm.com/display/DOCS/Check+Exists
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function vserverExists($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-checkexists", "vserverid"=>$serverID));
    }

    /**
     * Adds an IP address to specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Add+IP+Address
     *
     * @access       public
     * @param        int, str, bool
     * @return       str
     */
    public function addIP($serverID, $ipv4addr=0, $forceaddip=0) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        $args = array(
            "action" => "vserver-addip",
            "vserverid" => $serverID
        );

        if($ipv4addr) {
            if(filter_var($ipv4addr, FILTER_VALIDATE_IP) === false)
                throw new Exception("Invalid IPv4 Address");

            if(filter_var($forceaddip, FILTER_VALIDATE_BOOLEAN) === false)
                throw new Exception("forceaddip must be boolean");

            $args['ipv4addr'] = $ipv4addr;
            $args['forceaddip'] = $forceaddip;
        }

        return $this->execute($args);
    }

    /**
     * Deletes an IP address from the specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Delete+IP+Address
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function deleteIP($serverID, $ipaddr) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(filter_var($ipaddr, FILTER_VALIDATE_IP) === false)
            throw new Exception("Invalid IPv4 Address");

        return $this->execute(array("action"=>"vserver-delip", "vserverid"=>$serverID, "ipaddr"=>$ipaddr));
    }

    /**
     * Updates owner of specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Owner
     *
     * @access       public
     * @param        int, int
     * @return       str
     */
    public function changeOwner($serverID, $clientID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(!is_numeric($clientID))
            throw new Exception("Invalid ClientID");

        return $this->execute(array("action"=>"vserver-changeowner", "vserverid"=>$serverID, "clientid"=>$clientID));
    }

    /**
     * Updates vserver plan
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Plan
     *
     * @access       public
     * @param        int, str, bool
     * @return       str
     */
    public function changePlan($serverID, $plan, $changeHDD=false) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(filter_var($changeHDD, FILTER_VALIDATE_BOOLEAN) === false)
            throw new Exception("changeHDD must be boolean");

        return $this->execute(array("action"=>"vserver-change", "vserverid"=>$serverID, "plan"=>$plan, "changehdd"=>$changeHDD));
    }

    /**
     * Terminates specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Terminate+Virtual+Server
     *
     * @access       public
     * @param        int, bool
     * @return       str
     */
    public function terminate($serverID, $deleteclient=false) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(filter_var($deleteclient, FILTER_VALIDATE_BOOLEAN) === false)
            throw new Exception("deleteclient must be boolean");

        return $this->execute(array("action"=>"vserver-terminate", "vserverid"=>$serverID, "deleteclient"=>$deleteclient));
    }

    /**
     * Suspends specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Suspend+Virtual+Server
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function suspend($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-suspend", "vserverid"=>$serverID));
    }

    /**
     * Unsuspends specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Unsuspend+Virtual+Server
     *
     * @access       public
     * @param        int
     * @return       str
     */
    public function unsuspend($serverID) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-unsuspend", "vserverid"=>$serverID));
    }

    /**
     * Updates vserver's bandwidth limit
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Bandwidth+Limits
     *
     * @access       public
     * @param        int, int, int
     * @return       str
     */
    public function changeBandwidth($serverID, $limit, $overlimit) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(!is_numeric($limit))
            throw new Exception("Invalid Limit");

        if(!is_numeric($overlimit))
            throw new Exception("Invalid OverLimit");

        return $this->execute(array("action"=>"vserver-bandwidth", "vserverid"=>$serverID, "limit"=>$limit, "overlimit"=>$overlimit));
    }

    /**
     * Updates vserver's memory
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Memory
     *
     * @access       public
     * @param        int, int
     * @return       str
     */
    public function changeMemory($serverID, $memory) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(!is_numeric($memory))
            throw new Exception("Invalid Memory");

        return $this->execute(array("action"=>"vserver-change-memory", "vserverid"=>$serverID, "memory"=>$memory));
    }

    /**
     * Updates vserver's hdd size
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Hard+Disk+Size
     *
     * @access       public
     * @param        int, int
     * @return       str
     */
    public function changeDiskSize($serverID, $hdd) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        if(!is_numeric($hdd))
            throw new Exception("Invalid HDD");

        return $this->execute(array("action"=>"vserver-change-hdd", "vserverid"=>$serverID, "hdd"=>$hdd));
    }

    /**
     * Rebuilds specified vserver
     *
     *  https://documentation.solusvm.com/display/DOCS/Rebuild+Virtual+Server
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function rebuild($serverID, $template) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-rebuild", "vserverid"=>$serverID, "template"=>$template));
    }

    /**
     * Changes vserver's root password
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+Root+Password
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function changeRootPassword($serverID, $rootpassword) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-rootpassword", "vserverid"=>$serverID, "rootpassword"=>$rootpassword));
    }

    /**
     * Changes VNC password
     *
     *  https://documentation.solusvm.com/display/DOCS/Change+VNC+Password
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function changeVNCpassword($serverID, $vncpassword) {
        if(!is_numeric($serverID))
            throw new Exception("Invalid ServerID");

        return $this->execute(array("action"=>"vserver-vncpass", "vserverid"=>$serverID, "vncpassword"=>$vncpassword));
    }

    /**
     * Retrives list of available templates
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Templates
     *
     * @access       public
     * @param        str
     * @return       str
     */
    public function listTemplates($type, $listpipefriendly) {
        if(!in_array($type, array("xen hvm", "kvm", "xen", "openvz")))
            throw new Exception("Invalid Type");

        return $this->execute(array("action"=>"listtemplates", "type"=>$type, "listpipefriendly"=>$listpipefriendly));
    }

    /**
     * Retrives list of available plans
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Plans
     *
     * @access       public
     * @param        str
     * @return       str
     */
    public function listPlans($type) {
        if(!in_array($type, array("xen hvm", "kvm", "xen", "openvz")))
            throw new Exception("Invalid Type");

        return $this->execute(array("action"=>"listplans", "type"=>$type));
    }

    /**
     * Retrives list of nodes annotated by their ID
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Nodes+by+ID
     *
     * @access       public
     * @param        str
     * @return       str
     */
    public function listNodesByID($type) {
        if(!in_array($type, array("xen hvm", "kvm", "xen", "openvz")))
            throw new Exception("Invalid Type");

        return $this->execute(array("action"=>"node-idlist", "type"=>$type));
    }

    /**
     * Retrives list of nodes annotated by their name
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Nodes+by+Name
     *
     * @access       public
     * @param        str
     * @return       str
     */
    public function listNodesByName($type) {
        if(!in_array($type, array("xen hvm", "kvm", "xen", "openvz")))
            throw new Exception("Invalid Type");

        return $this->execute(array("action"=>"listnodes", "type"=>$type));
    }

    /**
     * Retrieves list of IP address associated with specified node
     *
     *  https://documentation.solusvm.com/display/DOCS/List+All+IP+Addresses+for+a+Node
     *
     * @access       public
     * @param        int, int
     * @return       str
     */
    public function getNodeIPs($nodeid) {
        if(!is_numeric($nodeid))
            throw new Exception("Invalid NodeID");

        return $this->execute(array("action"=>"node-iplist", "nodeid"=>$nodeid));
    }

    /**
     * Retrieves list of node groups
     *
     *  https://documentation.solusvm.com/display/DOCS/List+Node+Groups
     *
     * @access       public
     * @param        int, str
     * @return       str
     */
    public function listNodeGroups($type) {
        if(!in_array($type, array("xen hvm", "kvm")))
            throw new Exception("Invalid Type");

        return $this->execute(array("action"=>"listnodegroups", "type"=>$type));
    }
}
