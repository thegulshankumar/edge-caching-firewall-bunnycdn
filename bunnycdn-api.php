<?php
/**
 * BunnyCDN PHP library
 * Thanks to 'Code With Mark' 
 * for the original work (https://github.com/codewithmark/bunnycdn)
 * 
 *
 * This file is a modified version of original 
 * with replacement from cURL to HTTP API and 
 * several other changes.
 * LAST UPDATED 07:24 PM 08-March-2021 IST
 */
class bunnycdn_api {
    private $api_key_account;
    private $api_key_storage;
    protected $api_url = array("zone" => "https://bunnycdn.com/api", 'storage' => 'https://storage.bunnycdn.com', 'purge_url' => 'https://bunnycdn.com/api/purge?url=', 'purge_all_url' => 'https://bunnycdn.com/api/pullzone/');
    
    public function Account($api_key_account = '') {
        if (!$api_key_account) {
            return array('status' => 'error', 'code' => 'missing_api_key_account', 'msg' => 'missing api key account');
            die();
        }
        $this->api_key_account = $api_key_account;
        return $this;
    }

    public function GetZoneList() {
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone';
        $get_header = $this->create_header($key);
        $api_call = $this->run(array('call_method' => 'GET', 'api_url' => $api_url, 'header' => $get_header));
        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array)->Message,);
            return $result;
            die();
        }
        $zone_data = json_decode($api_call['data']);
        $a1 = array();
        foreach ($zone_data as $k1 => $v1) {
            $arr_hostnames = array();
            if ($v1->Hostnames) {
                foreach ($v1->Hostnames as $key => $v2) {
                    array_push($arr_hostnames, $v2->Value);
                }
            }
            $d = array("zone_id" => $v1->Id, "zone_name" => $v1->Name, "monthly_bandwidth_used" => $this->format_bytes($v1->MonthlyBandwidthUsed), "host_names" => $arr_hostnames);
            array_push($a1, $d);
        }
        return array('status' => 'success', 'zone_smry' => $a1, "zone_details" => $zone_data);
    }

    public function LoadFreeCertificate( $zone_id = '', $hostname = '' ) {

        if ( !$this->api_key_account ) {
            return array( 'status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing account api key' );
            die();
        }

        if ( !$zone_id ) {
            return array( 'status' => 'error', 'code' => 'zone_id', 'msg' => 'missing zone id' );
            die();
        }

        if ( !$hostname ) {
            return array( 'status' => 'error', 'code' => 'hostname', 'msg' => 'missing hostname' );
            die();
        }

        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/loadFreeCertificate?hostname=' . $hostname;
        $get_header = $this->create_header( $key );
        $api_call = $this->run( array( 'call_method' => 'GET', 'api_url' => $api_url, 'header' => $get_header ) );
        
        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }

        return array( 'status' => 'success', "msg" => $api_call );
        die();

    }

    public function GetZone($zone_id = '') {
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        if (!$zone_id) {
            return array('status' => 'error', 'code' => 'zone_id', 'msg' => 'missing zone id');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/' . $zone_id;
        $get_header = $this->create_header($key);
        $post_data_array = array('id' => $zone_id);
        $api_call = $this->run(array('call_method' => 'GET', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));
        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }
        $zone_data = json_decode($api_call['data']);
        $a1 = array();
        $arr_hostnames = array();
        if ($zone_data->Hostnames) {
            foreach ($zone_data->Hostnames as $key => $v1) {
                array_push($arr_hostnames, $v1->Value);
            }
        }
        $d = array("zone_id" => $zone_data->Id, "zone_name" => $zone_data->Name, "monthly_bandwidth_used" => $this->format_bytes($zone_data->MonthlyBandwidthUsed), "host_names" => $arr_hostnames);
        array_push($a1, $d);
        return array('status' => 'success', 'zone_smry' => $a1, "zone_details" => $zone_data);
        die();
    }

    public function CreateNewZone($zone_name = '', $zone_url = '') {
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        if (!$zone_name) {
            return array('status' => 'error', 'code' => 'zone_name', 'msg' => 'missing zone name');
            die();
        }
        if (!$zone_url) {
            return array('status' => 'error', 'code' => 'zone_url', 'msg' => 'missing zone url');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone';
        $get_header = $this->create_header($key);
        $post_data_array = array('Name' => $zone_name, 'OriginUrl' => $zone_url);
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));
        if ($api_call['http_code'] != 201) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }
        $zone_data = json_decode($api_call['data']);
        $cdnurl = '';
        if ($zone_data->Hostnames) {
            foreach ($zone_data->Hostnames as $key => $v1) {
                $cdnurl = $v1->Value;
            }
        }
        return array('status' => 'success', "zone_id" => $zone_data->Id, "zone_name" => $zone_data->Name, "origin_url" => $zone_data->OriginUrl, "cdn_url" => $cdnurl, "zone_details" => $zone_data);
        die();
    }

    public function DeleteZone($zone_id = '') {
        /*
        will delete a zone for the account
        */
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        if (!$zone_id) {
            return array('status' => 'error', 'code' => 'zone_id', 'msg' => 'missing zone id');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/' . $zone_id;
        $get_header = $this->create_header($key);
        $api_call = $this->run(array('call_method' => 'DELETE', 'api_url' => $api_url, 'header' => $get_header,));
        if ($api_call['http_code'] != 200 && $api_call['http_code'] != 302) {
            //error message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array),);
            return $result;
            die();
        }
        return array('status' => 'success', "msg" => $api_call,);
        //return $api_call;
        die();
    }

    public function UpdateZone($zone_id = '', $request_parameters = []) {
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        if (!$zone_id) {
            return array('status' => 'error', 'code' => 'zone_id', 'msg' => 'missing zone id');
            die();
        }
        if (!$request_parameters) {
            return array('status' => 'error', 'code' => 'request_parameters', 'msg' => 'request parameters are missing');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/' . $zone_id;
        $get_header = $this->create_header($key);
        $post_data_array = $request_parameters;
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));
        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }
        return array('status' => 'success', "http_code" => $api_call['http_code'], "msg" => "pullzone updated");
        die();
    }

    public function SetVaryCache($request_parameters = []) {
        
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }

        if (!$request_parameters) {
            return array('status' => 'error', 'code' => 'request_parameters', 'msg' => 'request parameters are missing');
            die();
        }

        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/setEnabledVaryParameters';
        $get_header = $this->create_header($key);
        $post_data_array = $request_parameters;
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));
        
        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }

        return array('status' => 'success', "http_code" => $api_call['http_code'], "msg" => "settings updated");
        die();
    }

    public function PurgeCache($url = false, $pullzone_id = false) {
        
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }

        if (!$url && $pullzone_id) {
            $api_url = $this->api_url['purge_all_url'] . $pullzone_id . "/purgeCache";
        } else {
            $api_url = $this->api_url['purge_url'] . $url;
        }

        $key = $this->api_key_account;
        $get_header = $this->create_header($key);
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header));

        if ($api_call['http_code'] != 200) {

            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => "purge everything skipped");
            return $api_call;
            die();

        }        


        return array('status' => 'success', "http_code" => $api_call['http_code'], "msg" => "purged everything");
        die();
    }

    public function SetOptimizerConfiguration($request_parameters = []) {
        
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }

        if (!$request_parameters) {
            return array('status' => 'error', 'code' => 'request_parameters', 'msg' => 'request parameters are missing');
            die();
        }

        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/setOptimizerConfiguration';
        $get_header = $this->create_header($key);
        $post_data_array = $request_parameters;
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));
        
        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }

        return array('status' => 'success', "http_code" => $api_call['http_code'], "msg" => "bunnycdn optimize configuration");
        die();
    }

    public function AddHostName($zone_id = '', $host_name_url = '') {
        /*
        
        will add a host name for the zone
        
        */
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        if (!$zone_id) {
            return array('status' => 'error', 'code' => 'zone_id', 'msg' => 'missing zone id');
            die();
        }
        if (!$host_name_url) {
            return array('status' => 'error', 'code' => 'host_name_url', 'msg' => 'missing host name url');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/addHostname';
        $get_header = $this->create_header($key);
        $post_data_array = array('PullZoneId' => $zone_id, 'Hostname' => $host_name_url);
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));
        if ($api_call['http_code'] != 200) {
            //error message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array),);
            return $result;
            die();
        }
        return array('status' => 'success', "msg" => $api_call,);
        die();
    }

    public function AddEdgeRule($zone_id = '', $request_parameters = []) {
        if (!$this->api_key_account) {
            return array('status' => 'error', 'code' => 'api_key_account', 'msg' => 'missing acount api key');
            die();
        }
        if (!$zone_id) {
            return array('status' => 'error', 'code' => 'zone_id', 'msg' => 'missing zone id');
            die();
        }
        if (!$request_parameters) {
            return array('status' => 'error', 'code' => 'request_parameters', 'msg' => 'request parameters are missing');
            die();
        }
        $key = $this->api_key_account;
        $api_url = $this->api_url['zone'] . '/pullzone/' . $zone_id . "/edgerules/addOrUpdate";
        $get_header = $this->create_header($key);
        $post_data_array = $request_parameters;
        $api_call = $this->run(array('call_method' => 'POST', 'api_url' => $api_url, 'header' => $get_header, 'post_data_array' => $post_data_array));

        if ($api_call['http_code'] != 200) {
            // Error Message
            $request_array = json_decode(json_encode($api_call['data']));
            $result = array("status" => 'error', "http_code" => $api_call['http_code'], "msg" => json_decode($request_array));
            return $result;
            die();
        }
        return array('status' => 'success', "http_code" => $api_call['http_code'], "msg" => "pullzone updated");
        die();
    }

    private function create_header($api_key) {
        $header = array('Content-Type: application/json', 'accesskey: ' . $api_key . '');
        return $header;
    }

    private function run($call_arr = array('call_method' => 'GET', 'api_url' => 'api_url', 'header' => array(), 'post_data_array' => array(),)) {
        $call_method = isset($call_arr['call_method']) ? $call_arr['call_method'] : 'GET';
        $api_url = isset($call_arr['api_url']) ? $call_arr['api_url'] : 'api_url';
        $header = isset($call_arr['header']) ? $call_arr['header'] : '';
        $post_data_array = isset($call_arr['post_data_array']) ? $call_arr['post_data_array'] : '';
        $post_data = json_encode($post_data_array);
        if($post_data == '""') $post_data = array();
        //define array
        $args = array();
        //define array
        $new_header = array();
        //loop the $header
        foreach ($header as $head) {
            //convert string $head to array
            $arr = explode(':', $head);
            //get string at first position in array
            $header_str = $arr[0];
            //remove string at first position in array
            unset($arr[0]);
            //convert array to string
            $header_val = implode(':', $arr);
            //build new header array
            $new_header[$header_str] = $header_val;
        }
        //assign variables to array $args
        $args['headers'] = $new_header;
        $args['method'] = $call_method;
        $args['body'] = $post_data;
        $args['redirection'] = '0';
        $args['timeout'] = '30';
        //request $api_url with options $args
        $response = wp_remote_request( $api_url, $args );
        //check wp error
        if ( is_wp_error( $response ) ) {
            //error message
            $error = $result->get_error_message();
        } else {
            //get http code from response
            $http_code = $response['response']['code'];
            //success json
            $result = wp_remote_retrieve_body($response);
        }
        
        // For Error Checking
        if ($result === false) {
            return array('status' => 'error', 'code' => 'http_api_log', 'result' => $error);
            die();
        }
        return array('http_code' => $http_code, 'data' => $result);
    }

    private function format_bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE) {
        $format = ($format === NULL) ? '%01.2f %s' : (string)$format;
        if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE) {
            $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
            $mod = 1024;
        } else {
            $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
            $mod = 1000;
        }
        if (($power = array_search((string)$force_unit, $units)) === FALSE) {
            $power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
        }
        return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
    }
}