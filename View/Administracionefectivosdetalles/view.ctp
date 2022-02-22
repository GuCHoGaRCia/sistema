<div class="administracionefectivosdetalles view">
    <h2><?php echo __('Administracionefectivosdetalle'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Administracionefectivo Id'); ?>: </b>
		<?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['administracionefectivo_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Consorcio Id'); ?>: </b>
		<?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['consorcio_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Importe'); ?>: </b>
		<?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['importe']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>