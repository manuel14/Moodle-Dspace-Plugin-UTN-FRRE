<?php

class repository_dspace extends repository {

	public $rest_url = "http://localhost:8080/rest/";
	
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array(), $readonly=0) {
        parent::__construct($repositoryid, $context, $options, $readonly);
        $this->rest_url = $this->get_option('dspace_url');
    }

    // Fixed settings

    function supported_returntypes() {
        return FILE_REFERENCE|FILE_EXTERNAL;
    }

    function supported_filetypes() {
        return '*';
    }

    // Admin zone

    public static function get_type_option_names() {
        /*return array_merge(parent::get_type_option_names(), array('dspace_url'));*/
		$option_names = array_merge(parent::get_type_option_names(), array('dspace_url'));

        return $option_names;
    }

    public static function type_config_form($mform) {
        parent::type_config_form($mform);
	
        $dspaceUrl = get_config('repository_dspace', 'dspace_url');
		
		
        $mform->addElement('text', 'dspace_url', get_string('dspace_url', 'repository_dspace'), array('value' => $dspaceUrl,'size' => '60'));
        
    }

    // Listing

    function get_listing($path="/", $page="") {
		
		$pathArray = explode("/", $path);

        $list = array();
        $list['nologin'] = true;
        $list['dynload'] = true;
        $list['nosearch'] = true;
        $list['list'] = array();

        if((count($pathArray)== 1 || (count($pathArray) == 2 && $pathArray[1] == "") )) {
            $results = $this->call_api("GET", "communities");
            foreach($results as $result) {
                $list['list'][] = array(
					'dynload'=>true,
                    'title' => $result->name,
                    'children'=> array(),
					'path' => "/".$result->id);
            } 
			$list['path'] = array(array('name'=>'communities','path'=>'/'));

			
       } elseif(count($pathArray) == 2) {
	  
		$results = $this->call_api("GET", "communities/".$pathArray[1]."/?expand=collections");
			
            foreach($results->collections as $result) {
                $list['list'][] = array(
                    'title' => $result->name,
                    'children'=> array(),
					'path' => "/".$pathArray[1]."/".$result->id);
            } 
		$list['path'] = array(array('name'=>'collections','path'=>'/'), array('name'=>$pathArray[1], 'path'=>'/'.$pathArray[1]));
       } elseif(count($pathArray) == 3) {
	   
		$results = $this->call_api("GET", "collections/".$pathArray[2]."/?expand=items");
			
            foreach($results->items as $result) {
                $list['list'][] = array(
                    'title' => $result->name,
                    'children'=> array(),
					'path' => "/".$pathArray[1]."/".$result->id . "/");
            } 
		$list['path'] = array(array('name'=>'items','path'=>'/'), array('name'=>$pathArray[1], 'path'=>'/'.$pathArray[1]));
       } 
	   
	   elseif(count($pathArray) == 4) {
	   
	   
		$results = $this->call_api("GET", "items/".$pathArray[2]."/?expand=bitstreams");
			
            foreach($results->bitstreams as $result) {
                $list['list'][] = array(
                    'title' => $result->name,
					'url' => $this->rest_url."bitstreams/".$result->id."/retrieve",
					'source' => $this->rest_url."bitstreams/".$result->id."/retrieve");
            } 
		$list['path'] = array(array('name'=>'bitstreams','path'=>'/'), array('name'=>$pathArray[1], 'path'=>'/'.$pathArray[1]));
      }

        return $list; 
		
    
		
    }

    // REST

    function call_api($method, $endpoint, $data = false)
    {
	
        $curl = curl_init();

        $url = $this->rest_url .$endpoint;
		
        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        } 

        curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result); 
    }
	 public function set_option($options = array()) {
        if (!empty($options['dspace_url'])) {
            set_config('dspace_url', trim($options['dspace_url']), 'dspace');
        }
        unset($options['dspace_url']);
        $ret = parent::set_option($options);
        return $ret;
      }
	  public function get_option($config = '') {
        if (preg_match('/^dspace_/', $config)) {
            return trim(get_config('dspace', $config));
        }

        $options = parent::get_option($config);
        return $options;
    }
}

