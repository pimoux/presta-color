<?php

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ColorModule extends Module implements WidgetInterface
{
    /**
     * ColorModule constructor.
     */
    public function __construct()
    {
        $this->name = 'colormodule';
        $this->tab = 'front_office_features';
        $this->version = '1.1.7';
        $this->author = 'Luka Vouillemont';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('color test module');
        $this->description = $this->l('This is my color test module');

        $this->confirmUninstall = $this->l('No... Don\'t do that please :(');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('registerGDPRConsent') &&
            $this->registerHook('moduleRoutes');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

//    public function hookdisplayFooter($params): string
//    {
//        $this->context->smarty->assign([
//            'catname' => 'pandore',
//            'cartId' => $this->context->cart->id
//        ]);
//        return $this->display(__FILE__, 'views/templates/hook/footer.tpl');
//    }

    public function renderWidget($hookName, array $configuration)
    {
        if ($hookName === 'displayNavFullWidth') {
            return 'displayNavFullWidthException';
        }
        $this->context->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch('module:colormodule/views/templates/hook/footer.tpl', $this->getCacheId('blockreassurance'));
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        return [
            'catName' => 'Pandore'
        ];
    }

//    public function getContent()
//    {
//        $message = null;
//
//        if(Tools::getValue('courseRating')) {
//            Configuration::updateValue('COURSE_RATING', Tools::getValue('courseRating'));
//            $message = 'Form saved correctly';
//        }
//
//        $courseRating = Configuration::get('COURSE_RATING');
//        $this->context->smarty->assign([
//            'courseRating' => $courseRating,
//            'message' => $message
//        ]);
//        return $this->fetch('module:colormodule/views/templates/admin/configuration.tpl');
//    }

    public function getContent(): string
    {
        $response = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $hexaRegex = '/#([a-f0-9]{3}){1,2}\b/i';
            $color = Tools::getValue('color');
            if (!preg_match($hexaRegex, $color)) {
                $response .= $this->displayError($this->trans('The value is not a hexadecimal color'));
            } else if ($color && Validate::isGenericName($color)) {
                Configuration::updateValue('HEXADECIMAL_COLOR', Tools::getValue('color'));
                $response .= $this->displayConfirmation($this->trans('The color has been submitted successfully'));
            } else {
                $response .= $this->displayError($this->trans('An error occurred'));
            }
        }

        return $response . $this->displayForm();
    }

    public function displayForm(): string
    {
        $defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields[0]['form'] = [
            'legend' => [
                'title' => $this->trans('Color settings')
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Hexadecimal color'),
                    'name' => 'color',
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->trans('Save the color'),
                'class' => 'btn btn-primary pull-right'
            ]
        ];

        //instance of the HF
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        //Language
        $helper->default_form_language = $defaultLanguage;
        $helper->allow_employee_form_lang = $defaultLanguage;

        //title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
            ],
            'back' => [
                'desc' => $this->l('Back to List'),
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules')
            ]
        ];
        $helper->fields_value['color'] = Configuration::get('HEXADECIMAL_COLOR');

        return $helper->generateForm($fields);
    }

    //hook module routes
    public function hookModuleRoutes($params)
    {
        return [
            'test' => [
                'controller' => 'test',
                'rule' => 'color',
                'keywords' => [],
                'params' => [
                    'module' => $this->name,
                    'fc' => 'module',
                    'controller' => 'test'
                ]
            ]
        ];
    }
}
