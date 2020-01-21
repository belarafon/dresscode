<?php

namespace bhr\Modules\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use bhr\Model\AbstractExportModel;

use bhr\Helper\Contact\Address;
use bhr\Helper\Contact\Contact;
use bhr\Helper\Contact\Options;
use bhr\Model\StoreTrait;
use SALESmanago\Exception\SalesManagoException;

class ExportModel extends AbstractExportModel
{
    use StoreTrait;

	/**
	 * @throws SalesManagoException
	 */
	public function countCustomers()
	{
		try {
			$query = "SELECT COUNT(unioned.email) FROM (SELECT DISTINCT postmeta.email as email FROM (
                SELECT p.post_id as post_id,
                       MAX(CASE WHEN p.meta_key = '_billing_email' THEN p.meta_value END) as email
                FROM {$this->db->postmeta} p
                WHERE p.post_id IN (
                    SELECT post_id
                    FROM {$this->db->postmeta} wppm
                    WHERE wppm.meta_value LIKE '%order_%'
                    ORDER BY p.meta_id DESC
                )
                GROUP BY p.post_id
            ) postmeta
                LEFT JOIN {$this->db->posts} as posts
                    ON postmeta.post_id = posts.id";

			$query .= (
				$this->advancedExport
				&& isset( $this->dateRange )
			)
				? "
	    WHERE posts.post_date >= '{$this->dateRange['from']}'
		  AND posts.post_date <= '{$this->dateRange['to']}'"
				: '';

			$query .= "
	    UNION
		SELECT wpu.user_email as email
		    FROM {$this->db->users} wpu";

			$query .= "
	        WHERE";
			$query .= (
				$this->advancedExport
				&& isset( $this->dateRange )
			)
				? " wpu.user_registered >= '{$this->dateRange['from']}'
		          AND wpu.user_registered <= '{$this->dateRange['to']}'
		          AND"
				: '';
			$query .= " wpu.ID IN
		    (SELECT wpum.user_id
		     FROM {$this->db->usermeta} wpum
		     WHERE wpum.meta_key LIKE 'wp_capabilities'
		       AND wpum.meta_value LIKE '%customer%')) as unioned";

			$uniqueCustomersEmails = $this->db->get_var( $query );

