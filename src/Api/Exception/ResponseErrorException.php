<?php

namespace Message\Mothership\Fedex\Api\Exception;

use Message\Mothership\Fedex\Api\Response\ResponseInterface;

class ResponseErrorException extends ResponseException
{
	static public function createFromResponse(ResponseInterface $response)
	{
		$messages = array();

		foreach ($response->getNotifications()->getBySeverity(array('FAILURE', 'ERROR')) as $n) {
			$messages[] = sprintf('%s: (%s) %s', $n->severity, $n->code, $n->message);
		}

		$exception = new self(sprintf('FedEx API Request Failure: %s', implode(', ', $messages)));

		$exception->setResponse($response);

		return $exception;
	}
}