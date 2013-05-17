<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Grap extends CI_Controller
{

    private $grap_config;

    function __construct()
    {
        parent::__construct();
        $this->load->library('crawler');
        $this->load->database();
        $this->config->load('grap');
        $this->grap_config = $this->config->config['grap'];
    }

    public function execGrap()
    {
        foreach ($this->grap_config as $channel => $interface_array) {
            foreach ($interface_array as $interface_name => $type_array) {
                foreach ($type_array as $type => $detail) {
                    $root_url = $detail['root_url'];
                    $grap_url = $detail['grap_url'];
                    $list_reg = $detail['list_reg'];
                    $detail_reg = $detail['detail_reg'];
                    $editor_reg = $detail['editor_reg'];
                    foreach ($grap_url as $grap_url_v) {
                        $list_content = $this->crawler->deal($grap_url_v);
                        $matches = $this->get_content($list_reg, $list_content);
                        $detail_url_array = array();
                        foreach ($matches as $v) {
                            $detail_url = $this->_expandlinks($v[1], $root_url);
                            $detail_url_array[] = $detail_url;
                        }
                        $detail_content = $this->crawler->deal($detail_url_array);
                        foreach ($detail_content as $k1 => $v1) {
                            $matches = $this->get_content($detail_reg, $v1);
//                             echo 'channel: ' . $channel . "\n";
//                             echo 'type: ' . $type . "\n";
//                             echo 'interface: ' . $interface_name . "\n";
//                             echo 'url: ' . $detail_url_array[$k] . "\n";
//                             echo 'title: ' . $matches[0][1] . "\n";
//                             echo 'date: ' . $matches[0][2] . "\n";
                            $post = $matches[0][3];
                            //过滤编辑信息
                            $post = preg_replace($editor_reg, '', $post);
                            //过滤html标签，清除文章前后空白字符
                            $post = trim(strip_tags($post, '<img>'));
                            // 暂时使用外链图片
                            $img = $this->get_content('/src="(.*)"/Uis', $post);
                            $origin_path = array();
                            $full_path = array();
                            if ($img) {
                                foreach ($img as $k2 => $v2) {
                                    $origin_path[] = $v2[1];
                                    $full_path[] = $this->_expandlinks($v2[1], $detail_url_array[$k1]);
                                }
                                $image_url = json_encode($full_path);
                                $post = str_replace($origin_path, $full_path, $post);
                            }
//                             echo 'post: ' . $post . "\n=====================================\n";
                            $post_info = array(
                                'title' => $matches[0][1],
                                'post' => $post,
                                'time' => strtotime($matches[0][2]),
                                'channel' => $channel,
                                'type' => $type,
                                'interface' => $interface_name,
                                'url' => $detail_url_array[$k1],
                                'add_time' => time(),
                                'unique1' => md5($matches[0][1]),
                                'unique2' => md5($detail_url_array[$k1]),
                            );
                            //title和url不重复的情况下再插入
                            $query_duplication = "SELECT `id` FROM `news` WHERE `unique1`='{$post_info['unique1']}' OR `unique2`='{$post_info['unique2']}'";
                            $query = $this->db->query($query_duplication);
                            if(!($query->num_rows())) {
                                echo $matches[0][1] . "\n";
                                $this->db->insert('news', $post_info);
                                $post_id = $this->db->insert_id();
                                //插入文章表
                                if ($img && $post_id) {
                                    //插入images表
                                    $image_info = array(
                                        'post_id' => $post_id,
                                        'image_url' => $image_url,
                                    );
                                    $this->db->insert('images',$image_info);
                                }
                            }
                        }
                    }
                }
                exit();
            }
        }
    }

    public function index()
    {
        $this->execGrap();
        /*
         * $root_url = 'http://auto.longhoo.net'; $url = 'http://auto.longhoo.net/a/jinrijujiao/'; $content = $this->crawler->deal($url); $matches = $this->get_content('/<li><span>(.*)<\/span><a href=\"(.*)\" target=\"_blank\">(.*)<\/a><\/li>/Uis', $content); foreach ($matches as $v) { $detail_url = $root_url . $v['2']; $detail_url_array[] = $detail_url;
         */
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
		/* }
		$content2 = $this->crawler->deal($detail_url_array);
		foreach ($content2 as $vv) {
			$matches = $this->get_content('/<h1>(.*)<\/h1>.*<div class="bianji">.*<p>(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}).*<div id="wz">(.*)<\/div>/Uis', $vv);
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
			echo 'post: ' . $post . "\n=====================================\n";
		} */
	}

    public function get_content($regx, $subject)
    {
        preg_match_all($regx, $subject, $matches, PREG_SET_ORDER);
        return $matches;
    }

    /**
     * from snoopy.class.php
     *
     * @param unknown $links(link
     *            got)
     * @param unknown $URI(root
     *            url)
     * @return mixed
     */
    function _expandlinks($links, $URI)
    {
        preg_match("/^[^\?]+/", $URI, $match);
        
        $match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|", "", $match[0]);
        $match = preg_replace("|/$|", "", $match);
        $match_part = parse_url($match);
        $match_root = $match_part["scheme"] . "://" . $match_part["host"];
        
        $search = array(
            "|^http://" . preg_quote('auto.longhoo.net') . "|i",
            "|^(\/)|i",
            "|^(?!http://)(?!mailto:)|i",
            "|/\./|",
            "|/[^\/]+/\.\./|"
        );
        
        $replace = array(
            "",
            $match_root . "/",
            $match . "/",
            "/",
            "/"
        );
        
        $expandedLinks = preg_replace($search, $replace, $links);
        
        return $expandedLinks;
    }
}