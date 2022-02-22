<div class="administraciontransferenciasdetalles view">
    <h2><?php echo __('Administraciontransferenciasdetalle'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Administraciontransferencia Id'); ?>: </b>
		<?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['administraciontransferencia_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Bancoscuenta Id'); ?>: </b>
		<?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['bancoscuenta_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Importe'); ?>: </b>
		<?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['importe']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>