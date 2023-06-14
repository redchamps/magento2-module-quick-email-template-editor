<?php
namespace RedChamps\QuickEmailTemplateEditor\Plugin\Model;

use Magento\Email\Model\Template;
use RedChamps\QuickEmailTemplateEditor\Registry\LastSavedTemplate;

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
