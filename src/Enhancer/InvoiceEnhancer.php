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

use AbraFlexi\Bricks\Convertor;
use AbraFlexi\Cenik;
use AbraFlexi\Dodavatel;
use AbraFlexi\Exception;
use AbraFlexi\FakturaPrijata;
use Ease\Functions;
use Ease\Html\H1Tag;
use Ease\Html\PreTag;
use Ease\Shared;

/**
 * Description of Invoice.
 *
 * @author vitex
 */
class InvoiceEnhancer extends FakturaPrijata
{
    public Cenik $pricelist;
    private $pricer;

    public function convertSelected($requestData): void
    {
        $this->pricelist = new Cenik();
        $this->pricer = new Dodavatel(['firma' => \AbraFlexi\Code::ensure((string) $this->getDataValue('firma')), 'poznam' => 'Import: '.Shared::AppName().' '.Shared::AppVersion()."\nhttps://github.com/VitexSoftware/AbraFlexi-InvoiceEnhancer/"], ['autoload' => false]);

        if (\array_key_exists('convert', $requestData)) {
            $invoiceItems = Functions::reindexArrayBy($this->getSubItems(), 'id');

            foreach ($requestData['convert'] as $itemId) {
                if (\array_key_exists($itemId, $invoiceItems)) {
                    $subitemData = $invoiceItems[$itemId];

                    if (empty($subitemData['eanKod']) === false) { // Check for pricelist presence using EAN code
                        $this->pricelist->loadFromAbraFlexi(['eanKod' => $subitemData['eanKod']]);
                    } else { // If the EAN code is not availble, then check for pricelist presence using partnumber
                        $this->pricelist->loadFromAbraFlexi(['kod' => $subitemData['kod']]);
                    }

                    if ($this->pricelist->getMyKey()) { // Is such record loaded ?
                        $this->addStatusMessage(_('Pricelist item found. Assigning ...'), 'success');
                    } else {
                        $candidates = $this->pricer->getColumnsFromAbraFlexi('*', ['kodIndi' => $subitemData['kod'], 'firma' => \AbraFlexi\Code::ensure((string) $this->getDataValue('firma'))]);

                        if (empty($candidates)) {
                            $this->addStatusMessage(_('Pricelist Item not found. Creating new one'));
                            $this->pricelist = $this->createPricelistItem($subitemData);
                        } else {
                            $itemCode = $candidates[0]['cenik'];
                            $this->pricelist->loadFromAbraFlexi(\AbraFlexi\Code::ensure($itemCode));
                        }
                    }

                    $this->updateSupplierPrice($subitemData);

                    $saver = new FakturaPrijata();
                    $saver->setDataValue('id', \AbraFlexi\Code::ensure($this->getRecordCode()));

                    $saver->addArrayToBranch([
                        'id' => $subitemData['id'],
                        'cenik' => $this->pricelist,
                    ], 'polozkyFaktury');

                    try {
                        $result = $saver->insertToAbraFlexi();

                        if ($saver->lastResponseCode === 201) {
                            $this->addStatusMessage(sprintf(_('Invoice Item %s converted to Pricelist Item %s'), $subitemData['nazev'], $this->pricelist));
                        } else {
                            $this->addStatusMessage(sprintf(_('Error converting Invoice Item %s to Pricelist Item'), $subitemData['nazev']));
                        }
                    } catch (Exception $exc) {
                        echo new H1Tag($exc->getMessage());
                        echo new PreTag($exc->getTraceAsString());
                    }
                }

                $this->pricelist->dataReset();
            }

            $this->reload();
        }
    }

    public function createPricelistItem($subitemData)
    {
        $invoiceItem = new FakturaPrijataPolozka($subitemData);
        $cvrtr = new Convertor($invoiceItem, $this->pricelist);
        $pricelistItem = $cvrtr->conversion();
        $pricelistItem->setDataValue('dodavatel', $this->getDataValue('firma'));
        $pricelistItem->setDataValue('kod', $subitemData['kod']);

        return $pricelistItem->sync() ? $pricelistItem : null;
    }

    /**
     * @param mixed $activeItemData
     */
    public function updateSupplierPrice($activeItemData): void
    {
        $this->pricer->unsetDataValue('id');
        $this->pricer->setDataValue('cenik', $this->pricelist);
        $this->pricer->setDataValue('kodIndi', $activeItemData['kod']);
        $priceFound = $this->pricer->loadFromAbraFlexi(['cenik' => $this->pricelist, 'firma' => $this->getDataValue('firma')]);

        if (empty($priceFound)) {
            $this->pricer->setDataValue('cenik', $this->pricelist);
            $this->pricer->setDataValue('firma', $this->getDataValue('firma'));
        }

        $this->pricer->setDataValue('nakupCena', $activeItemData['cenaMj']); // TODO: Confirm column
        $this->pricer->setDataValue('mena', \AbraFlexi\Code::ensure($activeItemData['mena']));
        $this->pricer->setDataValue('cenik', \AbraFlexi\Code::ensure($activeItemData['kod']));

        try {
            $this->pricer->insertToAbraFlexi();
            $this->pricer->addStatusMessage(_('supplier price update').': '.\AbraFlexi\Code::strip($this->pricelist->getRecordCode()).': '.$this->pricer->getDataValue('nakupCena').' '.\AbraFlexi\Code::strip($this->pricer->getDataValue('mena')), $this->pricer->lastResponseCode === 201 ? 'success' : 'error');
        } catch (Exception $exc) {
            echo new H1Tag($exc->getMessage());
            echo new PreTag($exc->getTraceAsString());
        }
    }
}
