<?php
declare(strict_types=1);

namespace RedMonks\QuickEmailTemplateEditor\Plugin\MagentoConfig\System\Form;

use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field as BaseField;

class Field
{
    /** @var UrlInterface */
    private $url;

    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    public function afterRender(BaseField $subject, $result, $element)
    {
        $originalData = $element->getOriginalData();
        if ( isset($originalData['source_model'])
            && $originalData['source_model'] == 'Magento\Config\Model\Config\Source\Email\Template'
            && $element->getValue()
        ) {
            if (is_numeric($element->getValue())) {
                return $this->appendHtml(__('Edit'), $this->getEditTemplateUrl($element->getValue()), $result);
            } else {
                return $this->appendHtml(__('Add'), $this->getNewEmailTemplateUrl($element->getValue()), $result);
            }
        }
        return $result;
    }

    private function appendHtml($label, $action, $result)
    {
        $result = str_replace('<select ', '<select style="width:85%;" ', $result);
        return str_replace('</select>','</select><a class="action-default " href="'.$action.'" target="_blank">'.$label.'</a>', $result);
    }

    private function getEditTemplateUrl($id)
    {
        return $this->url->getUrl('adminhtml/email_template/edit',['id' => $id]);
    }

    private function getNewEmailTemplateUrl($templateId)
    {
        return $this->url->getUrl('adminhtml/email_template/new',['template_id' => $templateId]);
    }
}
