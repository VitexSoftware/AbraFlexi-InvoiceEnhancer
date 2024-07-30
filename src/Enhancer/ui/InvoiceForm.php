<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace AbraFlexi\Enhancer\ui;

/**
 * Description of Invoice
 *
 * @author vitex
 */
class InvoiceForm extends \Ease\TWB5\Panel {

    public function __construct(\AbraFlexi\FakturaPrijata $invoice) {

        $form = new \Ease\TWB5\Form([], [], $this->items($invoice));
        $form->addItem(new \Ease\Html\InputHiddenTag('kod', \AbraFlexi\Functions::uncode($invoice->getRecordCode())));
        $form->addItem(new \Ease\Html\DivTag(new \Ease\TWB5\SubmitButton(_('Do it!'), 'primary btn-lg'), ['class' => 'd-grid gap-2']));

        parent::__construct(\AbraFlexi\Functions::uncode($invoice->getRecordIdent()), 'default', $form, $invoice->getDataValue('popis'));
//        $this->addItem( new \Ease\Html\PreTag(print_r($invoice->getSubItems(),true)));
    }

    /**
     * 
     * @param \AbraFlexi\FakturaPrijata $invoice
     * 
     * @return \Ease\TWB5\Table
     */
    public function items($invoice) {
        $itemsTable = new \Ease\TWB5\Table();
        $itemsTable->addRowHeaderColumns(['kod' => _('Code'), 'ean' => _('EAN'), 'name' => _('Name'), 'price' => _('Price'), 'x' => _('Convert')]);

        if ($invoice->getSubItems()) {
            foreach ($invoice->getSubItems() as $item) {
                switch ($item['typPolozkyK']) {
                    case 'typPolozky.obecny':
                        $itemsTable->addRowColumns([
                            'kod' => $item['kod'], // new \Ease\Html\InputTextTag('kod[' . $item['id'] . ']', $item['kod']),
                            'ean' => empty($item['eanKod']) ? '' : $item['eanKod'], // [ new \Ease\Html\InputHiddenTag('ean[' . $item['id'] . ']', $item['eanKod']) , $item['eanKod'], new \Ease\Html\ATag('https://www.ean-search.org/ean/' . $item['eanKod'], '❓', ['target' => '_blank'])],
                            'name' => $item['nazev'], // $nameInput,
                            'price' => $item['cenaMj'] . ' ' . \AbraFlexi\Functions::uncode($item['mena']), // [new \Ease\Html\InputTextTag('cenaMj[' . $item['id'] . ']', $item['cenaMj']), \AbraFlexi\Functions::uncode($item['mena'])],
                            'convert' => new \Ease\TWB5\Widgets\Toggle('convert[' . $item['id'] . ']', !empty($item['eanKod']), $item['id'])
                        ]);
                        break;
                    case 'typPolozky.katalog':
                        $itemsTable->addRowColumns([
                            'kod' => $item['kod'],
                            'ean' => empty($item['eanKod']) ? '' : $item['eanKod'],
                            'name' => $item['nazev'],
                            'price' => $item['cenaMj'] . ' ' . \AbraFlexi\Functions::uncode($item['mena']),
                            'convert' => '✅'
                        ]);
                        break;

                    default:
                        break;
                }
            }
        } else {
            $this->addStatusMessage(_('No Subitems in thi invoice'), 'info');
        }
        return $itemsTable;
    }
}
