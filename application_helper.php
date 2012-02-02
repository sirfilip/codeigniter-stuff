<?php

function ci()
{
	return get_instance();
}

function render_flash_data()
{
	foreach (array('info', 'error', 'success', 'warning') as $type)
	{
		if (ci()->session->flashdata($type))
		{
			?>
				<div class="alert-message <?php echo $type; ?>">
					<?php echo ci()->session->flashdata($type); ?> 
				</div>
			<?php
		}
	}
}