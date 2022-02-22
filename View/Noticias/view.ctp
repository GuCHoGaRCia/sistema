<div class="noticias view" id="seccionaimprimir">
    <h2 id="noimprimir"><?php echo __('Noticia'); ?></h2>
    <fieldset>
        <h4><?php echo h(__($noticia['Noticia']['titulo'])); ?></h4>
        <?php echo $noticia['Noticia']['noticia']; ?>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), array('action' => 'index')); ?>
<style type="text/css" media="print">
    @page { size: landscape; }
</style>