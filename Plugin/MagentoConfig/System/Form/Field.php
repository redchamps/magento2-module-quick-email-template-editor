<?php
declare(strict_types=1);

namespace RedMonks\QuickEmailTemplateEditor\Plugin\MagentoConfig\System\Form;

use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field as BaseField;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\EncoderInterface;

class Field
{
    /** @var UrlInterface */
    private $url;

    /** @var RequestInterface */
    private $request;

    /** @var EncoderInterface */
    private $encoder;

    private $allowedSourceModels;

    public function __construct(
        UrlInterface $url,
        RequestInterface $request,
        EncoderInterface $encoder,
        $allowedSourceModels = []
    ) {
        $this->url = $url;
        $this->request = $request;
        $this->encoder = $encoder;
        $this->allowedSourceModels = $allowedSourceModels;
    }

    public function afterRender(BaseField $subject, $result, $element)
    {
        $originalData = $element->getOriginalData();
        if (isset($originalData['source_model']) && $this->isEmailTemplateSelectBox($originalData) && $element->getValue()) {
            $configPath = $this->encoder->encode(isset($originalData['config_path'])?$originalData['config_path']:$originalData['path']."/".$originalData['id']);
            if (is_numeric($element->getValue())) {
                return $this->appendHtml(__('Edit'), $this->getEditTemplateUrl($element->getValue(), $configPath), $result);
            } else {
                return $this->appendHtml(__('Add'), $this->getNewEmailTemplateUrl($element->getValue(), $configPath), $result);
            }
        }
        return $result;
    }

    private function isEmailTemplateSelectBox($originalData)
    {
        return in_array($originalData['source_model'], $this->allowedSourceModels);
    }

    private function appendHtml($label, $action, $result)
    {
        $result = str_replace('<select ', '<select style="width:85%;" ', $result);
        return str_replace('</select>','</select><a class="action-default " href="'.$action.'">'.$label.'</a>', $result);
    }

    private function getEditTemplateUrl($id, $configPath)
    {
        return $this->url->getUrl(
            'adminhtml/email_template/edit',
            ['id' => $id, 'return_path' => $this->getCurrentPath(), 'config_path' => $configPath]
        );
    }

    private function getNewEmailTemplateUrl($templateId, $configPath)
    {
        return $this->url->getUrl(
            'adminhtml/email_template/new',
            ['template_id' => $templateId, 'return_path' => $this->getCurrentPath(), 'config_path' => $configPath]
        );
    }

    private function getCurrentPath()
    {
        $params = $this->request->getParams();
        if (isset($params[UrlInterface::SECRET_KEY_PARAM_NAME])) {
            unset($params[UrlInterface::SECRET_KEY_PARAM_NAME]);
        }
        $url = $this->request->getRouteName().'/'.$this->request->getControllerName().'/'.$this->request->getActionName().$this->makeParamString($params);
        return $this->encoder->encode($url);
    }

    private function makeParamString($params)
    {
        $string = '';
        foreach ($params as $key => $value) {
            $string .= '/'.$key .'/'.$value;
        }
        return $string;
    }
}
