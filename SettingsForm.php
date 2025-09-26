<?php

/**
 * @brief Plugin settings: connect to GetFTR
 */

namespace APP\plugins\generic\getftr;

use Exception;

use PKP\form\Form;
use PKP\form\validation\FormValidator;
use PKP\form\validation\FormValidatorPost;
use PKP\form\validation\FormValidatorCSRF;
use PKP\notification\Notification;

use APP\core\Application;
use APP\template\TemplateManager;
use APP\notification\NotificationManager;

class SettingsForm extends Form {
    
    public function __construct(private GetftrPlugin $plugin, private int $contextId)
    {
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
        $this->addCheck(
            new FormValidator(
                $this,
                'integratorId',
                FormValidator::FORM_VALIDATOR_REQUIRED_VALUE,
                'plugins.generic.getftr.settings.integratorIdRequired'
            )
        );
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    function initData(): void {
        $plugin = $this->plugin;
        $contextId = $this->contextId;

        $this->setData('integratorId', $plugin->getSetting($contextId, 'integratorId'));

        parent::initData();
    }

    function readInputData(): void {
        $this->readUserVars(['integratorId']);
    }

    function fetch($request, $template = null, $display = false): ?string {
        $plugin = $this->plugin;
        $contextId = $this->contextId;

        $templateManager = TemplateManager::getManager($request);

        $templateManager->assign('pluginName', $plugin->getName());

        return parent::fetch($request, $template, $display);
    }

    function execute(...$functionArgs): void {
        $plugin = $this->plugin;
        $contextId = $this->contextId;

        $plugin->updateSetting($contextId, 'integratorId', $this->getData('integratorId') , 'string');

        $notificationManager = new NotificationManager();
        $notificationManager->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            Notification::NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('plugins.generic.getftr.settings.settingsUpdated')]
        );

        parent::execute(...$functionArgs);
    }

}
