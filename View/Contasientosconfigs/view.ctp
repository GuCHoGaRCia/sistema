<div class="contasientosconfigs view">
    <h2><?php echo __('Contasientosconfig'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($contasientosconfig['Contasientosconfig']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Consorcio Id'); ?>: </b>
		<?php echo h($contasientosconfig['Contasientosconfig']['consorcio_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Config'); ?>: </b>
		<?php echo h($contasientosconfig['Contasientosconfig']['config']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $contasientosconfig['Contasientosconfig']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $contasientosconfig['Contasientosconfig']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>