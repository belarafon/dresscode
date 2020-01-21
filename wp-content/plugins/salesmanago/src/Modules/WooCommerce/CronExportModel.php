<?php

namespace bhr\Modules\WooCommerce;

use bhr\Modules\WooCommerce\ExportModel;
use SALESmanago\Exception\SalesManagoException;

class CronExportModel extends ExportModel
{
    /**
     * @param int $interval
     */
    public function setAdvancedForExportContacts($interval)
    {
        $this->advancedExport = true;

        $this->dateRange = [
            'from' => time() - ($interval*3600),
            'to' => time()
        ];
    }

    /**
     * @param mixed $page
     * @throws SalesManagoException
     * @return mixed
     */
    public function getEvents($page = 1)
    {
        try{
            $data = [];

            $argGetOrders = [
                'status' => [
                    'wc-completed',
                    'wc-processing',
                    'wc-pending',
                    'wc-on-hold',
                    'wc-refunded'
                ],
                'limit' => $this->packageLimit,
                'page' => $page
            ];

            if($this->advancedExport
                && !empty($this->dateRange['from'])
                && !empty($this->dateRange['to'])
            ){
                $argGetOrders['date_created'] = $this->dateRange['from'];
                $argGetOrders['date_created'] .= '...';
                $argGetOrders['date_created'] .= $this->dateRange['to'];
            }

            if ($orders = wc_get_orders($argGetOrders)) {
                if (empty($orders)) {
                    return false;
                }

                foreach ($orders as $order) {
                    if ($order->get_items()) {
                        $products = $order->get_items();
                        $prodArr = [
                            'ids' => [],
                            'names' => [],
                            'quantity' => []
                        ];

                        foreach ($products as $product) {
                            $prodArr['ids'][] = ($product->get_product_id())
                                ? $product->get_product_id()
                                : '';
                            $prodArr['names'][] = ($product->get_name())
                                ? $product->get_name()
                                : '';
                            $prodArr['quantity'][] = ($product->get_quantity())
                                ? $product->get_quantity()
                                : '';
                        }

                        if ($order->get_billing_email()) {
                            $data[] = [
                                'email' => $order->get_billing_email(),
                                'contactEvent' =>
                                    [
                                        'date' => ($order->get_date_created()->getTimestamp())
                                            ? $order->get_date_created()->getTimestamp() * 1000
                                            : '',
                                        'description' => ($order->get_payment_method_title())
                                            ? $order->get_payment_method_title()
                                            : '',
                                        'products' => is_array($prodArr['ids'])
                                            ? implode(',', $prodArr['ids'])
                                            : $prodArr['ids'],
                                        'location' => $this->getStoreId(get_home_url()),
                                        'value' => ($order->get_total())
                                            ? $order->get_total()
                                            : '',
                                        'contactExtEventType' => 'PURCHASE',
                                        'detail1' => is_array($prodArr['names'])
                                            ? implode(',', $prodArr['names'])
                                            : '',
                                        'detail2' => ($order->get_order_key())
                                            ? $order->get_order_key()
                                            : '',
                                        'detail3' => is_array($prodArr['quantity'])
                                            ? implode('/', $prodArr['quantity'])
                                            : $prodArr['quantity'],
                                        'externalId' => ($order->get_id())
                                            ? $order->get_id()
                                            : '',
                                        'shopDomain' => get_site_url()
                                    ]
                            ];
                        }
                    }
                }

                return $data;
            } else {
                return false;
            }

        } catch (\Exception $e){
            throw new SalesManagoException($e->getMessage());
        }
    }
}