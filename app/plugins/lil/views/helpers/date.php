<?php
	class DateHelper extends Helper {
		var $helpers = array('Time', 'Form');
		
		function __call($method, $param) {
			if (method_exists($this->Time, $method)) {
				return (call_user_func_array(array($this->Time, $method), $param));
			}
			return false;
		}
		
		function age($dateString=null, $userOffset = null) {
			if (strlen($dateString)==4) $dateString .= '-12-31';
			$date = $dateString ? $this->fromString($dateString, $userOffset) : time();
	
			$ret = date("Y")-date("Y", $date);
			if (date("m", $date) > date("m")) $ret--;
			else if (date("m", $date)==date("m") && date("d", $date) > date("d")) $ret--;
			return $ret;
		}
		
		function isPast($dateString=null, $userOffset=null) {
			$date = $dateString ? $this->fromString($dateString, $userOffset) : $this->fromString(time(), $userOffset);
			$now = $this->fromString(time(), $userOffset);
			return ($date < $now);
		}
		
		function nice($dateString=null, $userOffset=null, $showZeroTime=true) {
			$date = $dateString ? $this->fromString($dateString, $userOffset) : time();
			
			$time = strftime('%H:%M', $date);
			if ($showZeroTime && $time=='00:00') {
				$time = '';
			} else {
				$time = ', '.$time;
			}
			
			$ret = strftime(Configure::read('niceDateFormat').$time, $date);
			return $this->output($ret);
		}
		
		function niceShort($dateString = null, $userOffset = null, $showZeroTime=true) {
			$date = $dateString ? $this->fromString($dateString, $userOffset) : time();
			
			$y = $this->isThisYear($date) ? '' : ' %Y';
			
			$time = strftime('%H:%M', $date);
			if (!$showZeroTime && $time=='00:00') {
				$time = '';
			} else {
				$time = ', '.$time;
			}
			
			if ($this->isToday($date)) {
				$ret = __d('lil', 'Today', true).$time;
			} elseif ($this->wasYesterday($date)) {
				$ret = __d('lil', 'Yesterday', true).$time;
			} else {
				if (!$date_format = Configure::read('niceShortDateFormat')) {
					$date_format = '%b %d.';
				}
				$ret = strftime($date_format.$y.$time, $date);
			}
			
			return $this->output($ret);
		}
		
		function timeAgoInWords($dateTime, $options = array()) {
			$userOffset = null;
			if (is_array($options) && isset($options['userOffset'])) {
				$userOffset = $options['userOffset'];
			}
			
			if (is_array($options) && isset($options['start'])) {
				$now = strtotime($options['start']);
			} else {
				$now = time();
			}
			if (!is_null($userOffset)) {
				$now = 	$this->convert(time(), $userOffset);
			}
			$inSeconds = $this->fromString($dateTime, $userOffset);
			$backwards = ($inSeconds > $now);
	
			$format = '%d/%m/%Y';
			$end = '+1 month';
			$time = true;
	
			if (is_array($options)) {
				if (isset($options['format'])) {
					$format = $options['format'];
					unset($options['format']);
				}
				if (isset($options['end'])) {
					$end = $options['end'];
					unset($options['end']);
				}
				if (isset($options['time'])) {
					$time = $options['time'];
					unset($options['time']);
				}
			} else {
				$format = $options;
			}
	
			if ($backwards) {
				$futureTime = $inSeconds;
				$pastTime = $now;
			} else {
				$futureTime = $now;
				$pastTime = $inSeconds;
			}
			$diff = $futureTime - $pastTime;
			
			// If more than a week, then take into account the length of months
			if ($diff >= 604800) {
				$current = array();
				$date = array();
	
				list($future['H'], $future['i'], $future['s'], $future['d'], $future['m'], $future['Y']) = explode('/', date('H/i/s/d/m/Y', $futureTime));
	
				list($past['H'], $past['i'], $past['s'], $past['d'], $past['m'], $past['Y']) = explode('/', date('H/i/s/d/m/Y', $pastTime));
				$years = $months = $weeks = $days = $hours = $minutes = $seconds = 0;
	
				if ($future['Y'] == $past['Y'] && $future['m'] == $past['m']) {
					$months = 0;
					$years = 0;
				} else {
					if ($future['Y'] == $past['Y']) {
						$months = $future['m'] - $past['m'];
					} else {
						$years = $future['Y'] - $past['Y'];
						$months = $future['m'] + ((12 * $years) - $past['m']);
	
						if ($months >= 12) {
							$years = floor($months / 12);
							$months = $months - ($years * 12);
						}
	
						if ($future['m'] < $past['m'] && $future['Y'] - $past['Y'] == 1) {
							$years --;
						}
					}
				}
	
				if ($future['d'] >= $past['d']) {
					$days = $future['d'] - $past['d'];
				} else {
					$daysInPastMonth = date('t', $pastTime);
					$daysInFutureMonth = date('t', mktime(0, 0, 0, $future['m'] - 1, 1, $future['Y']));
	
					if (!$backwards) {
						$days = ($daysInPastMonth - $past['d']) + $future['d'];
					} else {
						$days = ($daysInFutureMonth - $past['d']) + $future['d'];
					}
	
					if ($future['m'] != $past['m']) {
						$months --;
					}
				}
	
				if ($months == 0 && $years >= 1 && $diff < ($years * 31536000)) {
					$months = 11;
					$years --;
				}
	
				if ($months >= 12) {
					$years = $years + 1;
					$months = $months - 12;
				}
	
				if ($days >= 7) {
					$weeks = floor($days / 7);
					$days = $days - ($weeks * 7);
				}
			} else {
				$years = $months = $weeks = 0;
				$days = floor($diff / 86400);
				
				$diff = $diff - ($days * 86400);
	
				$hours = floor($diff / 3600);
				$diff = $diff - ($hours * 3600);
	
				$minutes = floor($diff / 60);
				$diff = $diff - ($minutes * 60);
				$seconds = $diff;
			}
			$relativeDate = '';
			$diff = $futureTime - $pastTime;
	
			if ($diff > abs($now - $this->fromString($end))) {
				$relativeDate = strftime($format, $inSeconds);
			} else {
				if ($years > 0) {
					// years and months and days
					$relativeDate .= ($relativeDate ? ', ' : '') . $years . ' ' . __dn('lil', 'year', 'years', $years, true);
					$relativeDate .= $months > 0 ? ($relativeDate ? ', ' : '') . $months . ' ' . __dn('lil', 'month', 'months', $months, true) : '';
					$relativeDate .= $weeks > 0 ? ($relativeDate ? ', ' : '') . $weeks . ' ' . __dn('lil', 'week', 'weeks', $weeks, true) : '';
					$relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . $days . ' ' . __dn('lil', 'day', 'days', $days, true) : '';
				} elseif (abs($months) > 0) {
					// months, weeks and days
					$relativeDate .= ($relativeDate ? ', ' : '') . $months . ' ' . __dn('lil', 'month', 'months', $months, true);
					$relativeDate .= $weeks > 0 ? ($relativeDate ? ', ' : '') . $weeks . ' ' . __dn('lil', 'week', 'weeks', $weeks, true) : '';
					$relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . $days . ' ' . __dn('lil', 'day', 'days', $days, true) : '';
				} elseif (abs($weeks) > 0) {
					// weeks and days
					$relativeDate .= ($relativeDate ? ', ' : '') . $weeks . ' ' . __dn('lil', 'week', 'weeks', $weeks, true);
					$relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . $days . ' ' . __dn('lil', 'day', 'days', $days, true) : '';
                } elseif (strftime('%d', $now)-strftime('%d', $inSeconds)==1 && !$time) {
                    $relativeDate .= __d('lil', 'yesterday', true);
					$backwards = true;
				} elseif (abs($days) > 0) {
					// days and hours
					$relativeDate .= ($relativeDate ? ', ' : '') . $days . ' ' . __dn('lil', 'day', 'days', $days, true);
					if ($time) $relativeDate .= $hours > 0 ? ($relativeDate ? ', ' : '') . $hours . ' ' . __dn('lil', 'hour', 'hours', $hours, true) : '';
                } elseif (strftime('%d', $now)-strftime('%d', $inSeconds)==0 && !$time) {
					// days and hours
					$relativeDate .= __d('lil', 'today', true);
					$backwards = true;
				} elseif (abs($hours) > 0) {
					// hours and minutes
					$relativeDate .= ($relativeDate ? ', ' : '') . $hours . ' ' . __dn('lil', 'hour', 'hours', $hours, true);
					$relativeDate .= $minutes > 0 ? ($relativeDate ? ', ' : '') . $minutes . ' ' . __dn('lil', 'minute', 'minutes', $minutes, true) : '';
				} elseif (abs($minutes) > 0) {
					// minutes only
					$relativeDate .= ($relativeDate ? ', ' : '') . $minutes . ' ' . __dn('lil', 'minute', 'minutes', $minutes, true);
				} else {
					// seconds only
					$relativeDate .= ($relativeDate ? ', ' : '') . $seconds . ' ' . __dn('lil', 'second', 'seconds', $seconds, true);
				}
	
				if (!$backwards) {
					$relativeDate = sprintf(__d('lil', '%s ago', true), $relativeDate);
				}
			}
			return $this->output($relativeDate);
		}
		
		/**
		 * Returns a UNIX timestamp, given either a UNIX timestamp or a valid strtotime() date string.
		 *
		 * @param string $dateString Datetime string
		 * @param boolean $invalid flag to ignore results of fromString == false
		 * @param int $userOffset User's offset from GMT (in hours)
		 * @return string Formatted date string
		 */
		function format($format = '%d-%m-%Y', $date, $invalid = false, $userOffset = null) {
			$date = $this->fromString($date, $userOffset);
			if ($date === false && $invalid !== false) {
				return $invalid;
			} 
			return strftime($format, $date);
		}
		/**
		 * Returns a SELECT element for months.
		 *
		 * @param string $fieldName Prefix name for the SELECT element
		 * @param string $selected Option which is selected.
		 * @param array $attributes Attributes for the select element
		 *		'monthNames' is set and false 2 digit numbers will be used instead of text.
		 *
		 * @param boolean $showEmpty Show/hide the empty select option
		 * @return string
		 */
		function month($fieldName, $selected = null, $attributes = array(), $showEmpty = true) {
			if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
				if (is_array($value)) {
					extract($value);
					$selected = $month;
				} else {
					if (empty($value)) {
						if (!$showEmpty) {
							$selected = 'now';
						}
					} else {
						$selected = $value;
					}
				}
			}
	
			if (strlen($selected) > 2) {
				$selected = date('m', strtotime($selected));
			} elseif ($selected === false) {
				$selected = null;
			}
			
			$data = array();
			for ($m = 1; $m <= 12; $m++) {
				$data[sprintf("%02s", $m)] = strftime("%B", mktime(1, 1, 1, $m, 1, 1999));
			}
	
			return $this->Form->select(
				$fieldName, $data, $selected, $attributes, $showEmpty
			);
		}
		/**
		 * Returns a SELECT element for days.
		 *
		 * @param string $fieldName Prefix name for the SELECT element
		 * @param string $selected Option which is selected.
		 * @param array	 $attributes HTML attributes for the select element
		 * @param mixed $showEmpty Show/hide the empty select option
		 * @return string
		 */
			function day($fieldName, $selected = null, $attributes = array(), $showEmpty = true) {
				if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
					if (is_array($value)) {
						extract($value);
						$selected = $day;
					} else {
						if (empty($value)) {
							if (!$showEmpty) {
								$selected = 'now';
							}
						} else {
							$selected = $value;
						}
					}
				}
		
				if (strlen($selected) > 2) {
					$selected = date('d', strtotime($selected));
				} elseif ($selected === false) {
					$selected = null;
				}
				
				$min = 1;
				$max = 31;

				if (isset($options['min'])) {
					$min = $options['min'];
				}
				if (isset($options['max'])) {
					$max = $options['max'];
				}

				for ($i = $min; $i <= $max; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
				
				return $this->Form->select(
					$fieldName, $data, $selected, $attributes, $showEmpty
				);
			}
	}
?>