<div class="sueldosempleados view">
    <h2><?php echo __('Sueldosempleado'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['id'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Consorcio Id'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['consorcio_id'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Sueldoscategoria Id'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['sueldoscategoria_id'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Sueldosobrassociale Id'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['sueldosobrassociale_id'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Legajo'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['legajo'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Nombre'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['nombre'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Dni'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['dni'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Cuil'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['cuil'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Hijos'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['hijos'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Ingreso'); ?>: </b>
		<?php echo h(__($sueldosempleado['Sueldosempleado']['ingreso'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $sueldosempleado['Sueldosempleado']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $sueldosempleado['Sueldosempleado']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), array('action' => 'index')); ?>