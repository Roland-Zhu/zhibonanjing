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
	//多线程处理时一些变量
	private $_batch_config = array(
											'handle_array' => array(),
											'map_array' => array()
									);
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
		$this->_single_or_multi = is_array($url) ? FALSE : TRUE;
	}
	
	public function set_curl_option($option) {
		$this->_curl_option = $option;
	}
	
	public function init_handle() {
		if(is_array($this->_url)) {
			$this->_handle = curl_multi_init();
			foreach ($this->_url as $id=>$s_url) {
				$ch = curl_init($s_url);
				$this->_batch_config['handle_array'][] = $ch;
				curl_setopt_array($ch, $this->_curl_option);
	
				curl_multi_add_handle($this->_handle, $ch);
				$this->_batch_config['map_array'][(string)$ch] = $id;
// 				print_r($ch);
// 				echo "\n" . (string)$ch . "\n";
			}
			print_r($this->_batch_config);
		}
		else {
			$this->_handle = curl_init($this->_url);
			curl_setopt_array($this->_handle, $this->_curl_option);
		}
	}
	
	public function get_response() {
		if($this->_single_or_multi) {
			$response = curl_exec($this->_handle);
			curl_close($this->_handle);
			
			return $response;
		}
		else {
			$responses = array();
			do
			{
				while(($code = curl_multi_exec($this->_handle , $active)) == CURLM_CALL_MULTI_PERFORM);
				if($code != CURLM_OK)
				{
					break;
				}
		
				// a request was just completed -- find out which one
				while($done = curl_multi_info_read($this->_handle))
				{
		
					// get the info and content returned on the request
					$content = curl_multi_getcontent($done['handle']);
					$responses[$this->_batch_config['map_array'][(string) $done['handle']]] = $content;//$this->parseHead($content);
		
					// remove the curl handle that just completed
					curl_multi_remove_handle($this->_handle , $done['handle']);
					curl_close($done['handle']);
				}
		
				// Block for data in / output; error handling is done by
				// curl_multi_exec
				if($active > 0)
				{
					curl_multi_select($this->_handle , 0.5);
				}
			}
			while($active);
			
			return $responses;
		}
	}
}