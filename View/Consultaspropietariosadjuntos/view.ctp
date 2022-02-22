<div class="consultaspropietariosadjuntos view">
    <h2><?php echo __('Consultaspropietariosadjunto'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Client Id'); ?>: </b>
		<?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['client_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Propietario Id'); ?>: </b>
		<?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['propietario_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Ruta'); ?>: </b>
		<?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['ruta']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $consultaspropietariosadjunto['Consultaspropietariosadjunto']['created']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>