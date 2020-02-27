<?php

namespace wsydney76\guide;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\records\Widget;
use craft\services\Dashboard;
use craft\services\UserPermissions;
use craft\web\twig\variables\Cp;
use craft\web\UrlManager;
use craft\web\View;
use wsydney76\guide\assetbundles\GuideAssets;
use wsydney76\guide\widgets\GuideWidget;
use yii\base\Event;
use yii\base\Module;
use yii\web\User;
use const DIRECTORY_SEPARATOR;

class Guide extends Plugin
{
    public function init()
    {

        // Base template directory for site
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['wsguide'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
        });

        // Set routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['guide/<guideSlug:.*>'] = ['template' => 'wsguide/guide'];
            $event->rules['guide-ajax/<guideSlug:.*>'] = ['template' => 'wsguide/guideajax'];
        });

        // Set Nav
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS, function($event) {
            $nav = ['url' => 'guide/guide', 'label' => 'Guide', 'icon' => '@app/icons/routes.svg'];
            foreach ($event->navItems as $i => $navItem) {
                if ($navItem['url'] == 'assets') {
                    break;
                }
            }
            array_splice($event->navItems, $i + 1, 0, [$nav]);
        });

        // Register Widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = GuideWidget::class;
        }
        );

        // Control user widgets
        Event::on(
            User::class,
            User::EVENT_AFTER_LOGIN, function() {
            $this->_afterUserLogin();
        });

        // Register AssetBundle for guide

        if (Craft::$app->request->isCpRequest) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE, function() {
                Craft::$app->view->registerAssetBundle(GuideAssets::class);;
            }
            );
        }

        // Register Edit Screen extensions
        Craft::$app->view->hook('cp.entries.edit.meta', function(&$context) {
            if ($context['entry'] != null) {
                return Craft::$app->view->renderTemplate('wsguide/editorbutton.twig', ['entry' => $context['entry']]);
            }
        });

        // parent::init();
    }

    private function _afterUserLogin()
    {
        $user = Craft::$app->user->identity;
        $widgetType = GuideWidget::class;

        if (!$user->admin) {
            $query = Widget::find()
                ->where(['userId' => $user->id])
                ->andWhere(['type' => $widgetType]);

            if (!$query->count()) {
                $widget = new Widget([
                    'userId' => $user->id,
                    'type' => $widgetType,
                    'enabled' => 1,
                    'sortOrder' => 99
                ]);
                $widget->save();
            }
        }
    }
}
