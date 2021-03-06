<?php
declare(strict_types=1);

class ColorModuleTestModuleFrontController extends ModuleFrontController
{
    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {

        parent::initContent();
        $this->context->smarty->assign([
            'bg' => Configuration::hasKey('HEXADECIMAL_COLOR') ? Configuration::get('HEXADECIMAL_COLOR') : '#FFF'
        ]);
        $this->setTemplate('module:colormodule/views/templates/front/test.tpl');
    }

    public function postProcess()
    {
        //postProcess executed before initContent
        if (Tools::isSubmit('form')) {
            return Tools::redirect("URL");
        }
        parent::postProcess(); // TODO: Change the autogenerated stub
    }

    public function setMedia()
    {
        $this->registerStylesheet('theme-main', '/assets/css/theme.css', ['media' => 'all', 'priority' => 50]);
        $this->registerStylesheet('theme-custom', '/assets/css/custom.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('color', 'modules/colormodule/views/css/colorstyle.css');

        if ($this->context->language->is_rtl) {
            $this->registerStylesheet('theme-rtl', '/assets/css/rtl.css', ['media' => 'all', 'priority' => 900]);
        }

        $this->registerJavascript('corejs', '/themes/core.js', ['position' => 'bottom', 'priority' => 0]);
        $this->registerJavascript('theme-main', '/assets/js/theme.js', ['position' => 'bottom', 'priority' => 50]);
        $this->registerJavascript('theme-custom', '/assets/js/custom.js', ['position' => 'bottom', 'priority' => 1000]);

        $assets = $this->context->shop->theme->getPageSpecificAssets($this->php_self);
        if (!empty($assets)) {
            foreach ($assets['css'] as $css) {
                $this->registerStylesheet($css['id'], $css['path'], $css);
            }
            foreach ($assets['js'] as $js) {
                $this->registerJavascript($js['id'], $js['path'], $js);
            }
        }

        // Execute Hook FrontController SetMedia
        Hook::exec('actionFrontControllerSetMedia', []);

        return true;
    }
}