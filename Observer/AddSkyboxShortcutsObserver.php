<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Observer;

use Skybox\Checkout\Block\InContext\Minicart\Button;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * SkyBox Checkout module observer
 */
class AddSkyboxShortcutsObserver implements ObserverInterface
{
    /**
     * Block class
     */
    const SKYBOX_SHORTCUT_BLOCK = Button::class;

    /**
     * Add Skybox Checkout shortcut buttons
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Remove button from catalog pages
        if ($observer->getData('is_catalog_product')) {
            return;
        }

        /** @var ShortcutButtons $shortcutButtons */
        $shortcutButtons = $observer->getEvent()->getContainer();

        $shortcut = $shortcutButtons->getLayout()->createBlock(self::SKYBOX_SHORTCUT_BLOCK);

        $shortcut->setIsInCatalogProduct(
            $observer->getEvent()->getIsCatalogProduct()
        );

        $shortcutButtons->addShortcut($shortcut);
    }
}
