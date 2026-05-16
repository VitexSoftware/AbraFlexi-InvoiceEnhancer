<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexi-InvoiceEnhancer package
 *
 * https://github.com/VitexSoftware/AbraFlexi-InvoiceEnhancer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\AbraFlexi\Enhancer\ui;

use AbraFlexi\Enhancer\ui\AppLogo;
use PHPUnit\Framework\TestCase;

class AppLogoTest extends TestCase
{
    public function testInstantiation(): void
    {
        $logo = new AppLogo();
        $this->assertInstanceOf(\Ease\Html\ImgTag::class, $logo);
    }

    public function testRendersInlineSvg(): void
    {
        $logo = new AppLogo();
        ob_start();
        $logo->draw();
        $rendered = ob_get_clean();
        $this->assertStringContainsString('data:image/svg+xml;base64,', $rendered);
    }

    public function testAcceptsProperties(): void
    {
        $logo = new AppLogo(['class' => 'logo', 'width' => '64']);
        $this->assertSame('logo', $logo->getTagProperty('class'));
    }
}
