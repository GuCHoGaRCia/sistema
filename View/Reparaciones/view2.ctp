<div class="reparaciones view">
    <h2><?php echo __('Reparaciones'); ?></h2>
    <fieldset>
        <b><?php echo __('Recordatorio'); ?>: </b>
        <?php echo h($reparacione['Reparacione']['recordatorio']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Concepto'); ?>: </b>
        <?php echo h($reparacione['Reparacione']['concepto']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Estado'); ?>: </b>
        <?php echo h($reparacione['Reparacionesestado']['nombre']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Observaciones'); ?>: </b>
        <?php echo $reparacione['Reparacione']['observaciones']; ?>
        &nbsp;
        <br>
        <b><?php echo __('Creado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $reparacione['Reparacione']['created']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Modificado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $reparacione['Reparacione']['modified']); ?>
        &nbsp;
        <br>
    </fieldset>
</div>
<script>
    $(document).on('click', '.propimgdel', function () {
        var img = $(this);
        img.css('border', '4px solid red');
        if (confirm('<?= __("Desea eliminar la imagen seleccionada?") ?>')) {
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Reparaciones/delImagen", cache: false, data: {id: $(this).attr('id')}}).done(function (msg) {
                if (msg === "true") {
                    img.fadeOut(800, function () {
                        img.remove(); // borro la imagen del html
                    });
                } else {
                    alert('<?= __("El dato no pudo ser eliminado") ?>');
                    img.css("border", "0");
                }
            });
        } else {
            img.css("border", "0");
        }
    });
</script>
<script src="/sistema/js/slider.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function ($) {
        var jssor_1_options = {
            $AutoPlay: 1,
            $SlideshowOptions: {
                $Class: $JssorSlideshowRunner$,
                $TransitionsOrder: 1
            },
            $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$
            },
            $ThumbnailNavigatorOptions: {
                $Class: $JssorThumbnailNavigator$,
                $Cols: 10,
                $SpacingX: 8,
                $SpacingY: 8,
                $Align: 360
            }
        };

        var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);
        function ScaleSlider() {
            var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
            if (refSize) {
                refSize = Math.min(refSize, 800);
                jssor_1_slider.$ScaleWidth(refSize);
            } else {
                window.setTimeout(ScaleSlider, 30);
            }
        }
        ScaleSlider();
        $(window).bind("load", ScaleSlider);
        $(window).bind("resize", ScaleSlider);
        $(window).bind("orientationchange", ScaleSlider);
    });
</script>
<style>
    .jssora05l, .jssora05r {
        display: block;
        position: absolute;
        width: 40px;
        height: 40px;
        cursor: pointer;
        background: url('/sistema/img/a17.png') no-repeat;
        overflow: hidden;
    }
    .jssora05l { background-position: -10px -40px; }
    .jssora05r { background-position: -70px -40px; }
    .jssora05l:hover { background-position: -130px -40px; }
    .jssora05r:hover { background-position: -190px -40px; }
    .jssora05l.jssora05ldn { background-position: -250px -40px; }
    .jssora05r.jssora05rdn { background-position: -310px -40px; }
    .jssora05l.jssora05lds { background-position: -10px -40px; opacity: .3; pointer-events: none; }
    .jssora05r.jssora05rds { background-position: -70px -40px; opacity: .3; pointer-events: none; }
    .jssort01 .p {    position: absolute;    top: 0;    left: 0;    width: 72px;    height: 72px;}.jssort01 .t {    position: absolute;    top: 0;    left: 0;    width: 100%;    height: 100%;    border: none;}.jssort01 .w {    position: absolute;    top: 0px;    left: 0px;    width: 100%;    height: 100%;}.jssort01 .c {    position: absolute;    top: 0px;    left: 0px;    width: 68px;    height: 68px;    border: #000 2px solid;    box-sizing: content-box;    background: url('/sistema/img/t01.png') -800px -800px no-repeat;    _background: none;}.jssort01 .pav .c {    top: 2px;    _top: 0px;    left: 2px;    _left: 0px;    width: 68px;    height: 68px;    border: #000 0px solid;    _border: #fff 2px solid;    background-position: 50% 50%;}.jssort01 .p:hover .c {    top: 0px;    left: 0px;    width: 70px;    height: 70px;    border: #fff 1px solid;    background-position: 50% 50%;}.jssort01 .p.pdn .c {    background-position: 50% 50%;    width: 68px;    height: 68px;    border: #000 2px solid;}* html .jssort01 .c, * html .jssort01 .pdn .c, * html .jssort01 .pav .c {    /* ie quirks mode adjust */    width /**/: 72px;    height /**/: 72px;}
</style>
<div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:800px;height:456px;overflow:hidden;visibility:hidden;background-color:#24262e;">
    <div data-u="loading" style="position:absolute;top:0px;left:0px;background:url('/sistema/img/loading.gif') no-repeat 50% 50%;background-color:rgba(0, 0, 0, 0.7);"></div>
    <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:800px;height:356px;overflow:hidden;">
        <?php
        $dir = /* $this->webroot . */ '/files' . "/" . $_SESSION['Auth']['User']['client_id'] . "/rep/";
        if (count($reparacione['Reparacionesadjunto']) > 0) {
            foreach ($reparacione['Reparacionesadjunto'] as $k => $v) {
                ?>
                <div>
                    <img data-u="image" src="<?= $this->webroot . $dir . basename($v['ruta']) ?>" />
                    <img data-u="thumb" src="<?= $this->webroot . $dir . basename($v['ruta']) ?>" />
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div data-u="thumbnavigator" class="jssort01" style="position:absolute;left:0px;bottom:0px;width:800px;height:100px;" data-autocenter="1">
        <div data-u="slides" style="cursor: default;">
            <div data-u="prototype" class="p">
                <div class="w">
                    <div data-u="thumbnailtemplate" class="t"></div>
                </div>
                <div class="c"></div>
            </div>
        </div>
    </div>
    <span data-u="arrowleft" class="jssora05l" style="top:158px;left:8px;width:40px;height:40px;"></span>
    <span data-u="arrowright" class="jssora05r" style="top:158px;right:8px;width:40px;height:40px;"></span>
</div>

<?php
echo '<br>' . $this->Html->link(__('Volver'), array('action' => 'index'));
