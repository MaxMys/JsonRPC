<?php 

/**
 * rpcClient抽象类
 * @author maxmys
 * @date 2012-08-30
 * @copy epp_yerya
 * @mail Maxwell.mys@gmail.com
 */
abstract class rpcClient{

    /**
     * 连接一个rpc
     * @param $address String
     * @access public
     * @return null
     */
    public abstract function dial($address);

    /**
     * 调用一个rpc
     * @param $functionName String
     * @param $inData array()
     * @return $outData array();
     * @access public
     */
	public abstract function  call($functionName,$inData);

}//end of abstract class
