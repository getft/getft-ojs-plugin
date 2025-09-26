<?php

/**
 *
 * @class GetftrPlugin
 *
 * @brief Plugin to add GetFTR buttons to DOIs
 *
 */

namespace APP\plugins\generic\getftr;

use PKP\core\JSONMessage;
use PKP\core\PKPPageRouter;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;

use APP\core\Application;
use APP\template\TemplateManager;

class GetftrPlugin extends GenericPlugin
{

    public function register($category, $path, $mainContextId = null): bool
    {
        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

        if ($this->getEnabled($mainContextId)) {
            Hook::add('TemplateManager::display', $this->registerScript(...));
            Hook::add('Templates::Issue::Issue::Article', $this->handleIssueArticleSummary(...));
            // Hook::add('Templates::Article::Details', $this->handleArticleDetails(...));
            Hook::add('TemplateManager::display', $this->handleArticleMain(...));
        }

        return true;
    }

    public function getDisplayName(): string
    {
        return __('plugins.generic.getftr.displayName');
    }

    public function getDescription(): string
    {
        return __('plugins.generic.getftr.description');
    }

    public function getActions($request, $actionArgs): array
    {
        $actions = parent::getActions($request, $verb);
        if (!$this->getEnabled()) {
            return $actions;
        }

        $router = $request->getRouter();
        $url = $router->url($request, null, null, 'manage', null, [
            'verb' => 'settings',
            'plugin' => $this->getName(),
            'category' => 'generic'
        ]);

        return array_merge(
            [
                new LinkAction(
                    'settings',
                    new AjaxModal($url, $this->getDisplayName()),
                    __('plugins.generic.getftr.settings'),
                ),
            ],
            $actions
        );
    }

    function manage($args, $request) {
        $verb = $request->getUserVar('verb');

        switch ($verb) {
            case 'settings':
                $context = $request->getContext();

                $form = new SettingsForm($this, $context->getId());

                $save = $request->getUserVar('save');

                // OPEN - render
                if (!$save) {
                    $form->initData();
                    return new JSONMessage(true, $form->fetch($request));
                }

                $form->readInputData();

                $valid = $form->validate();

                // INVALID - render
                if (!$valid) {
                    return new JSONMessage(true, $form->fetch($request));
                }

                $form->execute();
                return new JSONMessage(true);

        }
        return parent::manage($args, $request);
    }

    public function getContextSpecificPluginSettingsFile(): string
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    function registerScript($hookName, $args): int
    {
        if(!$this->getEnabled()) {
            return Hook::CONTINUE;
        }

        $request = Application::get()->getRequest();
        $context = $request->getContext();
        
        if (!$context) {
            return Hook::CONTINUE;
        }

        $router = $request->getRouter();

        if (!$router instanceof PKPPageRouter) {
            return Hook::CONTINUE;
        }

        $integratorId = $this->getSetting($context->getId(), 'integratorId');
        if (empty($integratorId)) {
            return Hook::CONTINUE;
        }

        $dropinButtonCode = "
(function (G, e, t, F, T, R) {
G[t] = {};
G[t][T] = R;
s = e.createElement(F);
s.async = 1;
s.src = 'http://dropin-button.getft.io/integrator/' + R;
s.type = 'text/javascript';
q = e.getElementsByTagName(F)[0];
q.parentNode.insertBefore(s, q);
})(window, document, 'getftr', 'script', 'integratorId', '{$integratorId}');
";

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->addJavaScript(
            'GetFTRDropinButton',
            $dropinButtonCode,
            [
                'priority' => TemplateManager::STYLE_SEQUENCE_LAST,
                'inline' => true,
            ]
        );

        return Hook::CONTINUE;
    }

    function handleIssueArticleSummary($hookName, $args): int
    {
        if(!$this->getEnabled()) {
            return Hook::CONTINUE;
        }

        $smarty = & $args[1];
        $output = & $args[2];

        $article = $smarty->getTemplateVars('article');
        $publication = $article->getCurrentPublication();
        $doi = $publication->getDoi();

        $smarty->assign('doi', $doi);

        $output .= $smarty->fetch($this->getTemplateResource('articleSummary.tpl'));

        return Hook::CONTINUE;
    }

    function handleArticleDetails($hookName, $args): int
    {
        if(!$this->getEnabled()) {
            return Hook::CONTINUE;
        }

        $smarty = & $args[1];
        $output = & $args[2];

        $publication = $smarty->getTemplateVars('publication');
        $doi = $publication->getDoi();

        $smarty->assign('doi', $doi);

        $output .= $smarty->fetch($this->getTemplateResource('articleDetails.tpl'));

        return Hook::CONTINUE;
    }

    function handleArticleMain($hookName, $args) {
        if(!$this->getEnabled()) {
            return Hook::CONTINUE;
        }

        $smarty = $args[0];
        $template = $args[1];

        if ($template !== 'frontend/pages/article.tpl') {
            return Hook::CONTINUE;
        }

        $smarty->registerFilter('output', $this->handleArticleMain_filter(...));

        return Hook::CONTINUE;
    }

    function handleArticleMain_filter($output, $smarty) {

        // Find "main_entry" class
        $matches = preg_match('/class=\"main_entry\"/s', $output, $done);
        $found = !empty($done);

        if (!$found) {
            return $output;
        }

        $publication = $smarty->getTemplateVars('publication');
        $doi = $publication->getDoi();

        $smarty->assign('doi', $doi);

        $templateResource = $this->getTemplateResource('articleTop.tpl');
        
        $render = $smarty->fetch($templateResource);

        // Insert before first section
        $from = '<section';
        $to = $render . '<section';

        $output = preg_replace('/' . preg_quote($from) . '/', $to, $output, 1);

        return $output;
    }
}
