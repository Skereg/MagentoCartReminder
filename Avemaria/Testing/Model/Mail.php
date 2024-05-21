<?php
namespace Avemaria\Testing\Model;

use Avemaria\Testing\Model\Query;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Mail
{

    protected $query;

    protected $transportBuilder;

    protected $storeManager;

    public function __construct(
        Query $query,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->query = $query;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $customers = $this->query->selectQuery();
        $currentSystemTimestamp = time();

        foreach($customers as $customer) {
            $unixUpdate = strtotime($customer['updated_at']);
            if(($currentSystemTimestamp - $unixUpdate)<115200 && ($currentSystemTimestamp - $unixUpdate)>86400) {
                $receiverInfo = [
                    'email' => $customer['customer_email']
                ];

                $store = $this->storeManager->getStore();

                $items = $this->query->getItems($customer['entity_id']);

                $itemList = '';
                foreach ($items as $item) {
                    $itemName = $item['name'];
                    $itemList .= $itemName . " ";
                }

                $templateParams = ['store' => $store, 'customer' => $customer['customer_firstname'], 'items' => $itemList];

                $transport = $this->transportBuilder->setTemplateIdentifier(
                    'cart_reminder_template'
                )->setTemplateOptions(
                    ['area' => 'frontend', 'store' => $store->getId()]
                )->addTo(
                    $receiverInfo['email']
                )->setTemplateVars(
                    $templateParams
                )->setFrom(
                    'general'
                )->getTransport();

                $transport->sendMessage();
            }
        }
        return $this;
    }
}
