<?php 
require_once(__DIR__.'/rpcClient.php'); 
/**
 * jsonrpcclient实现
 * @author maxmys
 * @date 2012-08-31
 * @copy epp_yerya
 * @mail Maxwell.mys@gmail.com
 */
class jsonrpcClient extends rpcClient {

	/**
	 * Debug state
	 *
	 * @var boolean
	 */
	private $debug;
	
	/**
	 * The server URL
	 *
	 * @var string
	 */
	private $url;
	/**
	 * The request id
	 *
	 * @var integer
	 */
	private $id;
	/**
	 * If true, notifications are performed instead of requests
	 *
	 * @var boolean
	 */
	private $notification = false;

    /**
     * 构建函数
     * @param null
     * @access public
     * @return null
     */
    public function __construct() {}
        
    /**
	 * Sets the notification state of the object. In this state, notifications are performed, instead of requests.
	 *
	 * @param boolean $notification
	 */
	public function setRPCNotification($notification) {
		empty($notification) ?
							$this->notification = false
							:
							$this->notification = true;
	}
	
    /**
	 * Performs a jsonRCP request and gets the results as an array
	 *
	 * @param string $method
	 * @param array $params
	 * @return array
	 */
	public function __call($method,$params) {
		// check
		if (!is_scalar($method)) {
			throw new Exception('Method name has no scalar value');
		}
		
		// check
		if (is_array($params)) {
			// no keys
			$params = array_values($params);
		} else {
			throw new Exception('Params must be given as array');
		}
		
		// sets notification or request task
		if ($this->notification) {
			$currentId = NULL;
		} else {
			$currentId = $this->id;
		}
		
		// prepares the request
		$request = array(
						'method' => $method,
						'params' => $params,
						'id' => $currentId
						);
		$request = json_encode($request);
		$debug ='***** Request *****'."\n".$request."\n".'***** End Of request *****'."\n\n";
		// performs the HTTP POST
		$opts = array ('http' => array (
							'method'  => 'POST',
							'header'  => 'Content-type: application/json',
							'content' => $request
							));
		$context  = stream_context_create($opts);
		if ($fp = fopen($this->url, 'r', false, $context)) {
			$response = '';
			while($row = fgets($fp)) {
				$response.= trim($row)."\n";
			}
			$debug && $debug.='***** Server response *****'."\n".$response.'***** End of server response *****'."\n";
			$response = json_decode($response,true);
		} else {
			throw new Exception('Unable to connect to '.$this->url);
		}
		// debug output
        if ($this->debug) {
            echo nl2br($debug);
        }

		// final checks and return
		if (!$this->notification) {
			// check
			if ($response['id'] != $currentId) {
				throw new Exception('Incorrect response id (request id: '.$currentId.', response id: '.$response['id'].')');
			}
			if (!is_null($response['error'])) {
				throw new Exception('Request error: '.$response['error']);
			}
			
			return $response['result'];
			
		} else {
			return true;
		}
	}
    /**
     * 连接一个rpc
     * @param $address String
     * @access public
     * @return null
     */
    public function dial($address,$debug='',$proxy=''){
		// server URL
        $this->url = $address;
		// proxy
		empty($proxy) ? $this->proxy = '' : $this->proxy = $proxy;
		// debug state
		empty($debug) ? $this->debug = false : $this->debug = true;
		// message id
		$this->id = 1;
    }//end of funciton dial

    /**
     * 调用一个rpc
     * @param $functionName String
     * @param $inData array()
     * @return $outData array();
     * @access public
     */
    public function  call($evenName,$data){
        if( is_array($data) )
           $data = array_values($data);
        return call_user_func_array(
                array($this,$evenName),
                $data
                ); 
    }//end of function call();

}//end of class
