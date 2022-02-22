<div class="administraciontransferencias view">
    <h2><?php echo __('Administraciontransferencia'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($administraciontransferencia['Administraciontransferencia']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Proveedorspago Id'); ?>: </b>
		<?php echo h($administraciontransferencia['Administraciontransferencia']['proveedorspago_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Bancoscuenta Id'); ?>: </b>
		<?php echo h($administraciontransferencia['Administraciontransferencia']['bancoscuenta_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Anulado'); ?>: </b>
		<?php echo h($administraciontransferencia['Administraciontransferencia']['anulado']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $administraciontransferencia['Administraciontransferencia']['created']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>