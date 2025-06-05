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

use AbraFlexi\Enhancer\ui\InvoiceForm;
use AbraFlexi\RO;
use Ease\Html\ATag;
use Ease\WebPage;

require './init.php';

$kod = WebPage::getRequestValue('kod');

if (empty($kod)) {
    $oPage->addStatusMessage(_('Bad call'), 'warning');
    $oPage->addItem(new ATag('install.php', _('Please setup your AbraFlexi connection')));
} else {
    try {
        $invoicer = new InvoiceEnhancer(RO::code($kod));
        $oPage->setPageTitle($invoicer->getRecordIdent());

        if ($oPage->isPosted()) {
            $invoicer->convertSelected($_REQUEST);
        }

        $oPage->body->addItem(new InvoiceForm($invoicer));
    } catch (\AbraFlexi\Exception $exc) {
        if ($exc->getCode() === 401) {
            $oPage->body->addItem(new \Ease\Html\H2Tag(_('Session Expired')));
        } else {
            $oPage->addItem(new \Ease\Html\H1Tag($exc->getMessage()));
            $oPage->addItem(new \Ease\Html\PreTag($exc->getTraceAsString()));
        }
    }
}

$oPage->addItem(new ui\PageBottom());
echo $oPage;
