<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Crawler {
	private $_url;
	private $_handle;
	private $_handle_array; //仅在处理批量curl句柄时使用
	private $_curl_option;
	private $_single_or_multi = TRUE; //TRUE表示是单个curl句柄，FALSE表示一组批处理curl句柄(多线程操作)
	
	function __construct() {
		$this->_curl_option = array(
				CURLOPT_TIMEOUT => 60,
				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_HEADER => TRUE,
				CURLOPT_NOSIGNAL => TRUE,
		);
	}
	
	public function deal($url) {
		$this->set_url($url);
		$this->init_handle();
		
		return $this->get_response();
	}

	public function set_url($url) {
		$this->_url = $url;
	}
	
	public function set_curl_option($option) {
		$this->_curl_option = $option;
	}
	
	public function init_handle() {
		if(is_array($this->_url)) {
			$this->_handle = curl_multi_init();
			foreach ($this->_url as $id=>$s_url) {
				$ch = curl_init($s_url);
				$this->_handle_array[] = $ch;
				curl_setopt_array($ch, $this->_curl_option);
	
				curl_multi_add_handle($this->_handle, $ch);
			}
		}
		else {
			$this->_handle = curl_init($this->_url);
			curl_setopt_array($this->_handle, $this->_curl_option);
		}
	}
	
	public function get_response() {
		if($this->_single_or_multi) {
			return curl_exec($this->_handle);
		}
	}
	
	function __destruct() {
		$this->_single_or_multi
							? curl_close($this->_handle)
							: curl_multi_close($this->_handle);	
	}
}