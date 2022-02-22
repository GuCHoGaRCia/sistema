<div class='toolbar' style="<?= isset($style) ? h($style) : '' ?>">
    <div class='pag'>
        <?php
        // permito la cuenta de la cantidad de paginas (y las flechitas para cambiar de pagina) O el filtro, no ambos simultáneamente
        if (isset($pagecount) && $pagecount) {
            // navegador de paginas
            if ($this->Paginator->hasPrev()) {
                echo $this->Paginator->prev(__('<< '), [], null, ['class' => 'prev disabled']);
            }
            if ($this->Paginator->hasNext()) {
                echo $this->Paginator->next(__(' >>'), [], null, ['class' => 'next disabled']);
            }
            echo "&nbsp;&nbsp;&nbsp;"; // separador
            // contador
            echo $this->Paginator->counter([
                //'format' => html_entity_decode(__('P&aacute;gina {:page} de {:pages}, mostrando {:current} registros de un total de {:count}, del registro {:start} al {:end}'))
                'format' => html_entity_decode(__('P&aacute;g {:page}/{:pages}, {:count} registros'))
            ]);
        } else {
            // permite filtrar el listado actual por algún campo:
            if (isset($filter['enabled']) && $filter['enabled']) {
                echo $this->Form->create('filter');
                $selected = $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']] ?? $this->request->data['filter'][$filter['field']] ?? '';
                echo $this->Form->input($filter['field'], ['label' => false, 'empty' => '', 'options' => $filter['options'], 'type' => 'select', 'selected' => $selected]);
                echo $this->Form->end(); // ucfirst($this->request->params['action']) es porq a veces uso el filter en otra action que no sea index, sino no anda. Ej:reparaciones/anuladas , Avisosblacklist/panel_index
                $form = isset($filter['panel']) && $filter['panel'] === true ? '"#filter' . str_replace("_", "", ucwords($this->request->params['action'], "_")) . 'Form"' : '"#filter' . ucfirst($this->request->params['action']) . 'Form"';
                ?>
                <script>
                    $(function () {
                        $("#<?= "filter" . h(ucfirst($filter['field'])) ?>").select2({language: "es", placeholder: '<?= 'Seleccione ' . h($filter['field']) . "..." ?>', allowClear: true, width: "300"});
                        $("#<?= "filter" . h(ucfirst($filter['field'])) ?>").change(function () {
                            $(<?= $form ?>).submit();
                        });
                    });
                </script>
                <?php
            }
        }
        ?>
    </div>
    <div class='busc'>
        <?php
        if (isset($pagesearch) && $pagesearch) {
            echo $this->Form->create($model, ['id' => 'busqform']);
            echo $this->Form->input('buscar', ['id' => 'busq', 'div' => false, 'value' => isset($this->request->params['named']['buscar']) ? str_replace('-', '/', trim($this->request->params['named']['buscar'])) : '', 'required' => 'false', 'label' => '', 'maxlength' => 40, 'style' => 'width:160px;font-family:Courier', /* 'pattern' => '[\d\w\s]', 'title' => 'Caracteres permitidos: letras, numeros, punto, @, \' o espacios' */]);
            echo $this->Form->submit('search.png', ['div' => false, 'class' => 'searchimg', 'alt' => 'Buscar', 'title' => 'Buscar', 'onsubmit' => 'busq()']);
            echo $this->Form->end();
            // reemplazo las / por - y acomodo el formato de la fecha
            ?>
            <script>
                $("#busqform").submit(function (e) {
                    $("#busq").val($("#busq").val().replace(/\//g, '-'));
                });
            </script>
            <?php
        }
        ?>
    </div>
    <div class='botones'>
        <div class='inline'>
            <?php
            if (isset($pagenew) && $pagenew) {
                echo $this->Html->image('new.png', ['title' => 'Agregar', 'url' => ['action' => isset($taction) ? $taction : 'add'], 'class' => 'imgmove']);
            }
            if (isset($print) && $print) {
                echo "&nbsp;" . $this->Html->image('print2.png', ['title' => 'Imprimir', 'id' => 'print']);
            }
            if (isset($export) && $export) {
                echo "&nbsp;" . $this->Html->link($this->Html->image('exportcsv.png', ['title' => 'Exportar a CSV']), ['action' => 'exportar'], ['escapeTitle' => false, 'class' => 'imgmove']);
            }
            if (isset($multidelete) && $multidelete) {
                echo "&nbsp;" . $this->Html->image('deleteall.png', ['title' => 'Eliminar múltiples registros', 'id' => 'multidelete', 'style' => 'display:none;cursor:pointer', 'onclick' => 'multidelete()']);
                ?>
                <script>
                    var cont = 0;
                    function mdtoggle() {
                        var tildar = true;
                        var todostildados = true;
                        var todosdestildados = true;
                        $("input[class^='til_']").each(function () {
                            if (tildar) {
                                if (!$(this).is(':checked')) {
                                    todostildados = false;
                                    return false;
                                }
                            } else {
                                if ($(this).is(':checked')) {
                                    todosdestildados = false;
                                    return false;
                                }
                            }
                        });

                        tildar = (tildar && todostildados) || (!tildar && todosdestildados) ? !tildar : tildar;
                        $("input[class^='til_']").each(function () {

    <?php /* Si la accion es tildar y estan todos tildados ó si la accion es destildar y estan todos destildados realizo la accion inversa (!tildar) sino queda la opcion tildar y tendrian que hacer 2 clicks */ ?>
                            if (tildar) {
                                if (!$(this).is(':checked')) {
                                    cont++;
                                    $(this).prop('checked', true);
                                }
                            } else {
                                if ($(this).is(':checked')) {
                                    cont--;
                                    $(this).prop('checked', false);
                                }
                            }
                        });
                        hs();
                        tildar = !tildar;
                    }
                    $(document).on('click', "input[class^='til_']", function () {
                        if ($(this).is(':checked')) {
                            cont++;
                        } else {
                            cont--;
                        }
                        hs();
                    });

                    function hs() {
                        if (cont > 1) {
                            $("#multidelete").show();
                        } else {
                            $("#multidelete").hide();
                        }
                    }
                    function multidelete() {
                        var ids = [];
                        $("input[class^='til_']").each(function () {
                            if ($(this).is(':checked')) {
                                var strid = $(this).prop('class');
                                var id = strid.replace('til_', '');
                                ids.push(id);
                            }
                        });
                        if (ids.length > 0) {
                            if (confirm('Desea eliminar los registros seleccionados?')) {
                                $("#multidelete").prop('src', '<?= $this->webroot . 'img/loading.gif' ?>');
                                $.ajax({
                                    type: "POST",
                                    url: "<?= $this->webroot . (isset($this->request->params['panel']) ? 'panel/' : '') . $this->request->params['controller'] ?>/borrarMultiple",
                                    data: {ids: ids}
                                }).done(function (msg) {
                                    window.location.replace("<?= $this->webroot . (isset($this->request->params['panel']) ? 'panel/' : '') . $this->request->params['controller'] . "/" . str_replace('panel_', '', $this->request->params['action']) ?>");
                                }).fail(function (jqXHR, t) {
                                    if (jqXHR.status === 403) {
                                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                                    } else {
                                        alert("No se pudo realizar la accion, intente nuevamente");
                                    }
                                    $("#multidelete").prop('src', '<?= $this->webroot . 'img/multidelete.png' ?>');
                                });
                            }
                        }
                    }
                </script>
                <?php
            }
            ?>
        </div>
    </div>
</div>
