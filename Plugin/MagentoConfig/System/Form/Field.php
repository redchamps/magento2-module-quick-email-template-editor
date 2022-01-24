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
        if (isset($originalData['source_model'])
            && in_array($originalData['source_model'], $this->allowedSourceModels)
            && $element->getValue()) {
            return $this->appendHtml($result, $element);
        }
        return $result;
    }

    private function appendHtml($result, $element)
    {
        $originalData = $element->getOriginalData();
        $configPath = $this->encoder->encode(isset($originalData['config_path'])?$originalData['config_path']:$originalData['path']."/".$originalData['id']);
        $result = str_replace('<select ', '<select style="width:69%;" ', $result);
        return str_replace(
            '</select>',
            '</select><a class="action-default " href="'.$this->getEditTemplateUrl($element->getValue(), $configPath).'">'.__('Edit Template').'</a>',
            $result
        );
    }

    private function getEditTemplateUrl($templateId, $configPath)
    {
        $path = is_numeric($templateId)?'adminhtml/email_template/edit':'adminhtml/email_template/new';
        $identifier = is_numeric($templateId)?'id':'template_id';
        return $this->url->getUrl(
            $path.$this->makeParamString(),
            [$identifier => $templateId, 'return_path' => $this->getCurrentPath(), 'config_path' => $configPath]
        );
    }

    private function getCurrentPath()
    {
        $params = $this->request->getParams();
        if (isset($params[UrlInterface::SECRET_KEY_PARAM_NAME])) {
            unset($params[UrlInterface::SECRET_KEY_PARAM_NAME]);
        }
        $url = implode(
            '/',
            [$this->request->getRouteName(), $this->request->getControllerName(), $this->request->getActionName()]
        );
        return $this->encoder->encode($url.$this->makeParamString());
    }

    private function makeParamString()
    {
        $params = $this->request->getParams();
        if (isset($params[UrlInterface::SECRET_KEY_PARAM_NAME])) {
            unset($params[UrlInterface::SECRET_KEY_PARAM_NAME]);
        }
        $string = '';
        foreach ($params as $key => $value) {
            $string .= '/'.$key .'/'.$value;
        }
        return $string;
    }
}
