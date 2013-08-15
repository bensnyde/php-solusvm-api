<?php

/**
 * Zenoss XMLRPC API PHP Library
 *
 * PHP library for easy interaction with your Zenoss Network Monitoring solution.
 * Note, the Zenoss API has much more functionality than what is implemented below.
 *
 * @category   PHP Libraries
 * @package    Zenoss API
 * @author     Benton Snyder <noumenaldesigns@gmail.com>
 * @copyright  2013 Noumenal Designs
 * @license    WTFPL
 * @link       http://www.noumenaldesigns.com
 */

class Zenoss
{
        private $tmp;
        private $protocol;
        private $address;
        private $port;
        private $username;
        private $password;
        private $cookie;

        /**
        * Public constructor
        *
        * @access       public
        * @param        string $address
        * @param        string $username
        * @param        string $password
        * @param        string $port
        * @param        string $tmp
        * @param        string $protocol
        * @return
        */
        function __construct($address,$username,$password,$port='8080',$tmp='/tmp/',$protocol='http')
        {
                parent::__construct();

                $this->address = $address;
                $this->username = $username;
                $this->password = $password;
                $this->port = $port;
                $this->tmp = $tmp;
                $this->protocol = $protocol;
                $this->cookie = $tmp."zenoss_cookie.txt";
        }

        /**
         * Queries Zenoss for requested data
         *
         * @access      private
         * @param       array $data
         * @param       string $uri
         * @return      json array
         */
        private function zQuery(array $data, $uri)
        {
                // inject common variables to data container
                $data['tid'] = 1;
                $data['type'] = "rpc";

                // fetch authorization cookie
                $ch = curl_init("{$this->protocol}://{$this->address}:{$this->port}/zport/acl_users/cookieAuthHelper/login");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
                curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
                curl_exec($ch);

                // execute xmlrpc action
                curl_setopt($ch, CURLOPT_URL, "{$this->protocol}://{$this->address}:{$this->port}{$uri}");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $result = curl_exec($ch);

                // cleanup
                curl_close($ch);
                return $result;
        }

        /**
         * Retrieves a listing of Zenoss Device Collectors
         *
         * @access      public
         * @param       string $deviceURI
         * @return      json array
         */
        public function getDeviceCollectors($deviceURI)
        {
                $json_data = array();
                $json_main = array();

                $json_main['action'] = "DeviceRouter";
                $json_main['method'] = "getCollectors";
                $json_main['data'] = $json_data;

                return $this->zQuery($json_main, $deviceURI.'/device_router');
        }

        /**
         * Retrieves listing of Zenoss events for specified device
         *
         * @access      public
         * @param       string $deviceURI
         * @param       int $start
         * @param       int $limit
         * @param       string $sort
         * @param       string $dir
         * @return      json array
         */
        public function getDeviceEvents($deviceURI, $start=0, $limit=100, $sort="severity", $dir="DESC")
        {
                $json_params = array();
                $json_data = array();
                $json_main = array();

                $json_params['severity'] = array();
                $json_params['eventState'] = array();

                $json_data['start'] = $start;
                $json_data['limit'] = $limit;
                $json_data['dir'] = $dir;
                $json_data['sort'] = $sort;
                $json_data['params'] = $json_params;

                $json_main['action'] = "EventsRouter";
                $json_main['method'] = "query";
                $json_main['data'] = array($json_data);

                return $this->zQuery($json_main, $deviceURI.'/evconsole_router');
        }

        /**
         * Retrieves listing of components for specified Zenoss Device
         *
         * @access      public
         * @param       string $deviceURI
         * @param       int $start
         * @param       int $limit
         * @return      json array
         */
        public function getDeviceComponents($deviceURI, $start=0, $limit=50)
        {
                $json_keys = array();
                $json_data = array();
                $json_main = array();

                $json_data['start'] = $start;
                $json_data['limit'] = $limit;
                $json_data['uid'] = $deviceURI;
                $json_data['meta_type'] = "IpInterface";
                $json_data['keys'] = $json_keys;

                $json_main['action'] = "DeviceRouter";
                $json_main['method'] = "getComponents";
                $json_main['data'] = array($json_data);

                return $this->zQuery($json_main, $deviceURI.'/device_router');
        }


        /**
         * Retrieves Zenoss device details
         *
         * @access      public
         * @param       string $deviceURI
         * @return      json array
         */
        public function getDeviceInfo($deviceURI)
        {
                $json_keys = array();
                $json_data = array();
                $json_main = array();

                $json_keys = array("uptime", "firstSeen", "lastChanged", "lastCollected", "locking", "memory", "name", "productionState", "priority",
                                "tagNumber", "serialNumber", "rackSlot", "collector","hwManufacturer","hwModel","osManufacturer","osModel","systems",
                                "groups","location","links","comments","snmpSysName","snmpLocation","snmpContact","snmpDescr","snmpCommunity","snmpVersion");

                $json_data['keys'] = array($json_keys);
                $json_data['uid'] = $deviceURI;

                $json_main['action'] = "DeviceRouter";
                $json_main['method'] = "getInfo";
                $json_main['data'] = array($json_data);

                return $this->zQuery($json_main, $deviceURI.'/getSubDevices');
        }

        /**
         * Retrieves listing of Zenoss Devices
         *
         * @access      public
         * @param       int $start
         * @param       int $limit
         * @param       string $sort
         * @param       string $dir
         * @return      json array
         */
        public function getDevices($start=0, $limit=100, $sort="name", $dir="ASC")
        {
                $json_params = array();
                $json_data = array();
                $json_main = array();

                $json_data['dir'] = $dir;
                $json_data['limit'] = $limit;
                $json_data['sort'] = $sort;
                $json_data['start'] = $start;
                $json_data['params'] = $json_params;

                $json_main['action'] = "DeviceRouter";
                $json_main['method'] = "getDevices";
                $json_main['data'] = $json_data;

                return $this->zQuery($json_main, '/zport/dmd/Devices/getSubDevices');
        }

        /**
         * Retrieves URL's for Zenoss Device Interface RRD graphs
         *
         * @access      public
         * @param       string $deviceURI
         * @param       string $interface
         * @param       int $drange
         * @return      json array
         */
        public function getDeviceInterfaceRRD($deviceURI, $interface, $drange=129600)
        {
                $json_data = array();
                $json_main = array();

                $json_data['uid'] = $interface;
                $json_data['drange'] = $drange;

                $json_main['action'] = "DeviceRouter";
                $json_main['method'] = "getGraphDefs";
                $json_main['data'] = array($json_data);

                return $this->zQuery($json_main, $deviceURI.'/device_router');
        }

        /**
         * Retrieves details on specified Zenoss Device Interface
         *
         * @access      public
         * @param       string $deviceURI
         * @param       string $interface
         * @return      json array
         */
        public function getDeviceInterfaceDetails($deviceURI, $interface)
        {
                $json_data = array();
                $json_main = array();

                $json_data['uid'] = $interface;

                $json_main['action'] = "DeviceRouter";
                $json_main['method'] = "getForm";
                $json_main['data'] = array($json_data);

                return $this->zQuery($json_main, $deviceURI.'/device_router');
        }
}
