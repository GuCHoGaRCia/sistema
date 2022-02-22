<div class="liquidationsTypes view">
    <h2><?php echo __('Tipos de liquidaciones'); ?></h2>
    <fieldset>
		<b><?php echo __('Nombre'); ?>: </b>
		<?php echo h(__($liquidationsType['LiquidationsType']['name'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Habilitado'); ?>: </b>
		<?php echo $this->Html->image(h(__($liquidationsType['LiquidationsType']['enabled']) ? '1' : '0') . '.png', array('title' => __('Habilitado'))); ?>
			&nbsp;
		<br>
		<b><?php echo __('Creado'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $liquidationsType['LiquidationsType']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modificado'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $liquidationsType['LiquidationsType']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>