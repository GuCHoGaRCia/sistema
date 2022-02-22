<div class="administracionefectivos view">
    <h2><?php echo __('Administracionefectivo'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($administracionefectivo['Administracionefectivo']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Proveedorspago Id'); ?>: </b>
		<?php echo h($administracionefectivo['Administracionefectivo']['proveedorspago_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Bancoscuenta Id'); ?>: </b>
		<?php echo h($administracionefectivo['Administracionefectivo']['bancoscuenta_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Anulado'); ?>: </b>
		<?php echo h($administracionefectivo['Administracionefectivo']['anulado']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $administracionefectivo['Administracionefectivo']['created']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>