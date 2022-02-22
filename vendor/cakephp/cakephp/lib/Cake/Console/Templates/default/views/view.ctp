<?php
/**
 * Modificado por Esteban
 */
?>
<div class="<?php echo $pluralVar; ?> view">
    <h2><?php echo "<?php echo __('{$singularHumanName}'); ?>"; ?></h2>
    <fieldset>
        <?php
        foreach ($fields as $field) {
            $isKey = false;
            /* if (!empty($associations['belongsTo'])) {
              foreach ($associations['belongsTo'] as $alias => $details) {
              if ($field === $details['foreignKey']) {
              $isKey = true;
              echo "\t\t<dt><?php echo __('" . Inflector::humanize(Inflector::underscore($alias)) . "'); ?></dt>\n";
              echo "\t\t<dd>\n\t\t\t<?php echo ${$singularVar}['{$alias}']['{$details['displayField']}']; ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
              break;
              }
              }
              } */
            if ($isKey !== true) {
                echo "\t\t<b><?php echo __('" . Inflector::humanize($field) . "'); ?>: </b>\n";
                if (in_array($field, ['created', 'modified'])) {
                    echo "\t\t<?php echo \$this->Time->format(__('d/m/Y H:i:s'), \${$singularVar}['{$modelClass}']['{$field}']); ?>\n\t\t\t&nbsp;\n\t\t<br>\n";
                } else {
                    echo "\t\t<?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>\n\t\t\t&nbsp;\n\t\t<br>\n";
                }
            }
        }
        ?>
    </fieldset>
</div>
<?php echo "<?php echo '<br>' . \$this->Html->link(__('Volver'), ['action' => 'index']); ?>"; ?>