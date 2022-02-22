<div class="contasientos form">
    <h2><?php echo __('Configurar Asientos'); ?></h2>
    <?php
    $selected = isset($this->request->data['Contasiento']['consorcio_id']) ? $this->request->data['Contasiento']['consorcio_id'] : 0;
    echo "<div class='inline'>";
    echo $this->Form->create('Contasiento', ['class' => 'inline']);
    echo $this->Form->input('consorcio_id', ['label' => false, 'div' => false, 'options' => $consorcios, 'type' => 'select', 'selected' => $selected, 'empty' => '']);
    echo "&nbsp;&nbsp;<img src='" . $this->webroot . "img/loading.gif' style='display:none;width:30px' id='loading'/>";
    echo "</div>";
    echo "<div id='config'></div>";
    ?>
    <script>
        $(document).ready(function () {
            $("#ContasientoConsorcioId").select2({language: "es", placeholder: "Seleccione Consorcio..."});
        });
        $("#ContasientoConsorcioId").on("select2:select", function (e) {
            $("#loading").show();
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Contasientos/config", cache: false, data: {consorcio_id: $(this).val()}
            }).done(function (msg) {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                } else {
                    $("#config").html(obj.d);
                }
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la accion, intente nuevamente");
                }
            });
            $("#loading").hide();
        });
    </script>
    <?php
    if (isset($config)) {
        echo $this->Form->create('Contasiento', ['class' => 'jquery-validation', 'multiple' => 'multiple']);
        ?>
        <fieldset>
            <?php
            ?>
        </fieldset>
        <fieldset>
            <?php
            ?>
        </fieldset>
        <?php echo $this->Form->end(['label' => __('Guardar'), 'class' => 'detalle', 'id' => 'guardar']); ?>
        <script>
            $("#ContasientoEditForm").submit(function (e) {
                e.preventDefault();

                if ($("#ContasientoDescripcion").val() === "") {
                    alert("Debe ingresar una descripción");
                    e.preventDefault();
                    return false;
                }

                $("#guardar").prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "<?= $this->webroot ?>Contasientos/config",
                    data: $("#ContasientoEditForm").serialize(),
                }).done(function (msg) {
                    if (msg === "") {
                        alert("La Configuración fue guardada correctamente");
                    } else {
                        alert(msg);
                    }
                }).fail(function (j, a) {
                    if (j.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion, intente nuevamente");
                    }
                });
                $("#guardar").prop('disabled', false);

                return false;
            });
        </script>
        <?php
    }
    ?>
</div>