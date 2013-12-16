<?php

class Aoe_ForceHttpProtocol_Model_Observer
{
    public function controllerActionPredispatch(Varien_Event_Observer $event)
    {
        /** @var $controller Mage_Core_Controller_Varien_Action */
        $controller = $event->getControllerAction();
        if (!$controller instanceof Mage_Core_Controller_Varien_Action) {
            // Bail out early as this is not the event we are expecting
            return;
        }

        // Get the current store
        $store = Mage::app()->getStore();

        // Is the current request secure
        $isSecure = $store->isCurrentlySecure();

        // Resolve to a config key based on the current request being secure or not
        $configKey = 'system/aoe_forcehttpprotocol/' . ($isSecure ? 'http' : 'https');

        // Build an array of full action names that should invoke a redirect
        $actions = explode("\n", str_replace(',', "\n", Mage::getStoreConfig($configKey)));
        $actions = array_filter(array_map('trim', $actions));

        // Check the current action name against the action array
        if (in_array($controller->getFullActionName(), $actions)) {
            // Let Magento generate the proper base URL
            $url = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, !$isSecure);

            // Append the request portion of the URL to the base
            $url .= ltrim($controller->getRequest()->getRequestString(), '/');

            // Redirect the request to the proper URL and send response
            $controller->getResponse()->setRedirect($url, 301);

            // Set the no-dispatch flag to indicate that the request should not dispatch to the action
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
    }
}
