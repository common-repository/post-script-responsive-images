<?php
/*!
 * @wordpress-plugin
 * Plugin Name:		Post Script Responsive Images
 * Plugin URI:		https://www.p-stevenson.com
 * Description:		SRCSET responsive images on wordpress for content images.
 * Version:		2.1.0
 * Author:		Peter Stevenson
 * Author URI:		https://www.p-stevenson.com
 * License: 		GPL-2.0+
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 **/
namespace Plugins\WPPostScript;

defined('ABSPATH') or die("Cannot Access This File Directly");
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class ResponsiveImages {

	public function get_image_sizes() {
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $_size ) :
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) :
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			// elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) :
			// 	$sizes[ $_size ] = array(
			// 		'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
			// 		'height' => $_wp_additional_image_sizes[ $_size ]['height'],
			// 		'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			// 	);
			endif;
		endforeach;
		return $sizes;
	}

	public function imageResize( $data = array( ) ){
		
		if(!isset($data['width'])||!$data['width']){ $data['width']='1024'; }
		if(!isset($data['scr'])||!$data['scr']){ $data['scr']=''; }

		$sizes = array_reverse($this->get_image_sizes());

		$count=0;
		$returnURL = 'srcset="';
		if($data['src']):
			$returnURL .= $data['src'] . ' ' . $data['width'] . 'w' . ', ';
		endif;
		foreach($sizes as $key => $value):
			if( $value['width']<$data['width'] ):
				$image = wp_get_attachment_image_src($data['id'],$key);
				$returnURL .= $image[0] . ' ' . $value['width'] . 'w' . ', ';
			endif;
			$count++;
		endforeach;
		$returnURL = substr($returnURL, 0, -2);
		$returnURL .= '"';

		$returnURL .= ' '; // ADD SPACE

		$returnURL .= 'sizes="100vw"';

		return $returnURL;
	}

	public function createThumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if($html ===''){return;}

		$width = '';
		$src = '';
		preg_match_all('/(alt|title|src|class|height|width)=("[^"]*")/i',$html, $attrs);

		$html = '<img ';
		foreach( $attrs[0] as $key => $attr):
			$html .= $attr . ' ';
			if (strpos($attr,'width=') !== false):
				preg_match("/[0-9]+/",$attr,$width);
			endif;
			if (strpos($attr,'src=') !== false):
				preg_match("/\".+\"/",$attr,$src);
				$src[0] = str_replace('"', "", $src[0]);
			endif;
		endforeach;
		$resizeAttr['id'] = $post_thumbnail_id;
		if($src[0]):
			$resizeAttr['src'] = $src[0];
		endif;
		if($width[0]):
			$resizeAttr['width'] = $width[0];
		endif;
		$html .= $this->imageResize( $resizeAttr );
		$html .= ' /> ';

		return $html;
	}

	public function createContentImage($content){
		preg_match_all('/<img[^>]+>/i',$content, $result); 
		$img = array();
		foreach( $result[0] as $img_tag):
			preg_match_all('/(alt|title|src|class|height|width)=("[^"]*")/i',$img_tag, $img[$img_tag]);
		endforeach;

		$imgNew = array();
		foreach( $img as $key => $image):
			$id = '';
			$src = '';
			$width = '';
			$height = '';
			$count = $key;

			$imgNew[$count] = '<img ';
			foreach( $image[0] as $key => $attr):
				$imgNew[$count] .= $attr . ' ';
				
				if(strpos($attr,'class=') !== false):
					preg_match('/wp-image-[0-9]+/', $attr, $id);
				endif;
				if(strpos($attr,'width=') !== false):
					preg_match("/[0-9]+/",$attr,$width);
				endif;
				if(strpos($attr,'height=') !== false):
					preg_match("/[0-9]+/",$attr,$height);
				endif;
				if(strpos($attr,'src=') !== false):
					preg_match("/\".+\"/",$attr,$src);
				endif;
			endforeach;
			$id = preg_replace('/wp-image-/', '', $id[0]);
			$width = $width[0];
			$height = $height[0];
			$src = str_replace('"', "", $src[0]);

			if($id):
				if($id):
					$resizeAttr['id'] = $id;
				endif;
				if($src):
					$resizeAttr['src'] = $src;
				endif;
				if($width):
					$resizeAttr['width'] = $width;
				endif;
				if( !strpos(strtolower($src), '.gif') ):
					$imgNew[$count] .= $this->imageResize( $resizeAttr );
				endif;
			endif;
			$imgNew[$count] .= ' /> ';
		endforeach;

		$forCount = 0;
		foreach ($imgNew as $key => $image) :
			$content = str_replace($result[0][$forCount],  $image, $content);
			$forCount++;
		endforeach;
		
		return $content;
	}

}

$ResponsiveImages = new ResponsiveImages();
add_action( 'post_thumbnail_html', array($ResponsiveImages,'createThumbnail'),0,5 );
add_action('the_content', array($ResponsiveImages,'createContentImage'));
