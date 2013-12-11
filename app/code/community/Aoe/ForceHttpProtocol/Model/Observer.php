<?php

class Aoe_ForceHttpProtocol_Model_Observer
{
    public function controllerActionPredispatch(Varien_Event_Observer $event)
    {
        $http = array_filter(array_map('trim', explode("\n", trim(Mage::getStoreConfig('system/aoe_forcehttpprotocol/http')))));
        $https = array_filter(array_map('trim', explode("\n", trim(Mage::getStoreConfig('system/aoe_forcehttpprotocol/https')))));
        $response = Mage::app()->getResponse();
        $request = Mage::app()->getRequest();
        $url = Mage::helper('core/http')->getHttpHost() . $_SERVER['REQUEST_URI'];

        if ($request->isSecure() && in_array($event->getControllerAction()->getFullActionName(), $http)) {
            $response->setRedirect('http://' . $url, 302);
            $response->sendResponse();
            exit;
        } else if (!$request->isSecure() && in_array($event->getControllerAction()->getFullActionName(), $https)) {
            $response->setRedirect('https://' . $url, 302);
            $response->sendResponse();
            exit;
        }
    }
}
