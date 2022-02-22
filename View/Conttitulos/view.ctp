<div class="conttitulos view">
    <h2><?php echo __('Conttitulo'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($conttitulo['Conttitulo']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Client Id'); ?>: </b>
		<?php echo h($conttitulo['Conttitulo']['client_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Conttitulo Id'); ?>: </b>
		<?php echo h($conttitulo['Conttitulo']['conttitulo_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Codigo'); ?>: </b>
		<?php echo h($conttitulo['Conttitulo']['code']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Titulo'); ?>: </b>
		<?php echo h($conttitulo['Conttitulo']['titulo']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Orden'); ?>: </b>
		<?php echo h($conttitulo['Conttitulo']['orden']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $conttitulo['Conttitulo']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $conttitulo['Conttitulo']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>