<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
	/**
	 * Generate response with custom HTTP status and message
	 * @param  String  $message custom message of response
	 * @param  integer $code    HTTP status code, default : 200 (OK)
	 * @return Illuminate\Http\Response           generated response
	 */
	public function sendResponse($message, $code = 200) {
		return response()->json([
		    'message' => $message
		], $code);
	}

	/**
	 * Generate response with HTTP status 400
	 * @param  String $message custom message of bad request
	 * @return Illuminate\Http\Response          generated response
	 */
    public function sendInvalidRequest($message = null) {
    	return $this->sendResponse($message ?? 'Invalid request', 400);
    }

    /**
     * Generate response with HTTP status 401
     * @param  String $message custom message of unauthorized request
     * @return Illuminate\Http\Response          generated response
     */
    public function sendUnauthorized($message = null) {
    	return $this->sendResponse($message ?? 'Unauthorized', 401);
    }

    /**
     * Generate response with HTTP status 404
     * @param  String $message custom message of resources not found
     * @return Illuminate\Http\Response          generated response
     */
    public function sendResourcesNotFound($message = null) {
    	return $this->sendResponse($message ?? 'Resources not found', 404);
    }

    /**
     * Generate response with HTTP status 500
     * @param  String $message custom message of error
     * @return Illuminate\Http\Response          generated response
     */
    public function sendError($message = null) {
    	return $this->sendResponse($message ?? 'There\'s something wrong. Please try again later.', 500);
    }
}
