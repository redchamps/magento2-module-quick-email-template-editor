<?php

namespace RedMonks\QuickEmailTemplateEditor\ViewModel;

use Magento\Email\Model\Template\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EmailTemplate implements ArgumentInterface
{
    /** @var RequestInterface */
    private $request;

    private $emailConfig;

    public function __construct(
        RequestInterface $request,
        Config $emailConfig
    ) {
        $this->request = $request;
        $this->emailConfig = $emailConfig;
    }

    public function isNewTemplate()
    {
        return strstr($this->request->getUri()->getPath(), '/new/')
            && $this->request->getParam('template_id');
    }

    public function getTemplateId()
    {
        return $this->request->getParam('template_id');
    }

    public function getTemplateName()
    {
        return $this->emailConfig->getTemplateLabel($this->getTemplateId())." [Edited]";
    }
}
