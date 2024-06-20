<?php
namespace JW3B\core\classes\Helpful;

class Helpful {
	public static function get_large_img($img){
		$base = basename($img);
		$large_img = str_replace($base, 'l'.substr($base, 1), $img);
		return $large_img;
	}

	public static function sq($w,$h,$p){
		return number_format((($w*$h)/144)*$p,2);
	}

	public static function clean_url($str){
		return preg_replace(['/[^a-zA-Z0-9+]/', '/--+/'], '-', trim(stripslashes($str)));
	}

	public static function clean_text($str, $nl2br=''){
		$ret = $nl2br == '' ? trim(htmlentities(stripslashes($str))) : nl2br(trim(htmlentities(stripslashes($str))));
		return $ret;
	}

	public static function form_element_name($str){
		$ret = strtolower(helpful::clean_url($str));
		return $ret;
	}

	public function setUpLinks($tags, $b4Link, $afterLink='/', $sep=','){
		$tt = explode($sep, $tags);
		$tags = '';
		foreach($tt as $k2){
			if($k2 != ''){
				$rp = $this->removePound($k2);
				$tags .= '<a href="'.$b4Link.$this->clean_url($rp).$afterLink.'">'.$rp.'</a>, ';
			}
		}
		$tags = substr($tags, 0, -2);
		return $tags;
	}

	public function removePound($tt){
		return trim(str_replace(['#', '-'], ['', ' '], stripslashes(htmlentities($tt))));
	}

	public function parse_my_url(){
		$url = substr($_SERVER['REQUEST_URI'], 1);
		$parts = explode('/', $url);
		foreach($parts as $ui){
			if($ui != ''){
				$uri[] = $ui;
			}
		}
		return $uri;
	}
	public static function breadcrumb($pgs, $right=false, $add_class=''){
		echo '<ol class="breadcrumb'.$add_class.'">';
		if(is_array($pgs)){
			foreach($pgs as $k => $v){
				if(is_array($v)){
					echo '<li class="active">'.$v[0].'</li>';
				} else {
					echo '<li><a href="'.$k.'">'.$v.'</a></li>';
				}
			}
		}
		if(is_array($right)){
			foreach($right as $k => $v){
				if(is_array($v)){
					echo '<li class="pull-right active">'.$v[0].'</li>';
				} else {
					echo '<li class="pull-right"><a href="'.$k.'">'.$v.'</a></li>';
				}
			}
		}
		echo '</ol>';
	}

	public static function mail2($to, $subject, $message, $header=array()){
		global $Sets;
		$headers[] = 'Content-type: text/html; charset=UTF-8'; //iso-8859-1';
		//$headers[] = 'From: "'.$Sets['site_name'].'" <'.$Sets['site_email'].'>';
		$headers[] = 'From: '.$Sets['site_email'];
		$headers[] = 'Reply-To: '.$Sets['site_email'];
		$headers[] = 'X-Mailer: PHP/' . phpversion();
		$head = implode("\r\n", array_merge($headers, $header));
		if(@mail($to, $subject, $message, $head, "-f ".$Sets['site_email'])){
			return true;
		} else {
			return false;
		}
	}

	// ['tags' => 10, 'like' => 5]
	public function tagCloud($tags, $b4Link, $afterLink='/'){
		if(is_array($tags)){
			$t = 0;
			arsort($tags); // hightest value first, DESC
			foreach($tags as $k => $v){
				$Rtags[$k] = $v;
				$t++;
				if($t == 100) break;
			}
			$c = count($Rtags);
			ksort($Rtags); // abc order
			$max_size = 250; // max font size in %
			$min_size = 80; // min font size in %
			$max_qty = max(array_values($Rtags));
			$min_qty = min(array_values($Rtags));
			$spread = $max_qty - $min_qty;
			if (0 == $spread) { // we don't want to divide by zero
				$spread = 1;
			}
			$step = ($max_size - $min_size)/($spread);
			$ret = '';
			foreach($Rtags as $k => $v){
				$size = $min_size + (($v - $min_qty) * $step);
				$rp = $this->removePound($k);
				$ret .= '<a href="'.$b4Link.helpful::clean_url($rp).$afterLink.'" class="load-page" title="'.$rp.' tagged '.$v.' times" rel="tag" style="font-size: '.$size.'%">'.$rp.'</a> ';
			}
		} else { $ret = 'No Tags Found'; }
		return $ret;
	}

