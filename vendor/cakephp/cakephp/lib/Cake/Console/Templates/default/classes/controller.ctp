<?php
/**
  Modificado por Esteban
 */
echo "<?php\n";
echo "App::uses('{$plugin}AppController', '{$pluginPath}Controller');\n";
?>
class <?php echo $controllerName; ?>Controller extends <?php echo $plugin; ?>AppController {

<?php if ($isScaffold): ?>
    public $scaffold;

<?php
else:

    if (count($helpers)):
        //echo "/**\n * Helpers\n *\n * @var array\n */\n";
        echo "\tpublic \$helpers = [";
        for ($i = 0, $len = count($helpers); $i < $len; $i++):
            if ($i != $len - 1):
                echo "'" . Inflector::camelize($helpers[$i]) . "', ";
            else:
                echo "'" . Inflector::camelize($helpers[$i]) . "'";
            endif;
        endfor;
        echo "];\n\n";
    endif;

    //if (count($components)):
    //	echo "/**\n * Components\n *\n * @var array\n */\n";
    //	echo "\tpublic \$components = array(";
    //	for ($i = 0, $len = count($components); $i < $len; $i++):
    //		if ($i != $len - 1):
    //			echo "'" . Inflector::camelize($components[$i]) . "', ";
    //		else:
    //			echo "'" . Inflector::camelize($components[$i]) . "'";
    //		endif;
    //	endfor;
    //	echo ");\n\n";
    //endif;*/

    if (!empty($actions)) {
        echo trim($actions) . "\n";
    }

endif;
?>
}
