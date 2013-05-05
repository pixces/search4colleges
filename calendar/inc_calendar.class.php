<?php

class inc_calendar
{
	var $cal = "CAL_GREGORIAN";
	var $format = "%Y%m%d";
	var $today;
	var $day;
	var $month;
	var $year;
	var $pmonth;
	var $pyear;
	var $nmonth;
	var $nyear;
	var $wday_names = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	
	function inc_calendar()
	{
		$this->day = "1";
		$today = "";
		$month = "";
		$year = "";
		$pmonth = "";
		$pyear = "";
		$nmonth = "";
		$nyear = "";
	}


	function dateNow($month,$year)
	{
		if(empty($month))
			$this->month = strftime("%m",time());
		else
			$this->month = $month;
		if(empty($year))
			$this->year = strftime("%Y",time());	
		else
		$this->year = $year;
		$this->today = strftime("%d",time());		
		$this->pmonth = $this->month - 1;
		$this->pyear = $this->year - 1;
		$this->nmonth = $this->month + 1;
		$this->nyear = $this->year + 1;

	}

	function daysInMonth($month,$year)
	{
		if(empty($year))
			$year = inc_calendar::dateNow("%Y");

		if(empty($month))
			$month = inc_calendar::dateNow("%m");
		
		if($month == 2)
		{
			if(inc_calendar::isLeapYear($year))
			{
				return 29;
			}
			else
			{
				return 28;
			}
		}
		else if($month==4 || $month==6 || $month==9 || $month==11)
			return 30;
		else
			return 31;
	}

