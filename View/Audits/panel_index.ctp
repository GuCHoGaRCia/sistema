<div class="index">
    <h2><?= __('Auditoría') ?></h2>
    <?php
    echo "<div class='inline'>";
    echo $this->Form->create('Audit', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('client_id', ['label' => false, 'empty' => '', 'options' => $clientes, 'required' => false, 'type' => 'select', 'selected' => isset($c) ? $c : '']);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => !empty($d) ? $d : date('d/m/Y')]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => !empty($h) ? $h : date('d/m/Y')]);
    echo $this->Form->input('buscar', ['type' => 'text', 'label' => false, 'style' => 'width:100px']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px;margin-left:0px']);
    echo '</div>';
    ?>
    <table cellpadding="0" cellspacing="0" id="seccionaimprimir">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?= $this->Paginator->sort('client_id', 'Cliente'); ?></th>
                <th><?= $this->Paginator->sort('event', 'Evento'); ?></th>
                <th><?= $this->Paginator->sort('description', 'Usuario'); ?></th>
                <th><?= $this->Paginator->sort('model', 'Sección'); ?></th>
                <th><?= __("Identificador registro") ?></th>
                <th><?= $this->Paginator->sort('created', 'Fecha'); ?></th>
                <th class="actions"><?= __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($audits as $audit):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?= $class ?>>
                    <td class="borde_tabla"></td>
                    <td><?= h(substr($audit['Audit']['description'], strpos($audit['Audit']['description'], '@') + 1)) ?></td>                        
                    <td><?= h($audit['Audit']['event']) ?></td>
                    <td><?= h($audit['Audit']['description']) ?>&nbsp;</td>
                    <td><?= h($audit['Audit']['model']); ?></td>                        
                    <td><?= h($audit['Audit']['entity_id']); ?></td>
                    <td><span title="<?= $this->Time->timeAgoInWords($audit['Audit']['created']) ?>"><?= $this->Time->format(__('d/m/Y H:i:s'), $audit['Audit']['created']) ?></span></td>
                    <td class="actions">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style='width:400px;right:175px'>
                                <ul>
                                    <li>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <?php echo __('Auditoría detallada del movimiento'); ?>
                                            </div>
                                            <div class="panel-body">
                                                <fieldset>
                                                    <?php
                                                    if (!empty($audit['AuditDelta'])) {
                                                        if ($audit['Audit']['event'] !== "CREATE") {
                                                            foreach ($audit['AuditDelta'] as $auditDeltas) {
                                                                ?>
                                                                <b><?= __('Campo') . ": " ?></b>
                                                                <?= h($auditDeltas['property_name']) ?>
                                                                <br><br>
                                                                <b><?= __('Valor anterior') . ": " ?></b>
                                                                <?= h($auditDeltas['old_value']) ?>
                                                                <br><br>
                                                                <b><?= __('Valor nuevo') . ": " ?></b>
                                                                <?= h($auditDeltas['new_value']) ?>
                                                                <br><br>
                                                                <?php
                                                            }
                                                        } else {
                                                            // es una auditoria de CREATE, muestro el json_object
                                                            ?>
                                                            <b><?= __('Registro creado') . ": " ?></b>
                                                            <?php
                                                            echo "<code style='word-wrap: break-word;line-height:20px'>";
                                                            echo h($audit['Audit']['json_object']);
                                                            echo "</code>"
                                                            ?>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </span>
                        </span>
                        <?= $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $audit['Audit']['id']], ['escape' => false]); ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="7"></td>
                <td class="bottom_d"></td>
            </tr>
        </tbody>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#AuditClientId").select2({language: "es", placeholder: "<?= __('Seleccione cliente...') ?>", allowClear: true});
    });
</script>