<?php

namespace RedMonks\QuickEmailTemplateEditor\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EmailTemplate implements ArgumentInterface
{
    /** @var RequestInterface */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    public function isNewTemplate()
    {
        if (strstr($this->request->getUri()->getPath(), '/new/')
            && $this->request->getParam('template_id')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getTemplateId()
    {
        return $this->request->getParam('template_id');
    }
}
