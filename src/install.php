<?php

declare(strict_types=1);

/**
 * This file is part of the MultiFlexi package
 *
 * https://github.com/VitexSoftware/AbraFlexi-InvoiceEnhancer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Enhancer;

use AbraFlexi\ui\TWB5\ConnectionForm;
use Ease\TWB5\Container;
use Ease\TWB5\Row;
use Ease\TWB5\WebPage;
use Ease\TWB5\Widgets\Toggle;

\define('EASE_APPNAME', _('InvoiceEnhancer'));

require_once \dirname(__DIR__).'/vendor/autoload.php';

$oPage = new WebPage(_('Invoice Enhancer installer'));

if (empty(\Ease\WebPage::getRequestValue('myurl'))) {
    $_REQUEST['myurl'] = \dirname(\Ease\WebPage::phpSelf());
}

$loginForm = new ConnectionForm(['action' => 'install.php']);

$loginForm->addInput(
    new Toggle(
        'browser',
        isset($_REQUEST) && \array_key_exists('browser', $_REQUEST),
        'automatic',
        ['data-on' => _('AbraFlexi WebView'), 'data-off' => _('System Browser')],
    ),
    _('Open results in AbraFlexi WebView or in System default browser'),
);

// $loginForm->addInput( new \Ease\Html\InputUrlTag('myurl'), _('My Url'), dirname(\Ease\Page::phpSelf()), sprintf( _('Same url as you can see in browser without %s'), basename( __FILE__ ) ) );

$loginForm->fillUp($_REQUEST);

$loginForm->addItem(new \Ease\Html\PTag());

$loginForm->addItem(new \Ease\TWB5\SubmitButton(_('Install Button to AbraFlexi'), 'success btn-lg btn-block'));

$baseUrl = \Ease\WebPage::getRequestValue('myurl').'/index.php?authSessionId=${authSessionId}&companyUrl=${companyUrl}';

$buttonUrl = $baseUrl.'&kod=${object.kod}&id=${object.id}';

if ($oPage->isPosted()) {
    $browser = isset($_REQUEST) && \array_key_exists('browser', $_REQUEST) ? 'automatic' : 'desktop';

    $buttoner = new \AbraFlexi\RW(
        null,
        array_merge($_REQUEST, ['evidence' => 'custom-button']),
    );

    $buttoner->logBanner();

    $buttoner->insertToAbraFlexi(['id' => 'code:ENHANCER', 'url' => $buttonUrl,
        'title' => _('Invoice Enhancer'), 'description' => _('Invoice items to Pricelist Items convertor'),
        'location' => 'detail', 'evidence' => 'faktura-prijata', 'browser' => $browser]);

    $buttoner->addStatusMessage($buttonUrl, 'debug');

    if ($buttoner->lastResponseCode === 201) {
        $buttoner->addStatusMessage(_('Invoice Enhancer Button created'), 'success');
        \define('ABRAFLEXI_COMPANY', $buttoner->getCompany());
    }
} else {
    $oPage->addStatusMessage(_('My App URL').': '.$baseUrl);
}

$setupRow = new Row();
$setupRow->addColumn(2, new ui\AppLogo(['class' => 'img-fluid']));
$setupRow->addColumn(6, $loginForm);

$oPage->addItem(new Container(new \Ease\Html\H1Tag(_('AbraFlexi Invoice Enhancer'))));

$oPage->addItem(new Container($setupRow));

$oPage->addItem(new ui\PageBottom());

echo $oPage;
