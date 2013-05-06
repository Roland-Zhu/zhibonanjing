<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('display_errors',1);
error_reporting(E_ALL);
class Grap extends CI_Controller {

	public function index()
	{
		$root_url = 'http://auto.longhoo.net';
		$url = 'http://auto.longhoo.net/a/jinrijujiao/';
		$this->load->library('crawler');
		$content = $this->crawler->deal($url);
		$matches = $this->get_content('/<li><span>(.*)<\/span><a href=\"(.*)\" target=\"_blank\">(.*)<\/a><\/li>/Uis', $content);
		foreach ($matches as $v) {
			$detail_url = $root_url . $v['2'];
			$detail_url_array[] = $detail_url;
			/* $content = $this->crawler->deal($detail_url);
			$matches = $this->get_content('/<h1>(.*)<\/h1>.*<div class="bianji">.*<p>(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}).*<div id="wz">(.*)<\/div>/Uis', $content);
			echo 'url: ' . $detail_url . "\n";
			echo 'title: ' . $matches[0][1] . "\n";
			echo 'date: ' . $matches[0][2] . "\n";
// 			暂时使用外链图片
			$img = $this->get_content('/src="(.*)"/Uis', $matches[0][3]);
			$post = preg_replace('/\(责任.*\)/', '', $matches[0][3]);
			$post = trim(strip_tags($post, '<img>'));
			$origin_path = array();
			$full_path = array();
			if($img) {
				foreach ($img as $k=>$v) {
					$origin_path[] = $v[1];
					$full_path[] = $this->_expandlinks($v[1], $detail_url);
				}
				$post = str_replace($origin_path, $full_path, $post);
			}
			echo 'post: ' . $post . "\n=====================================\n"; */
		}
		print_r($detail_url_array);
	}
	
	public function get_content($regx, $subject) {
		preg_match_all($regx, $subject, $matches, PREG_SET_ORDER);
		return $matches;
	}
	
	/**
	 * from snoopy.class.php
	 * @param unknown $links
	 * @param unknown $URI
	 * @return mixed
	 */
	function _expandlinks($links,$URI)
	{
	
		preg_match("/^[^\?]+/",$URI,$match);
	
		$match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|","",$match[0]);
		$match = preg_replace("|/$|","",$match);
		$match_part = parse_url($match);
		$match_root =
		$match_part["scheme"]."://".$match_part["host"];
	
		$search = array( 	"|^http://".preg_quote('auto.longhoo.net')."|i",
				"|^(\/)|i",
				"|^(?!http://)(?!mailto:)|i",
				"|/\./|",
				"|/[^\/]+/\.\./|"
		);
	
		$replace = array(	"",
				$match_root."/",
				$match."/",
				"/",
				"/"
		);
	
		$expandedLinks = preg_replace($search,$replace,$links);
	
		return $expandedLinks;
	}
	
}