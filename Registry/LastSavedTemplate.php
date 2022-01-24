<?php
namespace RedMonks\QuickEmailTemplateEditor\Registry;

class LastSavedTemplate
{
    protected $template;

    public function set($template)
    {
        $this->template = $template;
    }

    public function get()
    {
        return $this->template;
    }
}
