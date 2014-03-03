<?php
/**
 * This class is based on the concept in the CronParser class written by Mick Sear http://www.ecreate.co.uk
 * The following functions are direct copies from or based on the original class:
 *  expandRanges, sanitize, getNbDaysInMonth, initDaysCronArray
 *
 * @author paztaga
 * @version 0.1
 * @example
 * $cron_str0 = "0,12,30-51 3,21-23,10 1-25 9-12,1 0,3-7";
 * require_once("bosjqmCronParser.class.php");
 * $cron = new bosjqmCronParser();
 * $cron->calcNextRun($cron_str0);
 * echo "Cron '$cron_str0' next due at: " . $cron->getNextRun() . "<p>";
 */
class CronParser {
  private
    $bits = array(), //exploded String like 0 1 * * *
   	$now = array(),	//Array of cron-style entries for time()
   	$next_run, 		//Timestamp of next run time.
  	$year, //computed year
  	$month, //computed month
  	$day, //computed day
  	$hour, //computed hour
  	$minute, //computed minute
  	$equal, //indicate whether or not the computed time is equal to current time - if yes add minute and process new calcul
  	$count_invalid_day_range, //for invalid cron range (eg: 30 february), there's a counter that avoid infinitate loop
  	$minutes_arr = array(),	//minutes array based on cron string
  	$days_arr = array(), //days array based on cron string
  	$hours_arr = array(),	//hours array based on cron string
  	$months_arr = array();	//months array based on cron string

	public function getNextRun() {
		return explode(",", strftime("%M,%H,%d,%m,%w,%Y", $this->next_run)); //Get the values for now in a format we can use
	}

	public function getNextRunUnix() {
		return $this->next_run;
	}

	/**
	 *  Calculate the last due time before this moment
	 */
	public function calcNextRun($string) {

 		$tstart = microtime();
		$this->count_invalid_day_range = 0;
		$this->equal = true;

		$string = preg_replace('/[\s]{2,}/', ' ', $string);

		if (preg_match('/[^-,* \\d]/', $string) !== 0) {
			throw new Exception("Cron String contains invalid character");
			return false;
		}

 		$this->bits = @explode(" ", $string);

		if (count($this->bits) != 5) {
			throw new Exception("Cron string is invalid. Too many or too little sections after explode");
			return false;
		}

		//put the current time into an array
		$t = strftime("%M,%H,%d,%m,%w,%Y", time());
		$this->now = explode(",", $t);

		$this->year = $this->now[5];
		$this->initMonthsCronArray();
		
		//l'init day dépand de l'année et du mois, on le calcul donc à la demande
		//$this->initDaysCronArray();
		$this->initHoursCronArray();
		$this->initMinutesCronArray();
		
		$this->calcNextMonth();
		if ($this->equal) {
		  $this->addMinute();
		}

		$this->next_run = mktime($this->hour, $this->minute, 0, $this->month, $this->day, $this->year);
		return true;
	}

	/**
	 * Assumes that value is not *, and creates an array of valid numbers that
	 * the string represents.  Returns an array.
	 */
	private static function expandRanges($str) {
		if (strstr($str,  ",")) {
			$arParts = explode(',', $str);
			foreach ($arParts AS $part) {
				if (strstr($part, '-')) {
					$arRange = explode('-', $part);
					for ($i = $arRange[0]; $i <= $arRange[1]; $i++) {
						$ret[] = $i;
					}
				} else {
					$ret[] = $part;
				}
			}

		} elseif (strstr($str,  '-')) {
			$arRange = explode('-', $str);
			for ($i = $arRange[0]; $i <= $arRange[1]; $i++) {
				$ret[] = $i;
			}

		} else {
			$ret[] = $str;
		}

		$ret = array_unique($ret);
		sort($ret);
		return $ret;
	}

	//remove the out of range array elements. $arr should be sorted already and does not contain duplicates
	private static function sanitize ($arr, $low, $high) {
		$count = count($arr);
		for ($i = 0; $i <= ($count - 1); $i++) {
			if ($arr[$i] < $low) {
				unset($arr[$i]);
			} else {
				break;
			}
		}

		for ($i = ($count - 1); $i >= 0; $i--) {
			if ($arr[$i] > $high) {
				unset ($arr[$i]);
			} else {
				break;
			}
		}

		//re-assign keys
		sort($arr);
		return $arr;
	}



	private static function getNbDaysInMonth($month, $year) {
		return date('t', mktime(0, 0, 0, $month, 1, $year));
	}



	private function addMonth() {
	  $this->equal = false;
	  if ($this->now[3] == max($this->months_arr)) {
	    $this->resetMonth();
	  } else {
	    $this->now[3]++;
	  }
	  $this->calcNextMonth();
	}


	private function resetMonth() {
	  $this->equal = false;
	  $this->year++;
	  $this->now[3] = min($this->months_arr);
	}


	private function calcNextMonth() {
	  foreach ($this->months_arr as $month) {

	    if ($this->now[3] <= $month) {
	      if ($this->now[3] < $month) {
	        $this->equal = false;
	      }

	      $this->month = $month;
	      $this->calcNextDay();
	      return ;
	    }
	  }

	  //le prochain mois n'est pas dans le range en cours
	  //on incrémente mois, on réinitialise le mois à 1 et on recalcule
	  $this->resetMonth();
    $this->calcNextMonth();
	}


	private function resetDay() {
	  $this->equal = false;
	  $this->now[2] = 1;
	  $this->addMonth();
	}


	private function addDay() {
	  $this->equal = false;
    if ($this->now[2] == max($this->days_arr)) {
      $this->resetDay();
      //ne pas lancer le calcNextDay car resetDay lance addMonth qui appel calcNextMonth qui appel calcNextDay
    } else {
      $this->now[2]++;
      $this->calcNextDay();
    }
	}


