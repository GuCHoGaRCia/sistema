<div class="usersform" id="div_login" style='display:none'>
    <?php
    echo $this->Form->create('User', ['url' => 'login', 'class' => 'jquery-validation', 'id' => 'form']);
    ?>
    <fieldset>
        <?php
        echo $this->Form->input('username', ['label' => false, 'style' => 'margin-top:20px', 'value' => '', 'autocomplete' => 'off', 'placeholder' => __('  Usuario')]);
        echo $this->Form->input('User.password', ['label' => false, 'type' => 'password', 'value' => '', 'autocomplete' => 'off', 'placeholder' => __('  Contraseña')]);
        echo $this->Form->input('token', ['type' => 'hidden', 'value' => '']);
        echo $this->Form->end(['label' => __('Ingresar')]);
        if (strtotime(date("Y-m-d")) >= strtotime(date("Y-12-08")) && strtotime(date("Y-m-d")) <= strtotime(date("Y-12-31"))) {
            // si es navidad
            echo $this->Html->image('navidad.png', ['style' => 'position:relative;width:auto;max-width:95%']);
        }
        if (Configure::read('mantenimiento') == 1) {
            echo "<div style='position:relative;width:100%;border:2px solid red;border-radius:10px;padding:5px;font-weight:bold'>El s&aacute;bado 13/03/2021 desde las 17hs hasta el Lunes 15/03 a las 7hs "
            . "implementaremos actualizaciones en el Sistema, por lo que algunas funcionalidades del mismo pueden verse interrumpidas. ";
            echo "<br>Recuerden verificar las noticias a partir del Lunes para estar al tanto. Muchas gracias!</div>";
        }
        ?>
    </fieldset>
</div>
<div id="loading" style="text-align:center"><?= $this->Html->image('loading.gif', ['width' => '48px', 'height' => '48px']) ?></div>
<center>
    <div class='error' id='errorx' style='width:90%;text-align:center;display:none'>
        Utilice <a href='https://www.google.com/chrome/' target='_blank' rel='nofollow noopener noreferrer'>Google Chrome v50+</a> o <a href='https://www.mozilla.org/es-AR/' target='_blank' rel='nofollow noopener noreferrer'>Mozilla Firefox</a> para ingresar al Sistema
    </div>
</center>
<script>
    var onloadCallback = function () {
        if (typeof $ === "undefined") {
            document.getElementById('errorx').style.display = 'inline-block';
        } else {
<?php
/* chequeo el browser a traves del render engine y feature detect
 * https://stackoverflow.com/questions/9847580/how-to-detect-safari-chrome-ie-firefox-and-opera-browser
 * En iOS !!window.chrome da false, no se porque. Entonces me fijo q no sea edge ni ie
 */
?>
            var isFirefox = typeof InstallTrigger !== 'undefined';
            var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
            var isIE = /*@cc_on!@*/false || !!document.documentMode;
            var isEdge = !isIE && !!window.StyleMedia;
            var isChrome = !isEdge && !isIE && /*!!window.chrome && !!window.chrome.webstore &&*/ getChromeVersion() > 49 && !isOpera;
            var ok = isFirefox || isChrome;<?php /* Se permite solo la version >49 de Chrome, ver https://developer.mozilla.org/en-US/docs/Web/API/FormData/entries (browser compatibility) */ ?>
            if (!ok) {
                $(".error").slideDown();
            } else {
                $("#div_login").css('display', 'inline-block !important');
                $("#div_login").slideDown('normal');
                $("#UserUsername").focus();
            }
            $("#loading").hide();
        }
    };
    function getChromeVersion() {
        var raw = navigator.userAgent.match(/C(hrome|hromium|riOS)\/([0-9]+)\./);
        return raw ? parseInt(raw[2], 10) : false;
    }
    function submit() {
        document.getElementById("form").submit();
    }
    grecaptcha.ready(function () {
        grecaptcha.execute('6LcAemkUAAAAANRp9g9UW-AyUafthNCPXpdENUHq', {action: 'submit'})
                .then(function (token) {
                    $("#UserToken").val(token);
                });
    });
</script>