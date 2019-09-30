<?php
/**
 * Created by PhpStorm.
 * User: wsydn
 * Date: 25.03.2019
 * Time: 17:44
 */

namespace wsydney76\guide\widgets;

use Craft;
use craft\base\Widget;
use craft\elements\Entry;

class GuideWidget extends Widget
{
    public static function displayName(): string
    {
        return 'Guides Widgets';
    }

    public function getTitle(): string {
        return 'New Guides';
    }

    public function getBodyHtml()
    {
        $app = Craft::$app;

        $entries = Entry::find()
            ->section('guide')
            ->showInDashboard(1)
            ->limit(4)
            ->orderBy('postDate desc')
            ->all();

        return $app->view->renderTemplate('wsguide/guidewidget.twig',
            [
                'entries' => $entries
            ]);
    }
}
