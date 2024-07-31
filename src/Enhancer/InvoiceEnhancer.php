<?php

namespace AbraFlexi\Enhancer;

use AbraFlexi\Bricks\Convertor;
use AbraFlexi\Cenik;
use AbraFlexi\Dodavatel;
use AbraFlexi\Exception;
use AbraFlexi\FakturaPrijata;
use AbraFlexi\Functions as Functions2;
use AbraFlexi\RO;
use Ease\Functions;
use Ease\Html\H1Tag;
use Ease\Html\PreTag;
use Ease\Shared;

/**
 * Description of Invoice
 *
 * @author vitex
 */
class InvoiceEnhancer extends FakturaPrijata {

    /**
     * 
     * @var Cenik
     */
    public $pricelist = null;
    private $pricer;

    public function convertSelected($requestData) {
        $this->pricelist = new Cenik();
        $this->pricer = new Dodavatel(['firma' => $this->getDataValue('firma'), 'poznam' => 'Import: ' . Shared::AppName() . ' ' . Shared::AppVersion() . "\nhttps://github.com/Spoje-NET/discomp2abraflexi"], ['evidence' => 'dodavatel', 'autoload' => false]);

        if (array_key_exists('convert', $requestData)) {
            $invoiceItems = Functions::reindexArrayBy($this->getSubItems(), 'id');
            foreach ($requestData['convert'] as $itemId) {
                if (array_key_exists($itemId, $invoiceItems)) {
                    $subitemData = $invoiceItems[$itemId];
                    if (empty($subitemData['eanKod']) === false) {
                        $this->pricelist->loadFromAbraFlexi(['eanKod' => $subitemData['eanKod']]);
                    } else {
                        $this->pricelist->loadFromAbraFlexi(['kod' => $subitemData['kod']]);
                    }
                    if ($this->pricelist->getMyKey()) {
                        $this->addStatusMessage(_('Pricelist item found. Assigning ...'), 'success');
                    } else {
                        $this->addStatusMessage(_('Pricelist Item not found. Creating new one'));
                        $this->pricelist = $this->createPricelistItem($subitemData);
                    }

                    $this->updateSupplierPrice($subitemData);

                    $saver = new FakturaPrijata();
                    $saver->setDataValue('id', Functions2::code($this->getRecordCode()));

                    $saver->addArrayToBranch([
                        'id' => $subitemData['id'],
                        'cenik' => $this->pricelist,
                            ], 'polozkyFaktury');

                    try {
                        $result = $saver->insertToAbraFlexi();
                        if ($saver->lastResponseCode == 201) {
                            $this->addStatusMessage(sprintf(_('Invoice Item %s converted to Pricelist Item %s'), $subitemData['nazev'], $this->pricelist));
                        } else {
                            $this->addStatusMessage(sprintf(_('Error converting Invoice Item %s to Pricelist Item'), $subitemData['nazev']));
                        }
                    } catch (Exception $exc) {
                        echo new H1Tag($exc->getMessage());
                        echo new PreTag($exc->getTraceAsString());
                    }
                }
            }
            $this->reload();
        }
    }

    public function createPricelistItem($subitemData) {
        $invoiceItem = new FakturaPrijataPolozka($subitemData);
        $cvrtr = new Convertor($invoiceItem, $this->pricelist);
        $pricelistItem = $cvrtr->conversion();
        $pricelistItem->setDataValue('dodavatel', $this->getDataValue('firma'));
        $pricelistItem->setDataValue('kod', $subitemData['kod']);
        return $pricelistItem->sync() ? $pricelistItem : null;
    }

    /**
     *
     */
    public function updateSupplierPrice($activeItemData) {
        $this->pricer->unsetDataValue('id');
        $this->pricer->setDataValue('cenik', $this->pricelist);
        $this->pricer->setDataValue('kodIndi', $activeItemData['kod']);
        $priceFound = $this->pricer->loadFromAbraFlexi(['cenik' => $this->pricelist, 'firma' => $this->getDataValue('firma')]);
        if (empty($priceFound)) {
            $this->pricer->setDataValue('cenik', $this->pricelist);
            $this->pricer->setDataValue('firma', $this->getDataValue('firma'));
        }
        $this->pricer->setDataValue('nakupCena', $activeItemData['cenaMj']); //TODO: Confirm column
        $this->pricer->setDataValue('mena', RO::code($activeItemData['mena']));
        $this->pricer->setDataValue('cenik', RO::code($activeItemData['kod']));

        try {
            $this->pricer->insertToAbraFlexi();
            $this->pricer->addStatusMessage(_('supplier price update') . ': ' . RO::uncode($this->pricelist->getRecordCode()) . ': ' . $this->pricer->getDataValue('nakupCena') . ' ' . RO::uncode($this->pricer->getDataValue('mena')), $this->pricer->lastResponseCode == 201 ? 'success' : 'error');
        } catch (Exception $exc) {
            echo new H1Tag($exc->getMessage());
            echo new PreTag($exc->getTraceAsString());
        }
    }
}
