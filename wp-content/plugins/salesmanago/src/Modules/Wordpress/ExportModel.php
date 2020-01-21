<?php

namespace bhr\Modules\Wordpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Model\AbstractExportModel;

use bhr\Helper\Contact\Contact;
use bhr\Helper\Contact\Address;
use bhr\Helper\Contact\Options;
use SALESmanago\Exception\Exception;
use SALESmanago\Exception\SalesManagoException;

class ExportModel extends AbstractExportModel
{
    public function getCustomers($page = '0')
    {
        $offset = $page * $this->packageLimit;
        $upsertDetails = array();

	    if (isset($this->dateRange['from'])
	        && isset($this->dateRange['to'])
	    ) {
		    $users = $this->getUsersDataByDateRange(
		    	$this->dateRange['from'],
			    $this->dateRange['to']
		    );
	    } else {
		    $args = [
			    'count_total' => true,
			    'role__in' =>
				    [
					    'subscriber',
					    'author',
					    'editor',
					    'contributor'
				    ],
			    'fields' => 'all',
			    'number' => $this->packageLimit,
			    'offset' => $offset
		    ];

		    $args['offset'] = $offset;

		    $users = get_users($args);
	   }

        foreach ($users as $user) {
            $contact = $this->translateCustomer($user);
            if (!empty($contact)) {
                array_push($upsertDetails, $contact);
            }
        }

        return $upsertDetails;
    }

	/**
	 * @param string $dateFrom - date without time;
	 * @param  string $dateTo - date without time;
	 * @return array $users - users data;
	 */
	public function getUsersDataByDateRange($dateFrom, $dateTo) {
	    $users = array();
	    $usersIds = $this->getUsersIdByDateRange($dateFrom, $dateTo);
	    foreach ($usersIds as $userData) {
		    $userData = get_user_by('id', $userData['ID']);

		    if (!in_array( 'customer', $userData->roles, true )
		        && !in_array( 'administrator', $userData->roles, true )) {
			    $users[] = $userData;
		    }
	    }

	    return $users;
	}

	/**
	 * @param string $dateFrom - date without time;
	 * @param  string $dateTo - date without time;
	 * @return array $usersData - with users ids;
	*/
	public function getUsersIdByDateRange($dateFrom, $dateTo) {
		$this->db->set_blog_id( get_current_blog_id() );
		$usersData = $this->db->get_results(
			$this->db->prepare(
				"SELECT ID FROM {$this->db->users} date WHERE `user_registered` BETWEEN %s AND %s",
				$dateFrom,
				$dateTo
			),
			ARRAY_A
		);

		return $usersData;
	}

	/**
	 * @param array
	 * @return array
	 * @throws
	 */
	public function translateCustomer($user = array())
    {
        $email = $user->get('user_email');

        if (empty($email)) {
            return false;
        }

        $Contact = new Contact(
            $email,
            '',
            array(
                $user->get('first_name'),
                $user->get('last_name')
            ),
            $user->get('billing_phone'),
            '',
            '',
            '',
            $user->get('id')
        );

        $Address = new Address(
            array(
                $user->get('billing_address_1'),
                $user->get('billing_address_2')
            ),
            $user->get('billing_postcode'),
            $user->get('billing_city'),
            $user->get('billing_country')
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
    * @throws SalesManagoException;
     */
    public function countCustomers()
    {
    	try {
		    $cContacts = 0;
		    if ( $this->advancedExport && isset( $this->dateRange ) ) {
			    $this->db->set_blog_id( get_current_blog_id() );
			    $usersData = $this->getUsersIdByDateRange($this->dateRange['from'], $this->dateRange['to']);

			    foreach ($usersData as $userData) {
				    $meta = get_userdata( $userData['ID'] );

				    if (!in_array( 'customer', $meta->roles, true )
				        && !in_array( 'administrator', $meta->roles, true )) {
					    $cContacts++;
				    }
			    }

			    return $cContacts;
		    } else {
			    $cContacts = count_users( 'time', get_current_blog_id() );
			    $cContacts = (int) $cContacts['total_users'] - (int) $cContacts['avail_roles']['administrator'] - (int) $cContacts['avail_roles']['customer'];
		    }

		    return $cContacts;
	    } catch (\Exception $e) {
		    throw new SalesManagoException($e->getMessage());
	    }
    }
}
