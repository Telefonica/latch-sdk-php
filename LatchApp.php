<?php

/*
  Latch PHP SDK - Set of  reusable classes to  allow developers integrate Latch on
  their applications.
  Copyright (C) 2013 Eleven Paths

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once('LatchAuth.php');
require_once('LatchResponse.php');

/**
 * This class model the API for Applications. Every action here is related to an Application. This
 * means that a LatchApp object should use a pair ApplicationId/Secret obtained from the Application page of the Latch Website.
 */
class LatchApp extends LatchAuth {

	/**
	 * Create an instance of the class with the Application ID and secret obtained from Eleven Paths
	 * @param $appId
	 * @param $secretKey
	 */
	function __construct($appId, $secretKey) {
        parent::__construct($appId, $secretKey);
	}

	public function pairWithId($accountId) {
		return $this->HTTP_GET_proxy(self::$API_PAIR_WITH_ID_URL . "/" . $accountId);
	}

	public function pair($token) {
		return $this->HTTP_GET_proxy(self::$API_PAIR_URL . "/" . $token);
	}

    public function status($accountId, $silent = false, $nootp = false) {
        if ($silent && $nootp) {
            return $this->HTTP_GET_proxy(self::$API_CHECK_STATUS_URL . "/" . $accountId . "/nootp/silent");
        }
        if ($silent) {
            return $this->HTTP_GET_proxy(self::$API_CHECK_STATUS_URL . "/" . $accountId . "/silent");
        }
        if ($nootp) {
            return $this->HTTP_GET_proxy(self::$API_CHECK_STATUS_URL . "/" . $accountId . "/nootp");
        }
        return $this->HTTP_GET_proxy(self::$API_CHECK_STATUS_URL . "/" . $accountId);
    }

    public function operationStatus($accountId, $operationId, $silentAndNootp = false) {
        if ($silentAndNootp) {
            return $this->HTTP_GET_proxy(self::$API_CHECK_STATUS_URL . "/" . $accountId . "/op/" . $operationId . "/nootp/silent");
        } else {
            return $this->HTTP_GET_proxy(self::$API_CHECK_STATUS_URL . "/" . $accountId . "/op/" . $operationId);
        }
    }

	public function unpair($accountId) {
		return $this->HTTP_GET_proxy(self::$API_UNPAIR_URL . "/" . $accountId);
	}

	public function lock($accountId, $operationId=null){
		if ($operationId == null){
			return $this->HTTP_POST_proxy(self::$API_LOCK_URL . "/" . $accountId, array());
		}else{
			return $this->HTTP_POST_proxy(self::$API_LOCK_URL . "/" . $accountId . "/op/" . $operationId, array());
		}
	}

	public function unlock($accountId, $operationId=null){
		if ($operationId == null){
			return $this->HTTP_POST_proxy(self::$API_UNLOCK_URL . "/" . $accountId, array());
		}else{
			return $this->HTTP_POST_proxy(self::$API_UNLOCK_URL . "/" . $accountId . "/op/" . $operationId, array());
		}
	}

	public function history($accountId, $from=0, $to=null) {
		if ($to == null){
			$date = time();
			$to = $date*1000;
		}
		return $this->HTTP_GET_proxy(self::$API_HISTORY_URL . "/" . $accountId . "/" . $from . "/" . $to);
	}

	public function createOperation($parentId, $name, $twoFactor, $lockOnRequest){
		$data = array(
			'parentId' => urlencode($parentId),
			'name' => urlencode($name),
			'two_factor' => urlencode($twoFactor),
			'lock_on_request' => urlencode($lockOnRequest));
		return $this->HTTP_PUT_proxy(self::$API_OPERATION_URL, $data);
	}

	public function removeOperation($operationId){
		return $this->HTTP_DELETE_proxy(self::$API_OPERATION_URL . "/" . $operationId);
	}

	public function updateOperation($operationId, $name, $twoFactor, $lockOnRequest){
		$data = array(
			'name' => urlencode($name),
			'two_factor' => urlencode($twoFactor),
			'lock_on_request' => urlencode($lockOnRequest));
		return $this->HTTP_POST_proxy(self::$API_OPERATION_URL . "/" . $operationId, $data);
	}

	public function getOperations($operationId=null){
		if ($operationId == null){
			return $this->HTTP_GET_proxy(self::$API_OPERATION_URL);
		}else{
			return $this->HTTP_GET_proxy(self::$API_OPERATION_URL . "/" . $operationId);
		}
	}
}
