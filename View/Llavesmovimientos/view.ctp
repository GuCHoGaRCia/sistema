<div class="llavesmovimientos view">
    <h2><?php echo __('Llavesmovimiento'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Llave Id'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['llave_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Fecha'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['fecha']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Titulo'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['titulo']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Llavesestado Id'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['llavesestado_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Proveedor Id'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['proveedor_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Consorcio Id'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['consorcio_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Propietario Id'); ?>: </b>
		<?php echo h($llavesmovimiento['Llavesmovimiento']['propietario_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $llavesmovimiento['Llavesmovimiento']['created']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>