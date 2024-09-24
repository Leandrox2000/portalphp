<?php

namespace Pagination;

use Pagerfanta\View\Template\TwitterBootstrapTemplate;

class CustomTwitterBootstrapTemplate extends TwitterBootstrapTemplate
{
    static protected $defaultOptions = array(
        'prev_message'        => '&larr; Previous',
        'next_message'        => 'Next &rarr;',
        'dots_message'        => '&hellip;',
        'active_suffix'       => '',
        'css_container_class' => 'pagination',
        'css_prev_class'      => 'prev',
        'css_next_class'      => 'next',
        'css_disabled_class'  => 'disabled hidden',
        'css_dots_class'      => 'disabled',
        'css_active_class'    => 'active'
    );

    public function container()
    {
        return sprintf('<div class="%s"><ul class="pagination">%%pages%%</ul></div>',
            $this->option('css_container_class')
        );
    }

}
