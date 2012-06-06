<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

if (! function_exists('ci'))
{
	function ci()
	{
		return get_instance();
	}
}

function render_flash_data()
{
	foreach (array('info', 'error', 'success', 'warning') as $type)
	{
		if (ci()->session->flashdata($type))
		{
			?>
				<div class="alert alert-<?php echo $type; ?>">
					<?php echo ci()->session->flashdata($type); ?> 
				</div>
			<?php
		}
	}
}

function flash($type, $message)
{
	ci()->session->set_flashdata($type, $message);
}

function flash_success($message)
{
	flash('success', $message);
}

function flash_warning($message)
{
	flash('warning', $message);
}

function flash_error($message)
{
	flash('error', $message);
}

function flash_info($message)
{
	flash('info', $message);
}


function current_user()
{
	return ci()->auth->current_user();
}

function display_errors($errors)
{
	if (empty($errors)) return '';
	
	return ci()->load->view('shared/form_errors', array('errors' => $errors), TRUE);
}

function gravatar_for($email_addr, $size = 20)
{
	$hash = md5(strtolower(trim($email_addr)));
	return '<image class="gravatar" src="http://www.gravatar.com/avatar/'.$hash.'?s='.$size.'" />';
}

function arr_extract($array, $allowed = array())
{
	$data = array();
	
	foreach ($array as $key => $element)
	{
		if (in_array($key, $allowed)) $data[$key] = $element; 
	}
	
	return $data;
}

function active_on($params)
{
	$controller = ci()->router->fetch_class();
	$action = ci()->router->fetch_method();
	
	if (! is_array($params)) $params = array($params);
	
	if (in_array("{$controller}/{$action}", $params))
	{
		return 'class="active"';
	}
	else
	{
		return '';
	}
}

function updated_fields_for($object, $updates)
{
	$data = array();
	
	foreach ($updates as $prop => $val)
	{
		if (property_exists($object, $prop) and $object->{$prop} != $val) $data[$prop] = $val;
	}
	
	return $data;
}

function months()
{
	return array(
		1 => 'Jan',
		2 => 'Feb',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'Aug',
		9 => 'Sep',
		10 => 'Oct',
		11 => 'Nov',
		12 => 'Dec',
	);
}


function crop_resize($dest_path, $src_path, $dest_x, $dest_y, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h)
{
    $ext = pathinfo($src_path, PATHINFO_EXTENSION);
	$dest = imagecreatetruecolor($dest_w, $dest_h);

	switch ($ext)
	{
		case 'jpg':
		case 'jpeg':
			$src = imagecreatefromjpeg($src_path); 
		break;

		case 'png':
			$src = imagecreatefrompng($src_path);
		break;

		case 'gif':
			$src = imagecreatefromgif($src_path);
		break;

		default:
			throw new Exception('Format not supported.');
		break;
	}

	imagecopyresized($dest, $src, $dest_x, $dest_y, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h);

	switch ($ext)
	{
		case 'jpg':
		case 'jpeg':
			return imagejpeg($dest, $dest_path, 100);
		break;

		case 'png':
			return imagepng($dest, $dest_path, 9);
		break;

		case 'gif':
			return imagegif($dest, $dest_path);
		break;

		default:
			throw new Exception('Format not supported.');
		break;
	}
}

/**
 * Timespan
 *
 * Returns a span of seconds in this format:
 *	10 days 14 hours 36 minutes 47 seconds
 *
 * @access	public
 * @param	integer	a number of seconds
 * @param	integer	Unix timestamp
 * @return	integer
 */	
if ( ! function_exists('time_since'))
{
	function time_since($time)
	{

		$now = time();
		$now_day = date("j", $now);
		$now_month = date("n", $now);
		$now_year = date("Y", $now);

		$time_day = date("j", $time);
		$time_month = date("n", $time);
		$time_year = date("Y", $time);
		$time_since = "";

		switch(TRUE) 
		{
			case ($time == 0):
				$time_since = 'Never';
				break;
			case ($now-$time < 60):
				// RETURNS SECONDS
				$seconds = $now-$time;
	                        // Append "s" if plural
				$time_since = $seconds > 1 ? "$seconds seconds" : "$seconds second";
				break;
			case ($now-$time < 45*60): // twitter considers > 45 mins as about an hour, change to 60 for general purpose
				// RETURNS MINUTES
				$minutes = round(($now-$time)/60);
				$time_since = $minutes > 1 ? "$minutes minutes" : "$minutes minute";
				break;
			case ($now-$time < 86400):
				// RETURNS HOURS
				$hours = round(($now-$time)/3600);
				$time_since = $hours > 1 ? "about $hours hours" : "about $hours hour";
				break;
			case ($now-$time < 1209600):
				 // RETURNS DAYS
				 $days = round(($now-$time)/86400);
				 $time_since = $days > 1 ? "$days days" : "$days day";
				 break;
			case (mktime(0, 0, 0, $now_month-1, $now_day, $now_year) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
				 // RETURNS WEEKS
				 $weeks = round(($now-$time)/604800);
				 $time_since = "$weeks weeks";
				 break;
			case (mktime(0, 0, 0, $now_month, $now_day, $now_year-1) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
				 // RETURNS MONTHS
				 if($now_year == $time_year) { $subtract = 0; } else { $subtract = 12; }
				 $months = round($now_month-$time_month+$subtract);
				 $time_since = "$months months";
				 break;
			default:
			// RETURNS YEARS
				if ($now_month < $time_month) 
				{
					$subtract = 1;
				} 
				elseif ($now_month == $time_month) 
				{
					if ($now_day < $time_day) 
					{ 
						$subtract = 1; 
					} 
					else 
					{ 
						$subtract = 0; 
					}
				} 
				else 
				{
					$subtract = 0;
				}
				$years = $now_year-$time_year-$subtract;
				$time_since = "$years years";
				break;
		}
	
		return $time_since .' ago';
	}
}

