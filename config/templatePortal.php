<?php

$path = include BASE_PATH . "config/paths.php";;

/**
 *
 * Configuração dos Templates
 */

return array(
    'base_link'     => 'http://'.$_SERVER['HTTP_HOST'] . $path['base_path'],
    'assets_link'     => 'http://'.$_SERVER['HTTP_HOST'] . $path['base_path'].'templates/portal/assets',
    'js_path'       => getcwd().'/templates/portal/assets/js',
    'js_link'       => 'http://'.$_SERVER['HTTP_HOST'] . $path['base_path'].'templates/portal/assets/js',
    'css_path'      => getcwd().'/templates/portal/assets/css',
    'css_link'      => 'http://'.$_SERVER['HTTP_HOST'] . $path['base_path'].'templates/portal/assets/css',
    'view_path'     => getcwd().'/templates/portal/view',
    'js'            => array(
        '/jquery.min.js',
        '/jquery.validate.min.js',
        '/bootstrap.js',
        '/jquery.dropdownPlain.js',
        '/jquery.cycle2.min.js',
        '/jquery.cycle2.center.js',
        '/jquery.cycle2.carousel.min.js',
        '/jquery-ui/jquery-ui.min.js',
        '/textresize/store.min.js',
        '/textresize/rv-jquery-fontsize.min.js',
        '/jquery.truncator.js',
        '/jquery.bxslider.min.js',
        '/jquery.mask.js',
        '/default.js',
    ),
    'css'          => array(
        '/bootstrap.css',
        '/template.css',
        '/style.css',
        '/contrast.css',
        '/jquery.bxslider.css',
    ),
);