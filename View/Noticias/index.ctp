<div class="noticias index">
    <h2><?php echo __('Noticias'); ?></h2>
    <div class="warning"><b>&Uacute;ltima noticia: <?= isset($noticias[0]['Noticia']['created']) ? $this->Time->timeAgoInWords($noticias[0]['Noticia']['created']) : '--' ?>. Recuerde chequear peri&oacute;dicamente las noticias!</b></div>
    <div class='toolbar' style="<?= isset($style) ? $style : '' ?>">
        <b>¿C&oacute;mo prorratear una liquidaci&oacute;n ?</b> 
        <?php
        echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', 1)));
        ?>
        <div class='busc'>
            <?php
            echo $this->Form->create('Noticia', ['id' => 'busqform']);
            echo $this->Form->input('buscar', ['id' => 'busq', 'div' => false, 'value' => isset($this->request->params['named']['buscar']) ? str_replace('-', '/', $this->request->params['named']['buscar']) : '', 'required' => 'false', 'label' => '', 'maxlength' => 20, 'style' => 'width:160px;font-family:Courier', /* 'pattern' => '[\d\w\s]', 'title' => 'Caracteres permitidos: letras, numeros, punto, @, \' o espacios' */]);
            echo $this->Form->submit('search.png', ['div' => false, 'class' => 'searchimg', 'alt' => __('Buscar'), 'title' => __('Buscar'), 'onsubmit' => 'busq()']);
            echo $this->Form->end();
            // reemplazo las / por - y acomodo el formato de la fecha
            ?>
            <script>
                $("#busqform").submit(function (event) {
                    $("#busq").val($("#busq").val().replace(/\//g, '-'));
                });
            </script>
        </div>
    </div>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('titulo', __('Título')); ?></th>
                <th><?php echo $this->Paginator->sort('noticia', __('Vista previa')); ?></th>
                <th style="width:80px"><?php echo $this->Paginator->sort('created', __('Fecha')); ?></th>
                <th class="acciones" style="width:80px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($noticias as $noticia):
                if ($noticia['Noticia']['id'] == 1) {
                    continue;
                }
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($noticia['Noticia']['titulo']) ?>&nbsp;</td>
                    <td><?php echo $noticia['Noticia']['noticia'] ?>&nbsp;</td>
                    <td><span><?php echo $this->Time->format(__('d/m/Y'), $noticia['Noticia']['created']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $noticia['Noticia']['id'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="4"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>