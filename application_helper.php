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

