<?php

class Aoe_ForceHttpProtocol_Model_Observer
{
    public function controllerActionPredispatch(Varien_Event_Observer $event)
    {
        $http = array_filter(array_map('trim', explode("\n", trim(Mage::getStoreConfig('system/aoe_forcehttpprotocol/http')))));
        $https = array_filter(array_map('trim', explode("\n", trim(Mage::getStoreConfig('system/aoe_forcehttpprotocol/https')))));
        $response = Mage::app()->getResponse();
        $request = Mage::app()->getRequest();

        if ($request->isSecure() && in_array($event->getControllerAction()->getFullActionName(), $http)) {
            header('Location: ' . str_replace('https://', 'http://', $request->getRequestUri()));
            exit;
        } else if (!$request->isSecure() && in_array($event->getControllerAction()->getFullActionName(), $https)) {
            //$response->setRedirect(str_replace('http://', 'https://', $request->getRequestUri()), 302);
            //$response->sendResponse();
            header('Location: ' . str_replace('http://', 'https://', $request->getRequestUri()));
            exit;
        }
    }
}
