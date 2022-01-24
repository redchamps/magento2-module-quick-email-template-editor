<?php
namespace RedMonks\QuickEmailTemplateEditor\Plugin\Model;

use Magento\Email\Model\Template;
use RedMonks\QuickEmailTemplateEditor\Registry\LastSavedTemplate;

class TemplatePlugin
{
    private $lastSavedTemplate;

    public function __construct(LastSavedTemplate $lastSavedTemplate)
    {
        $this->lastSavedTemplate = $lastSavedTemplate;
    }

    public function afterAfterSave(Template $subject, $result)
    {
        $this->lastSavedTemplate->set($result);
        return $result;

    }
}
