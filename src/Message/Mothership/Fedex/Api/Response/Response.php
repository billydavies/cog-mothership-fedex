<?php

namespace Message\Mothership\Fedex\Response;

abstract class Response
{

	protected $_request;
	protected $_response;
	protected $_notifications;
	protected $_successful = false;

	public function __construct($response, Request $request)
	{
		$this->_request = $request;
		$this->_response = $response;
		$this->_loadNotifications();
		$this->_checkStatus();
		if (!$this->isSuccessful()) {
			// STRIP NAMESPACES FROM CLASS NAME
			$class = explode('\\', get_class($this));
			throw new Exception(end($class) . ' request failed.', Exception::REQUEST_FAILED, null, $this->_notifications);
		}
		$this->_validate();
	}

	public function isSuccessful()
	{
		return $this->_successful;
	}

	public function getNotifications()
	{
		return $this->_notifications;
	}

	protected function _loadNotifications()
	{
		// IF ONLY ONE Notification RETURNED, IT'S NOT RETURNED IN
		// AN ARRAY FOR SOME REASON. THIS FIXES THAT.
		if (isset($this->_response->Notifications) && is_object($this->_response->Notifications)) {
			$this->_response->Notifications = array($this->_response->Notifications);
		}
		foreach ($this->_response->Notifications as $notification) {
			$this->_notifications[] = Notification::loadFromResponse($notification);
		}
	}

	protected function _checkStatus()
	{
		if ($this->_response->HighestSeverity === 'SUCCESS') {
			$this->_successful = true;
		}
		return $this->_successful;
	}

	abstract protected function _validate();

}