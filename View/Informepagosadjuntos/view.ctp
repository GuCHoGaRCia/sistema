<div class="informepagosadjuntos view">
    <h2><?php echo __('Informepagosadjunto'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($informepagosadjunto['Informepagosadjunto']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Informepago Id'); ?>: </b>
		<?php echo h($informepagosadjunto['Informepagosadjunto']['informepago_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Ruta'); ?>: </b>
		<?php echo h($informepagosadjunto['Informepagosadjunto']['ruta']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $informepagosadjunto['Informepagosadjunto']['created']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>