	function isLeapYear($year)
	{
      return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0); 
	}

	function dayOfWeek($month,$year) 
  { 
		if($month > 2) 
				$month -= 2; 
		else 
		{ 
				$month += 10; 
				$year--; 
		} 
		 
		$day =  ( floor((13 * $month - 1) / 5) + 
						$this->day + ($year % 100) + 
						floor(($year % 100) / 4) + 
						floor(($year / 100) / 4) - 2 * 
						floor($year / 100) + 77); 
		 
		$weekday_number = (($day - 7 * floor($day / 7))); 
		 
		return $weekday_number; 
  }

	function getWeekDay()
	{
		$week_day = inc_calendar::dayOfWeek($this->month,$this->year);
		//return $this->wday_names[$week_day];
		return $week_Day;
	}

	function showThisMonth()
	{	
		if(isset($_SESSION['s4c_user_id']) && $_SESSION['s4c_user_id'] != '')
		{
			print  '<div class="calendar_box">
						<div class="month_name_box">
							<div class="month_name_left_arrow">
								<img src="images/callander_left_arrow.png" alt="Previus" width="17" height="17" onclick="alter_calendar(0);" />
							</div>
							<div class="month_name">'.date("F Y", mktime(0, 0, 0, $this->month+1,0,$this->year)).'</div>
							<div class="month_name_right_arrow">
								<img src="images/callander_right_arrow.png" alt="Next" width="17" height="17" onclick="alter_calendar(1);" />
							</div>
						</div>
						<div class="callendar_days">';
							for($i=0;$i<7;$i++){
								$class = strtolower($this->wday_names[$i]);
								echo '<div class="'.$class.'">'.$this->wday_names[$i].'</div>';
							}
			print	'</div>';
						$wday = inc_calendar::dayOfWeek($this->month,$this->year);
						$no_days = inc_calendar::daysInMonth($this->month,$this->year);
						$count = 1;
						for($i=1;$i<=$wday;$i++)
						{
							print '<div class="callendar_dates"></div> ';
							$count++;
						}
						if($wday+$no_days>35)
							$tag=6;
						else
							$tag=5;

						$events = get_records_sql("SELECT * FROM  event event where event.added_by = ".$_SESSION['s4c_user_id']."
													OR event.added_by IN (SELECT  friend_id from friends  where user_id = ".$_SESSION['s4c_user_id']." AND status='active')");

					//get_records('event','status','active','','opening_date,short_description,id,name,added_date,priority,event_type');

						//print_object($events);

						//echo mktime() - 24*3600;

						$p=1;
						$events_datearr = array();
						if(!empty($events)){
							foreach($events as $data){
								
								//echo "ppppp".date('dnY',$data->opening_date);

								$events_datearr[date('dnY',$data->opening_date)][$p]['id'] = $data->id;
								$events_datearr[date('dnY',$data->opening_date)][$p]['name'] = $data->name;
								$events_datearr[date('dnY',$data->opening_date)][$p]['short_description'] = $data->short_description;
								$events_datearr[date('dnY',$data->opening_date)][$p]['event_date'] = date('M d Y',$data->opening_date);
								$events_datearr[date('dnY',$data->opening_date)][$p]['priority'] = $data->priority;
								$events_datearr[date('dnY',$data->opening_date)][$p]['event_type'] = $data->event_type;
								$p++;
							}
						}
						//print_object($events_datearr);
						$rec=0;
						for($i=1;$i<=$no_days;$i++)
						{
								if($i < 10){
									$comparedate = '0'.$i.$this->month.$this->year;
									$comparedate1 = '0'.$i.','.$this->month.','.$this->year;
								}
								else{
									$comparedate = $i.$this->month.$this->year;
									$comparedate1 = $i.','.$this->month.','.$this->year;
								}
								
								//echo $comparedate;
								//echo $this->today;
								if($i == $this->today)
								{
									
									$eventtoday = '';
									foreach($events_datearr as $key => $events_datearr2)
									{
										if($comparedate == $key)
										{
											$new_arr=array_merge(array(),$events_datearr2);
											//echo "Paul:";
											//print_r(new_arr);
										
											foreach($new_arr as $events_datearr1)
											{
											//echo $events_datearr1[$comparedate]['name'];
											
												$hover_message = "<br />Event: ".$events_datearr1['name']."<br />";
												$hover_message .= "Event Type: ".$events_datearr1['event_type']."<br />";
												$hover_message .= "Event date: ".$events_datearr1['event_date']."<br />";
												$hover_message .= "Priority: ".$events_datearr1['priority']."<br />";
												$hover_message .= "Description: ".$events_datearr1['short_description']."<br /><br />";
												if($events_datearr1['priority'] == 'high')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:red;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												elseif($events_datearr1['priority'] == 'normal')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:blue;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												elseif($events_datearr1['priority'] == 'low')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:green;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												else
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}

											}
										}
									}
									print '<div class="callendar_dates_current"><span class="callendar_numbers">
									<a  href="events.html?date='.$comparedate1.'" title="Add event">'.$i.'</a></span>'.$eventtoday.'</div>';
								}
								else
								{
									$eventtoday = '';
									$today = date("on");
									$divclass = 'class="callendar_dates"';
									if($i< 10)
										 $thismonthdate = $this->year.$this->month.'0'.$i."<br />";
									else
										$thismonthdate = $this->year.$this->month.$i;
									
									if($thismonthdate <= $today.$this->today){
									
									//New Code
									//print_object($events_datearr);

									foreach($events_datearr as $key => $events_datearr2)
									{
										if($comparedate == $key)
										{
											$new_arr=array_merge(array(),$events_datearr2);
											
											foreach($new_arr as $events_datearr1)
											{
												$hover_message = "<br />Event: ".$events_datearr1['name']."<br />";
												$hover_message .= "Event Type: ".$events_datearr1['event_type']."<br />";
												$hover_message .= "Event date: ".$events_datearr1['event_date']."<br />";
												$hover_message .= "Priority: ".$events_datearr1['priority']."<br />";
												$hover_message .= "Description: ".$events_datearr1['short_description']."<br /><br />";

												$divclass = 'class="callendar_dates_past"';
												if($events_datearr1['priority'] == 'high')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:red;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												elseif($events_datearr1['priority'] == 'normal')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:blue;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												elseif($events_datearr1['priority'] == 'low')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:green;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												else
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
											}
										}
									}
									//End of New Code

									}
									else{
									foreach($events_datearr as $key => $events_datearr2)
									{
										if($comparedate == $key)
										{
											$new_arr=array_merge(array(),$events_datearr2);
											
											foreach($new_arr as $events_datearr1)
											{
											$hover_message = "<br />Event: ".$events_datearr1['name']."<br />";
											$hover_message .= "Event Type: ".$events_datearr1['event_type']."<br />";
											$hover_message .= "Event date: ".$events_datearr1['event_date']."<br />";
											$hover_message .= "Priority: ".$events_datearr1['priority']."<br />";
											$hover_message .= "Description: ".$events_datearr1['short_description']."<br /><br />";
											$divclass = 'class="callendar_dates"';

												if($events_datearr1['priority'] == 'high')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:red;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												elseif($events_datearr1['priority'] == 'normal')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:blue;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												elseif($events_datearr1['priority'] == 'low')
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer; color:green;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
												else
												{
													$eventtoday .= '<span class="callendar_text"><a class="eventlinks_'.$events_datearr1['priority'].' tipz"  style="cursor:pointer;"  title="'.$hover_message.' "  >'.$events_datearr1['name'].'</a></span>';
												}
											}
										}
									}
									}
									print '<div '.$divclass.'><span class="callendar_numbers"><a href="events.html?date='.$comparedate1.'" title="Add event">'.$i.'</a></span>'.$eventtoday.'</div>';
								}
								$rec++;
						}
						for($i=$no_days+$wday;$i<$tag*7;$i++)
						{
								print '<div class="callendar_dates"><span class="callendar_numbers"></span></div>';
						}

			print	'</div>'; 
		/*print '<table cellpadding="2" cellspacing="2" border=1 bordercolor="cccccc">';
		print '<tr>
				<td colspan="7"><input type="button" name="prev" value="<<Prev" onclick="showPrevMonth();"><b>'.date("F Y", mktime(0, 0, 0, $this->month,0,$this->year)).'</b><input type="button" name="next" value="Next>>" onclick="showNextMonth();">
				</td>
				</tr>';
		print '<tr>';
		for($i=0;$i<7;$i++)
			print '<td width="40" height="30" bgcolor="#ff9000" align="center">'. $this->wday_names[$i]. '</td>';
		print '</tr>';		
		$wday = inc_calendar::dayOfWeek($this->month,$this->year);
		$no_days = inc_calendar::daysInMonth($this->month,$this->year);
		$count = 1;
		print '<tr>';
		for($i=1;$i<=$wday;$i++)
		{
			print '<td align="center" height="25">&nbsp;</td>';
			$count++;
		}
		for($i=1;$i<=$no_days;$i++)
		{
				if($count > 6)
				{
					if($i == $this->today)
					{
						print '<td align="center" height="25"><font color="#0000ff">' . $i . '</font></td></tr>';
					}
					else
					{
						print '<td align="center" height="25"><font color="#000000">' . $i . '</font></td></tr>';
					}
					$count = 0;
				}
				else
				{
					if($i == $this->today)
					{
						print '<td align="center" height="25"><font color="#0000ff">' . $i . '</font></td>';
					}
					else
					{
						print '<td align="center" height="25"><font color="#000000">' . $i . '</font></td>';
					}
				}
				$count++;
		}
		print '</tr></table>';*/
	} 

  }
}
?>