<div class="contejercicios view">
    <h2><?php echo __('Contejercicio'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($contejercicio['Contejercicio']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Client Id'); ?>: </b>
		<?php echo h($contejercicio['Contejercicio']['client_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Consorcio Id'); ?>: </b>
		<?php echo h($contejercicio['Contejercicio']['consorcio_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Nombre'); ?>: </b>
		<?php echo h($contejercicio['Contejercicio']['nombre']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Inicio'); ?>: </b>
		<?php echo h($contejercicio['Contejercicio']['inicio']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Fin'); ?>: </b>
		<?php echo h($contejercicio['Contejercicio']['fin']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $contejercicio['Contejercicio']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $contejercicio['Contejercicio']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>