  private function calcNextDay() {
	  $this->initDaysCronArray();
	  foreach ($this->days_arr as $day) {
	  
      if ($this->now[2] <= $day) {
        if ($this->now[2] < $day) {
          $this->equal = false;
        }

	      $this->day = $day;
	      
	      $this->calcNextHour();

        return ;
      }
	  }

	  $this->resetDay();
	}


	private function resetHour() {
	  $this->equal = false;
	  $this->now[1] = min($this->hours_arr);
	  $this->addDay();
	}

	private function addHour() {
    $this->equal = false;

    if ($this->hour == max($this->hours_arr)) {
      $this->resetHour();
    } else {
      $this->now[1]++;
      $this->calcNextHour();
    }
	}


	private function calcNextHour() {
	  foreach ($this->hours_arr as $hour) {
	    if ($this->now[1] <= $hour) {
	      if ($this->now[1] < $hour) {
	        $this->equal = false;
	      }
        $this->hour = $hour;
        // added by miha nahtigal; this is questionable
		//$this->now[0] = min($this->minutes_arr);
        $this->calcNextMinute();
	      return ;
	    }
	  }

	  $this->resetHour();
	}


	private function resetMinute() {
	  $this->equal = false;
	  $this->now[0] = min($this->minutes_arr);
	  $this->addHour();
	}

	private function addMinute() {
    $this->equal = false;

    if ($this->minute == max($this->minutes_arr)) {
      $this->resetMinute();
    } else {
      $this->now[0]++;
      $this->calcNextMinute();
    }
	}


	private function calcNextMinute() {
	  foreach ($this->minutes_arr as $min) {
	    if ($this->now[0] <= $min) {
	      if ($this->now[0] < $min) {
	        $this->equal = false;
	      }
        $this->minute = $min;
	      return ;
	    }
	  }
	  $this->resetMinute();
	}


	private function initMonthsCronArray() {
		if (empty($this->months_arr)) {
			$months = array();
			if ($this->bits[3] == '*') {
				for ($i = 1; $i <= 12; $i++) {
					$months[] = $i;
				}
			} else {
				$months = self::expandRanges($this->bits[3]);
				$months = self::sanitize($months, 1, 12);
			}

			if (!count($months)) {
			  throw new Exception('Month range is not valid');
			}

			$this->months_arr = $months;
		}
	}

	private function initHoursCronArray() {
		$hours = array();

		if ($this->bits[1] == '*') {
			for ($i = 0; $i <= 23; $i++) {
				$hours[] = $i;
			}
		} else {
			$hours = self::expandRanges($this->bits[1]);
			$hours = self::sanitize($hours, 0, 23);
		}

		if (!count($hours)) {
		  throw new Exception('Hour range is not valid');
		}
		$this->hours_arr = $hours;
	}

	private function initMinutesCronArray() {
		$minutes = array();

		if ($this->bits[0] == '*') {
			for ($i = 0; $i <= 60; $i++) {
				$minutes[] = $i;
			}
		} else {
			$minutes = self::expandRanges($this->bits[0]);
			$minutes = self::sanitize($minutes, 0, 59);
		}

		if (!count($minutes)) {
		  throw new Exception('Minute range is not valid');
		}
		$this->minutes_arr = $minutes;
	}

	//given a month/year, return an array containing all the days in that month
	private static function getMonthDaysArray($month, $year) {
		$days = array();
		$daysinmonth = self::getNbDaysInMonth($month, $year);
		for ($i = 1; $i <= $daysinmonth; $i++) {
			$days[] = $i;
		}
		return $days;
	}


  //given a month/year, list all the days within that month fell into the week days list.
	private function initDaysCronArray() {
		$days = array();
		$month = $this->month; $year = $this->year;

		//return everyday of the month if both bit[2] and bit[4] are '*'
		if ($this->bits[2] == '*' AND $this->bits[4] == '*') {
			$days = self::getMonthDaysArray($month, $year);
		} else {
			//create an array for the weekdays
			if ($this->bits[4] == '*') {
				for ($i = 0; $i <= 6; $i++) {
					$arWeekdays[] = $i;
				}
			} else {
				$arWeekdays = self::expandRanges($this->bits[4]);
				$arWeekdays = self::sanitize($arWeekdays, 0, 7);

				//map 7 to 0, both represents Sunday. Array is sorted already!
				if (in_array(7, $arWeekdays)) {
					if (in_array(0, $arWeekdays)) {
						array_pop($arWeekdays);
					} else {
						$tmp[] = 0;
						array_pop($arWeekdays);
						$arWeekdays = array_merge($tmp, $arWeekdays);
					}
				}
			}

			if ($this->bits[2] == '*') {
				$daysmonth = $this->getMonthDaysArray($month, $year);
			} else {
				$daysmonth = self::expandRanges($this->bits[2]);
				// so that we do not end up with 31 of Feb
				$daysinmonth = self::getNbDaysInMonth($month, $year);
				$daysmonth = self::sanitize($daysmonth, 1, $daysinmonth);
			}

			//Now match these days with weekdays
			foreach ($daysmonth AS $day) {
				$wkday = date('w', mktime(0, 0, 0, $month, $day, $year));
				if (in_array($wkday, $arWeekdays)) {
					$days[] = $day;
				}
			}
		}
		//self::sanitize($days, 1, self::getNbDaysInMonth($month, $year));

		if(!count($days)) {
		  $this->count_invalid_day_range++;
		}

		if ($this->count_invalid_day_range == 12) {
		  throw new Exception('Day range is not valid');
		}
		$this->days_arr = $days;
	}

}