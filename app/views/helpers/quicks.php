<?php
class QuicksHelper extends Helper {
	var $helpers = array('Text', 'Html', 'Sanitize', 'Date');
/**
 * function link
 * 
 * Creates a HTML link. Behaves exactly like Html::link with ability to use 
 * nicer links in form like "[Link] additional data"
 *
 * @param string $title The content to be wrapped by <a> tags.
 * @param mixed $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array $options Array of HTML attributes.
 * @param string $confirmMessage JavaScript confirmation message.
 * @return string An <a /> element.
 * @access public
 */
	function link($title, $url = null, $options = array(), $confirmMessage = false) {
		if (preg_match('/\[(.*)\]/', $title, $matches)) {
			$link_element = $this->Html->link($matches[1], $url, $options, $confirmMessage);
			return str_replace($matches[0], $link_element, $title);
		} else {
			return $this->Html->link($title, $url, $options, $confirmMessage);
		}
	}
/**
 * function exceprt
 * 
 * Extracts excerpt from text
 *
 * @param string $body
 * @param int $max_length
 * @param string $page_delimiter 
 * @return string
 * @access public
 * @static
 */
	function excerpt($body = null, $max_length=300, $page_delimiter='<!-- -- -->') {
		$ret = '';
		if (stripos($body, $page_delimiter)!==false) {
			$ret = substr($body, 0, stripos($body, $page_delimiter));
		} else {
			$ret = $this->Text->truncate($body, $max_length, array('ending' => '...', 'exact' => false, 'html' => true));
		}
		return $ret;
	}
/**
 * function profileCaption
 * 
 * Composes profile data into single identifying caption
 *
 * @param array $data
 * @return string
 * @access public
 * @static
 */
	function profileCaption($data) {
		App::import('Sanitize');
		
		$ret = '';
		$ret .= Sanitize::html($data['d_n']);
		
		if (!empty($data['mdn']) && $data['mdn'] != $data['ln']) {
			$ret .= ' ('.Sanitize::html($data['mdn']).')';
		}
		if (!empty($data['dob_y']) || !empty($data['dod_y'])) {
			$ret .= ' ';
			if (!$data['dob_y'] || (!$data['dod_y'] || !@$data['dob_c'])) $ret .= 'b.';
			if (@$data['dob_c']) $ret .= 'c.';
			$ret .= $data['dob_y'];
			
			if (!$data['l']) {
				$ret .= '-';
				if (!$data['dod_y'] || (!$data['dob_y'] || !@$data['dod_c'])) $ret .= 'd.';
				if (@$data['dod_c']) $ret .= 'c.';
				$ret .= $data['dod_y'];
			}
		} else if (!$data['l']) {
			$ret .= ' '.__('deceased', true);
		}
		return $ret;
	}
/**
 * function profileAge
 * 
 * Composes profile's age with available data
 *
 * @param array $data
 * @return string
 * @access public
 * @static
 */
	function profileAge($data) {
		$ret = $this->Date->age(
			$data['dob_y'] . '-' . 
			str_pad($data['dob_m'], 2 , '0', STR_PAD_LEFT) . '-' . 
			str_pad($data['dob_d'], 2 , '0', STR_PAD_LEFT)
		);
		return $ret;
	}
/**
 * function slug
 * 
 * Returns a string with all spaces converted to underscores (by default), accented
 * characters converted to non-accented characters, and non word characters removed.
 *
 * @param string $string
 * @param string $replacement
 * @return string
 * @access public
 * @static
 */
	function slug($string, $replacement = '_') {
		if (!class_exists('String')) {
			require LIBS . 'string.php';
		}
		$map = array(
			'/à|á|å|â/' => 'a',
			'/è|é|ê|ẽ|ë/' => 'e',
			'/ì|í|î/' => 'i',
			'/ò|ó|ô|ø/' => 'o',
			'/ù|ú|ů|û/' => 'u',
			'/ç|č|ć/' => 'c',
			'/š/' => 's',
			'/ž/' => 'z',
			'/đ/' => 'dz',
			
			'/Ć|Č/' => 'C',
			'/Š/' => 'S',
			'/Ž/' => 'Z',
			'/Đ/' => 'DZ',
			
			'/ñ/' => 'n',
			'/ä|æ/' => 'ae',
			'/ö/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/ß/' => 'ss',
			'/[^\w\s]/' => ' ',
			'/\\s+/' => $replacement,
			String::insert('/^[:replacement]+|[:replacement]+$/', array('replacement' => preg_quote($replacement, '/'))) => '',
		);
		$result = preg_replace(array_keys($map), array_values($map), $string);
		$result = preg_replace('/[^A-Za-z0-9-]/', $replacement, $result);
		return preg_replace('/'.preg_quote($replacement,  '/').'+/', $replacement, $result);
	}
/**
 * Creates a comma separated list where the last two items are joined with 'and', forming natural English
 *
 * @param array $list The list to be joined
 * @param string $and The word used to join the last and second last items together with. Defaults to 'and'
 * @param string $separator The separator used to join all othe other items together. Defaults to ', '
 * @return string The glued together string.
 * @access public
 */
	function toList($list, $and = 'and', $separator = ', ') {
		if (sizeof($list) > 1) {
			return implode($separator, array_slice($list, null, -1)) . ' ' . $and . ' ' . array_pop($list);
		} else {
			return reset($list);
		}
	}
}
?>
