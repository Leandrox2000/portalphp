<?php

$path = include getcwd()."/config/paths.php";;

/**
 *
 * Configuração dos Templates
 */
return array(
        'base_link'     => 'http://'.$_SERVER['HTTP_HOST'].$path['base_path'],
        'js_path'       => getcwd().'/templates/amanda/assets/js',
        'js_link'       => 'http://'.$_SERVER['HTTP_HOST'].'/'.$path['base_path'].'templates/amanda/assets/js',
        'css_path'      => getcwd().'/templates/amanda/assets/css',
        'css_link'      => 'http://'.$_SERVER['HTTP_HOST'].'/'.$path['base_path'].'templates/amanda/assets/css',
        'view_path'     => getcwd().'/templates/amanda/view',
        'js'            => array(
                "/plugins/jquery-1.8.3.min.js",
                "/plugins/jquery-ui-1.10.4.custom.min.js",
                "/plugins/jquery.ui.datepicker-pt-BR.js",
                "/plugins/jquery.inputmask.min.js",
                "/plugins/php.js/functions.js",
                "/plugins/jquery.dataTables.js",
                "/plugins/jquery.uniform.min.js",
                "/plugins/jquery.jgrowl.js",
                "/plugins/jquery.Jcrop.min.js",
                "/plugins/jquery.alerts.js",
                "/plugins/chosen.jquery.min.js",
                "/plugins/jquery.tagsinput.min.js",
                "/plugins/validate/jquery.validate.js",
                "/plugins/validate/additional-methods.js",
                "/plugins/validate/messages_ptbr.js",
                "/plugins/jquery.colorbox-min.js",
                "/plugins/jquery.ui.tooltip.js",
                "/plugins/jquery.maskedinput-1.3.1.js",
                "/plugins/easyTooltip.js",
                "/plugins/jquery.form.js",
                "/../../../../vendor/jointi/bibliotecas-externas/ckeditor/ckeditor.js",
                "/../../../../vendor/jointi/bibliotecas-externas/ckeditor/lang/pt-br.js",
                "/plugins/jquery.smartWizard-2.0.min.js",
                "/custom/general.js",
                "/custom/validators.js",
                "/imagem/imagens.js",
        ),
        'css'           => array(
                "/style.default.css",
                "/plugins/jquery.ui.css",
                "/plugins/jquery.ui.autocomplete.css",
                "/plugins/uniform.tp.css",
                "/plugins/jquery.jgrowl.css",
                "/plugins/jquery.Jcrop.css",
                "/plugins/jquery.alerts.css",
                "/plugins/jquery.chosen.css",
                "/plugins/jquery.tagsinput.css",
                "/plugins/colorbox.css",
                "/plugins/ui.spinner.css",
                "/glyphicons.css",
                "/imagem/imagens.css",
        ),
);