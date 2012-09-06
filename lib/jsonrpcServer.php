<?php
require_once(__DIR__.'/rpcServer.php');

/**
 * jsonrpcserver 实现抽象类rpcserver
 * @author maxmys
 * @date 2012-08-31
 * @copy epp_yerya
 * @mail Maxwell.mys@gmail.com
 *
 */
class jsonrpcServer extends rpcServer {

    /**
     * 构造方法
     * @param null
     * @access public
     * @return $this
     */
    public function __construct() {
       parent::__construct();
    }//end of __construct()

    /**
     * 覆盖抽象方法listen
     * @param null
     * @access public
     * @return $data
     */
    public  function listen() {
		// checks if a JSON-RCP request has been received
		if (
			$_SERVER['REQUEST_METHOD'] != 'POST' || 
			empty($_SERVER['CONTENT_TYPE']) ||
			$_SERVER['CONTENT_TYPE'] != 'application/json'
			) {
			// This is not a JSON-RPC request
		    return false;
		}
		// reads the input data
		$request = json_decode(file_get_contents('php://input'),true);
        // executes the task on local object
        try {
            if( ! isset($this->functions[$request['method']]) )
                $response = array(
                    'id'     =>  $request['id'],
                    'result' =>  NULL,
                    'error'  =>  'unset event'
                );
            else
                if ($result = @call_user_func_array( $this->functions[$request['method']],$request['params'])) {
                    $response = array (
                                        'id' => $request['id'],
                                        'result' => $result,
                                        'error' => NULL
                                        );
                } else {
                    $response = array (
                                        'id' => $request['id'],
                                        'result' => NULL,
                                        'error' => 'unknown method or incorrect parameters'
                                        );
                       }//end of else
        } catch (Exception $e) {
			$response = array (
								'id' => $request['id'],
								'result' => NULL,
								'error' => $e->getMessage()
								);
		}
		// output the response
		if (!empty($request['id'])) { // notifications don't want response
			header('content-type: text/javascript');
			echo json_encode($response);
		}
		
		// finish
		return true;
	}


}//end of class jsonrpcServer
