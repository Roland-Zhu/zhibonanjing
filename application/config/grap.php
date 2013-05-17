<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$grap = array(
            'cars' => array(
                'longhoo' => array(
                    'news' => array(
                        'root_url' => 'http://auto.longhoo.net',
                        'grap_url' => array(
                            'http://auto.longhoo.net/a/jiaodiantuyoucewenzilian/',
                            'http://auto.longhoo.net/a/jiaotonglukuang/index.html',
                            'http://auto.longhoo.net/a/xingyexinwen/index.html'
                        ),
                        'list_reg' => '/<li><span>.*<\/span><a href=\"(.*)\" target=\"_blank\">(.*)<\/a><\/li>/Uis',
                        'detail_reg' => '/<h1>(.*)<\/h1>.*<div class="bianji">.*<p>(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}).*<div id="wz">(.*)<\/div>/Uis',
                        'editor_reg' => array('/\(责任.*\)/','/龙虎网讯/','/出品.*$/Uis'),
                    ),
                    'girls' => array(
                        'root_url' => 'http://auto.longhoo.net',
                        'grap_url' => array(
                            'http://auto.longhoo.net/a/xiangchemeinv/'
                        ),
                        'list_reg' => '/<li><span>.*<\/span><a href=\"(.*)\" target=\"_blank\">(.*)<\/a><\/li>/Uis',
                        'detail_reg' => '/<h1>(.*)<\/h1>.*<div class="bianji">.*<p>(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}).*<div id="wz">(.*)<\/div>/Uis',
                        'editor_reg' => array('/\(责任.*\)/','/龙虎网讯/','/出品.*$/Uis'),
                    )
                )
            ),
            'localnews' => array(
            ),
            'internalnews' => array(
            ),
            'internationalnews' => array(
            ),
            'house' => array(
            ),
            'education' => array(
            ),
            'technology' => array(
            ),
            'entertainment' => array(
            ),
            'sport' => array(
            ),
            'shopping' => array(
            ),
            'pictures' => array(
            )
        );
$config['grap'] = $grap;
/* End of file grap.php */
/* Location: ./application/config/grap.php */