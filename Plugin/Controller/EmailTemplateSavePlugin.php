<?php
namespace RedMonks\QuickEmailTemplateEditor\Plugin\Controller;

use Magento\Email\Controller\Adminhtml\Email\Template\Save;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Url\DecoderInterface;
use RedMonks\QuickEmailTemplateEditor\Registry\LastSavedTemplate;

class EmailTemplateSavePlugin
{
    private $lastSavedTemplate;

    private $decoder;
    private $configWriter;
    private $cacheTypeList;

    public function __construct(
        LastSavedTemplate $lastSavedTemplate,
        DecoderInterface $decoder,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList
    ) {
        $this->lastSavedTemplate = $lastSavedTemplate;
        $this->decoder = $decoder;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
    }

    public function afterExecute(Save $subject, $result)
    {
        $configPath = $subject->getRequest()->getParam('config_path');
        if ($configPath && $subject->getRequest()->getParam('template_id')) {
            $this->configWriter->save(
               $this->decoder->decode($configPath),
               $this->lastSavedTemplate->get()->getId()
            );
            $this->cacheTypeList->cleanType('config');
        }
        if($subject->getRequest()->getParam('return_path')) {
            $subject->getResponse()->setRedirect(
                $subject->getUrl($this->decoder->decode($subject->getRequest()->getParam('return_path')))
            );
        }
        return $result;
    }
}