			return $uniqueCustomersEmails;
		} catch (\Exception $e) {
			throw new SalesManagoException($e->getMessage());
		}
	}

	/**
	 * @param integer $page
	 * @throws SalesManagoException
	 * @return array $upsertDetails
	 */
    public function getCustomers($page = 0)
    {
    	try {
		    $offset        = $page * $this->packageLimit;
		    $upsertDetails = array();

		    $query = "SELECT DISTINCT ms.email,
					                ms.name,
					                ms.surname,
					                ms.address,
					                ms.city,
					                ms.postcode,
					                ms.phone,
					                ms.post_id
					      FROM (
						      SELECT p.post_id as post_id,
					                      MAX(CASE WHEN p.meta_key = '_billing_email' THEN p.meta_value END) email,
					                      MAX(CASE WHEN p.meta_key = '_billing_first_name' THEN p.meta_value END) name,
					                      MAX(CASE WHEN p.meta_key = '_billing_last_name' THEN p.meta_value END) surname,
					                      MAX(CASE WHEN p.meta_key = '_billing_address_1' THEN p.meta_value END) address,
					                      MAX(CASE WHEN p.meta_key = '_billing_city' THEN p.meta_value END) city,
					                      MAX(CASE WHEN p.meta_key = '_billing_postcode' THEN p.meta_value END) postcode,
					                      MAX(CASE WHEN p.meta_key = '_billing_phone' THEN p.meta_value END) phone
					               FROM {$this->db->postmeta} p
					               WHERE p.post_id IN
							    (
								    SELECT post_id
					                         FROM {$this->db->postmeta}
					                         WHERE meta_value
					                                   LIKE '%order_%'
					                         ORDER BY {$this->db->postmeta}.`meta_id` DESC
					                     )
					                 AND p.post_id IN (
								    SELECT id
					                   FROM {$this->db->posts}";
		    $query .= (
			    $this->advancedExport
			    && isset( $this->dateRange )
		    )
			    ? "
                   WHERE post_date >= '{$this->dateRange['from']}'
                         AND post_date <= '{$this->dateRange['to']}'
                "
		        : '' ;
		    $query .= ")
               GROUP BY p.post_id
           ) ms

		      UNION
		
		      SELECT DISTINCT ud.user_email as email,
		                      umeta.name,
		                      umeta.surname,
		                      umeta.address,
		                      umeta.city,
		                      umeta.postcode,
		                      umeta.phone,
		                      umeta.user_id
		
		      FROM (SELECT mc.name,
		                   mc.surname,
		                   mc.address,
		                   mc.city,
		                   mc.postcode,
		                   mc.phone,
		                   mc.user_id
		            FROM (
			            SELECT up.user_id as user_id,
		                            MAX(CASE WHEN up.meta_key = 'first_name' THEN up.meta_value END) name,
		                            MAX(CASE WHEN up.meta_key = 'last_name' THEN up.meta_value END) surname,
		                            MAX(CASE WHEN up.meta_key = '' THEN up.meta_value END) address,
		                            MAX(CASE WHEN up.meta_key = '' THEN up.meta_value END) city,
		                            MAX(CASE WHEN up.meta_key = '' THEN up.meta_value END) postcode,
		                            MAX(CASE WHEN up.meta_key = '' THEN up.meta_value END) phone
		                     FROM wp_usermeta up
		                     WHERE up.user_id IN
				    (
					    SELECT user_id
		                               FROM `wp_usermeta`
		                               WHERE meta_value LIKE '%customer%'
		                               ORDER BY `wp_usermeta`.`umeta_id` DESC
		                           )
		                       AND up.user_id IN (
					    SELECT id
		                         FROM `wp_users`
                         ";
		    $query .= (
			    $this->advancedExport
			    && isset( $this->dateRange )
		    ) ? "
                  WHERE user_registered >= '{$this->dateRange['from']}'
                    AND user_registered <= '{$this->dateRange['to']}'"
			    : '';
		    $query .= "
                     )
                     GROUP BY up.user_id
                 ) mc) umeta

               LEFT JOIN wp_users ud
                         ON ID = umeta.user_id";

		    if ( $this->packageLimit != '-1' ) {
			    $query .= " LIMIT {$this->packageLimit} OFFSET {$offset} ";
		    }

		    $results = $this->db->get_results( $query, ARRAY_A );

		    if ( ! isset( $results ) ) {
			    return false;
		    }

		    foreach ( $results as $wkey => $user ) {
			    $customer = $this->translateCustomer( $user );

			    if ( ! empty( $customer ) ) {
				    array_push( $upsertDetails, $customer );
			    }
		    }

		    return $upsertDetails;
	    } catch (\Exception $e) {
		    throw new SalesManagoException($e->getMessage());
	    }
    }

	public function translateCustomer($user = array())
	{
		if (empty($user['email'])) {
			return false;
		}

		$Contact = new Contact(
			$user['email'],
			'',
			$user['name'],
			$user['phone'],
			'',
			'',
			'',
			$user['post_id']
		);

		$Address = new Address(
			$user['address'],
			$user['postcode'],
			$user['city'],
			$user['billing_country']
		);

		$Contact->setAddress($Address);

		$Options = new Options();

		if (isset($this->tags)) {
			$Options->setTags($this->tags);
		}

		$contactStatus = isset($this->contactOptStatus)
			? $this->contactOptStatus
			: '';

		switch ($contactStatus) {
			case "optIn":
				$Options->setForceOptIn(true);
				$Options->setForceOptOut(false);
				break;
			case "optOut":
				$Options->setForceOptIn(false);
				$Options->setForceOptOut(true);
				break;
			default:
				$Options->setForceOptIn(false);
				$Options->setForceOptOut(false);
				break;
		}

		$Contact->setOptions($Options);
		$contact = $Contact->getForExport();

		return $contact;
	}

	/**
	 * @param mixed $page
	 * @throws SalesManagoException
	 * @return mixed
	*/
    public function getEvents($page = 1)
    {
        try {
            $data = [];

            $argGetOrders = [
                'status' => 'wc-completed',
                'limit' => $this->packageLimit,
                'page' => $page
            ];

            if ($this->advancedExport
                && !empty($this->dateRange['from'])
                && !empty($this->dateRange['to'])
            ) {
                $argGetOrders['date_created'] = strtotime($this->dateRange['from']);
                $argGetOrders['date_created'] .= '...';
                $argGetOrders['date_created'] .= strtotime($this->dateRange['to']);
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
        } catch (\Exception $e) {
            throw new SalesManagoException($e->getMessage());
        }
    }

    public function translateEvent($extEvent)
    {
        if (empty($extEvent)) {
            return false;
        }

        $extEvent['date'] = $extEvent['date'] * 1000;

        $extEvent['location'] = $this->getStoreId(get_home_url());
        $extEvent['shopDomain'] = get_home_url();

        $translated = array('email' => $extEvent['email']);

        unset($extEvent['email']);
        unset($extEvent['order_id']);

        $translated['contactEvent'] = $extEvent;

        return $translated;
    }

    public function countOrders()
    {
        $countedOrders = 0;
        $doWhile = true;

        $args = [
            'status' => 'wc-completed',
            'limit' => 100,
            'page' => 1
        ];

        if ($this->advancedExport
            && isset($this->dateRange)) {
            $args['date_created'] = $this->dateRange['from'].'...'.$this->dateRange['to'];
        }

        while ($doWhile) {
            if ($orders = wc_get_orders($args)) {
                $countedOrders += count($orders);
                $args['page']++;
            } else {
                $doWhile = false;
            }
        }

        return $countedOrders;
    }
}
