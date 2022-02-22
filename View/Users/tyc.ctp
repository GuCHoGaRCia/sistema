<div class="users form">
    <?php
    echo $this->Form->create('User', array('class' => 'jquery-validation'));
    echo $this->Form->input('acepta', array('value' => 1, 'type' => 'hidden'));
    ?>
    <div>
        <h3>T&eacute;rminos y Condiciones</h3>
        blablabla
    </div>
    <?php echo $this->Form->end(__('Aceptar')); ?>
    <?php
    echo $this->Form->create('User', array('class' => 'jquery-validation'));
    echo $this->Form->input('acepta', array('value' => 0, 'type' => 'hidden'));
    echo $this->Form->end(__('Cancelar'));
    ?>
</div>