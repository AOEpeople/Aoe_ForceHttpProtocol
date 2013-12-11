<?php

class Aoe_ForceHttpProtocol_Model_Observer
{
    public function controllerActionPredispatch(Varien_Event_Observer $event)
    {
        $http = array_filter(explode("\n", trim(Mage::getStoreConfig('system/aoe_forcehttpprotocol/http'))));
        $https = array_filter(explode("\n", trim(Mage::getStoreConfig('system/aoe_forcehttpprotocol/https'))));
        $response = Mage::app()->getResponse();
        $request = Mage::app()->getRequest();

        if ($request->isSecure() && in_array($event->getControllerAction()->getFullActionName(), $http)) {
            $response->setRedirect(str_replace('https://', 'http://', $request->getRequestUri()), 302);
            $response->sendResponse();
            exit;
        } else if (!$request->isSecure() && in_array($event->getControllerAction()->getFullActionName(), $https)) {
            $response->setRedirect(str_replace('http://', 'https://', $request->getRequestUri()), 302);
            $response->sendResponse();
            exit;
        }
    }
}
