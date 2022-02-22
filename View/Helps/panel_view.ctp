<div class="gastosGenerales view">
    <h2><?php echo __('Ayudas'); ?></h2>
    <fieldset>
		<b><?php echo __('Sección'); ?>: </b>
		<?php echo h(__($help['Help']['controller'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Acción'); ?>: </b>
		<?php echo h(__($help['Help']['action'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Descripción'); ?>: </b>
		<?php echo $help['Help']['content']; ?>
			&nbsp;
		<br>
		<b><?php echo __('Habilitada'); ?>: </b>
		<?php echo $this->Html->image(h($help['Help']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitado'))); ?>
			&nbsp;
		<br>		<b><?php echo __('Creado'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $help['Help']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modificado'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $help['Help']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>