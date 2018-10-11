<?php

namespace Snap\Templating;

interface Templating_Interface
{
    public function render($slug, $data = []);
    
    public function partial($slug, $data = []);

    public function get_current_view();
}