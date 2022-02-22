<div class="proveedorspagosacuentas view">
    <h2><?php echo __('Proveedorspagosacuenta'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Proveedor Id'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['proveedor_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('User Id'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['user_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Fecha'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['fecha']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Concepto'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['concepto']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Importe'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['importe']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Anulado'); ?>: </b>
		<?php echo h($proveedorspagosacuenta['Proveedorspagosacuenta']['anulado']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $proveedorspagosacuenta['Proveedorspagosacuenta']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $proveedorspagosacuenta['Proveedorspagosacuenta']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>