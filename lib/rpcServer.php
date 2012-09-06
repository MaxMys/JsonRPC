<?php

/**
 * rpcServer抽象基类
 * @author maxmys
 * @date 2012-8-31
 * @copy epp_yerya
 * @mail Maxwell.mys@gmail.com
 */
abstract class rpcServer{

    protected $functions = null;

    public function __construct() {
        $this->functions = array();
    }//end of function 
    /**
     * 注册方法的函数 一次只能添加一个函数.可以添加对象和方法
     * @param $functions function 
     * @access public
     * @return null
     */
    public function register($function,$name) {
        if( ! is_callable($function) ) 
            throw new \Exception($name.':is uncallable!');
        $this->functions[$name] = $function;
    }//end of public function 

	/**
     * 监听rpc调用
     * @param null
     * @access public
     * @return $result array()
     */
	public abstract function  listen();
}
