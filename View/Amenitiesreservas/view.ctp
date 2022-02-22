<div class="amenitiesreservas view">
    <h2><?php echo __('Amenitiesreserva'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($amenitiesreserva['Amenitiesreserva']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Amenitie Id'); ?>: </b>
		<?php echo h($amenitiesreserva['Amenitiesreserva']['amenitie_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Fecha'); ?>: </b>
		<?php echo h($amenitiesreserva['Amenitiesreserva']['fecha']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Amenitiesturno Id'); ?>: </b>
		<?php echo h($amenitiesreserva['Amenitiesreserva']['amenitiesturno_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Propietario Id'); ?>: </b>
		<?php echo h($amenitiesreserva['Amenitiesreserva']['propietario_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $amenitiesreserva['Amenitiesreserva']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $amenitiesreserva['Amenitiesreserva']['modified']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Cancelado'); ?>: </b>
		<?php echo h($amenitiesreserva['Amenitiesreserva']['cancelado']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>