		// fix array for the following function for image uploads
		/*
		$file_ary = reArrayFiles($_FILES['file']);

			foreach ($file_ary as $file) {
					print 'File Name: ' . $file['name'];
					print 'File Type: ' . $file['type'];
					print 'File Size: ' . $file['size'];
			}
			*/
	public function reArrayFiles(&$file_post) {
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
		for($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
		return $file_ary;
	}

	public function resize_uploaded_image($file, $destination, $sizes=array('s' => '300', 'l' => '800')){
		if($file['error'] == 0){
			switch($file['type']){
				case "image/jpeg":
				case "image/jpg":
				case "image/pjpeg":
					$ext = '.jpg';
					break;
				case "image/png":
				case "image/x-png":
					$ext = '.png';
					break;
				case "image/gif":
					$ext = '.gif';
					break;
				default:
					return 'Incorrect file type uploaded. Only png, gif, and jpg files are supported.';
			}
			$filename = preg_replace('/[^a-zA-Z0-9\._]/i', '', $file['name']);
			$year = date('Y', time());
			$month = date('n', time());
			if(!is_dir($destination.$year.'/')){
				@mkdir($destination.$year.'/', 0777);
			}
			if(!is_dir($destination.$year.'/'.$month.'/')){
				@mkdir($destination.$year.'/'.$month.'/', 0777);
			}
			$dir = $destination.$year.'/'.$month.'/';
			if(is_file($dir.$filename)){
				$n = 0;
				for(;;){
					if(!is_file($dir.$n.$filename)){
						$filename = $n.$filename;
						break;
					}
					$n++;
				}
			}
			move_uploaded_file($file['tmp_name'], $dir.$filename);
			list($width, $height, $typeM, $attr) = getimagesize($dir.$filename);
			$total_sizes = count($sizes);
			$created = array();
			foreach($sizes as $pre => $size){
				$new_file = $total_sizes > 1 ? $dir.$pre.'_'.$filename : $dir.$filename;
				if($width > $size || $height > $size){
					system("convert ".$dir.$filename." -resize ".$size."x".$size." -quality 100 ".$new_file);
				} else if($new_file != $dir.$filename){
					copy($dir.$filename, $new_file);
				}
				$created[$pre] = $new_file;
			}
			$not_found = false;
			foreach($created as $file2){
				if($file2 != '' && !is_file($file2)){
					$not_found = true;
				}
			}
			if($not_found == true){
				return 'error - '.$file2.' is not found';
			} else {
				return $created;
			}
		} else {
			return 'Looks like there was an error uploading this file.';
		}
	}
	// I didnt feel like writing my own AGAIN...
	// http://css-tricks.com/snippets/php/pagination-function/
	/*<ul class="pagination">
	<li class="disabled"><a href="#">&laquo;</a></li>
	<li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
	...
	</ul>
	*/
	function pagination($item_count, $limit, $cur_page, $link){
		$page_count = ceil($item_count/$limit);
		$current_range = array(($cur_page-2 < 1 ? 1 : $cur_page-2), ($cur_page+2 > $page_count ? $page_count : $cur_page+2));
		// First and Last pages
		$first_page = $cur_page > 3 ? '<li><a href="'.sprintf($link, '1').'">1</a></li>'.($cur_page < 5 ? '' : '<li class="disabled"><a href="#">...</a></li>') : null;
		$last_page = $cur_page < $page_count-2 ? ($cur_page > $page_count-4 ? '' : '<li class="disabled"><a href="#">...</a></li>').'<li><a href="'.sprintf($link, $page_count).'">'.$page_count.'</a></li>' : null;
		// Previous and next page
		$previous_page = $cur_page > 1 ? '<li><a href="'.sprintf($link, ($cur_page-1)).'">&laquo;</a></li>' : null;
		$next_page = $cur_page < $page_count ? '<li><a href="'.sprintf($link, ($cur_page+1)).'">&raquo;</a></li>' : null;
		// Display pages that are in range
		for($x=$current_range[0];$x <= $current_range[1]; ++$x){
			$active = $x == $cur_page ? ' class="active"' : '';
			$pages[] = '<li'.$active.'><a href="'.sprintf($link, $x).'">'.$x.'</a></li>';
		}
		if($page_count > 1)
			return '<ul class="pagination">'.$previous_page.$first_page.implode('', $pages).$last_page.$next_page.'</ul>';
	}
}