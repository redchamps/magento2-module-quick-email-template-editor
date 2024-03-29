<?php
namespace RedChamps\QuickEmailTemplateEditor\Plugin\Block\Widget\Button;

use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;

class ToolbarPlugin
{
    /** @var DecoderInterface */
    private $decoder;

    public function __construct(
        DecoderInterface $decoder
    ) {
        $this->decoder = $decoder;
    }

    public function beforePushButtons(
        ToolbarContext $toolbar,
        AbstractBlock  $context,
        ButtonList     $buttonList
    ) {
        if ('template_edit' == $context->getNameInLayout()
            && $returnPath = $context->getRequest()->getParam('return_path')) {
            $buttonList->update(
                'back',
                'on_click',
                sprintf("location.href = '%s';", $context->getUrl(
                    $this->decoder->decode($returnPath)
                ))
            );
        }
        return [$context, $buttonList];
    }
}
