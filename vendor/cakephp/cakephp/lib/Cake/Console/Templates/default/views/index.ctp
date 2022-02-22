<?php
/*
  modificado por esteban 14/07/14 para CEOnline
 */
?>
<div class="<?php echo $pluralVar; ?> index">
    <h2><?php echo "<?php echo __('{$pluralHumanName}'); ?>"; ?></h2>
    <?php echo "<?php echo \$this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => '{$modelClass}']); ?>\n"; ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <?php
                $cantidad = 1; // la columna "Acciones" suma 1
                foreach ($fields as $field):
                    if (!in_array($field, ['created', 'modified', 'updated', 'id'])) {
                        $cantidad++;
                        // si se cumple esta condición, pongo centrada y evito ordenar la columna 
                        if ($field == 'enabled' || strpos($field, "is_") !== false) {
                            echo "<th class='center'><?php echo '" . ucwords(Inflector::humanize($field)) . "' ?></th>\n\t\t";
                        } else {
                            echo "<th><?php echo \$this->Paginator->sort('{$field}', __('" . ucwords(Inflector::humanize($field)) . "')); ?></th>\n\t\t";
                        }
                    }
                endforeach;
                echo '<th class="acciones" style="width:auto">';
                echo "<?php echo __('Acciones'); ?>";
                ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            echo "<?php 
            \$i = 0;
            foreach (\${$pluralVar} as \${$singularVar}): 
                \$class = null;
                if (\$i++ % 2 == 0) {
                    \$class = ' class=\"altrow\"';
                }            
                ?>\n";
            echo "\t\t<tr<?= \$class ?>>\n";
            echo "\t\t<td class=\"borde_tabla\"></td>\n";
            $fieldScripts = [];
            $numColumna = 0; // para q la columna 7 o superior tengan el placement del bootstrap editable en left (sino se va de la pantalla el popup edit)
            foreach ($fields as $field) {
                $numColumna++;
                $isKey = false;
                if (!empty($associations['belongsTo'])) {
                    foreach ($associations['belongsTo'] as $alias => $details) {
                        if ($field === $details['foreignKey']) {
                            $isKey = true;
                            echo "\t<td><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], ['controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}']]); ?></td>\n";
                            break;
                        }
                    }
                }
                if ($isKey !== true) {
                    if ($field == 'enabled' || strpos($field, "is_") !== false) {
                        echo "\t\t\t<td class=\"center\"><?php echo \$this->Html->link(\$this->Html->image(h(\${$singularVar}['{$modelClass}']['{$field}']? '1':'0') . '.png', ['title'=> __('Habilitar / Deshabilitar')]),['controller'=>'{$pluralVar}','action' => 'invertir', '$field', h(\${$singularVar}['{$modelClass}']['id'])], ['class' => 'status', 'escape' => false]); ?></td>\n";
                    } elseif (in_array($field, ['created', 'modified', 'updated', 'id'])) {/*
                      echo "\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n"; */
                    } else {
                        echo "\t\t<td><span class=\"{$field}\"";
                        echo " data-value=\"<?php echo h(\${$singularVar}['{$modelClass}']['{$field}']) ?>\"";
                        echo " data-pk=\"<?php echo h(\${$singularVar}['{$modelClass}']['id']) ?>\">";
                        echo "<?php echo h(\${$singularVar}['{$modelClass}']['{$field}']) ?></span>&nbsp;</td>\n";
                        $fieldScripts[$field] = "\$('.$field').editable({type:'text',name:'$field',success:function(n){if(n){return n}},url:'<?php echo \$this->webroot; ?>$pluralVar/editar',placement:'" . ($numColumna > 7 ? 'left' : 'right') . "'});";
                    }
                }
            }
            echo "\t\t<td class=\"acciones\" style=\"width:auto\">";
            echo "\n\t\t\t<?php 
                  \techo \$this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']]]);
                  \techo \$this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']]]);
                  \techo \$this->Form->postLink(\$this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}']));
                \t?>";
            echo "\n\t\t</td>\n";
            echo "\t\t<td class=\"borde_tabla\"></td>\n";
            echo "\t\t</tr>\n";
            echo "\t<?php endforeach; ?>";
            // para los script del bootstrap editable (1 solo por cada $field), no como antes que cada registro del index tenia su script
            if (count($fieldScripts) > 0) {
                echo "\n\t<script>\n\t$(document).ready(function(){";
                foreach ($fieldScripts as $v) {
                    echo $v;
                }
                echo "\n\t});\n\t</script>";
            }
            echo "
        <tr class=\"altrow\">
            <td class=\"bottom_i\"></td>
            <td colspan=\"$cantidad\"></td>
            <td class=\"bottom_d\"></td>
        </tr>\n";
            ?>
    </table>
<?php echo "<?php echo \$this->element('pagination'); ?>\n"; ?>
</div>