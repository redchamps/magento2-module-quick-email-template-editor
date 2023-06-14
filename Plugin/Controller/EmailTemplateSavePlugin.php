<?php
namespace RedChamps\QuickEmailTemplateEditor\Plugin\Controller;

use Magento\Email\Controller\Adminhtml\Email\Template\Save;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Store\Model\ScopeInterface;
use RedChamps\QuickEmailTemplateEditor\Registry\LastSavedTemplate;

class EmailTemplateSavePlugin
{
    private $decoder;

    private $configWriter;

    private $appConfig;

    private $lastSavedTemplate;

    public function __construct(
        LastSavedTemplate $lastSavedTemplate,
        DecoderInterface $decoder,
        WriterInterface $configWriter,
        ReinitableConfigInterface $appConfig
    ) {
        $this->lastSavedTemplate = $lastSavedTemplate;
        $this->decoder = $decoder;
        $this->configWriter = $configWriter;
        $this->appConfig = $appConfig;
    }

    public function afterExecute(Save $subject, $result)
    {
        if ($this->lastSavedTemplate->get()) {
            $this->savConfig($subject);
            $this->setRedirectPath($subject);
        }
        return $result;
    }

    protected function savConfig($subject)
    {
        $request = $subject->getRequest();
        $configPath = $request->getParam('config_path');
        if ($configPath && $request->getParam('template_id')) {
            $scope = $this->getScope($request);
            $this->configWriter->save(
                $this->decoder->decode($configPath),
                $this->lastSavedTemplate->get()->getId(),
                $scope['type'],
                $scope['scopeId']
            );
            $this->appConfig->reinit();
        }
    }

    protected function setRedirectPath($subject)
    {
        if($subject->getRequest()->getParam('return_path')) {
            $subject->getResponse()->setRedirect(
                $subject->getUrl($this->decoder->decode($subject->getRequest()->getParam('return_path')))
            );
        }
    }

    protected function getScope($request)
    {
        if ($website = $request->getParam('website')) {
            return ['type' => ScopeInterface::SCOPE_WEBSITES, 'scopeId' => $website];
        } elseif ($store = $request->getParam('store')) {
            return ['type' => ScopeInterface::SCOPE_STORES, 'scopeId' => $store];
        } else {
            return ['type' => ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 'scopeId' => 0];
        }
    }
}
