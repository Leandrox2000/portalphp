<?php

namespace Pagination;

use Pagerfanta\View\TwitterBootstrapView;

class CustomTwitterBoostrapView extends TwitterBootstrapView
{
    protected function createDefaultTemplate()
    {
        return new CustomTwitterBootstrapTemplate();
    }
}
