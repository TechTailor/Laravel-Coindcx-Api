<?php

namespace TechTailor\CoinDCXApi\Traits;

trait HandlesResponseErrors
{
    private function handleError($response)
    {
        // Set a default error.
        $error = [
            'code'    => '1000',
            'error'   => 'Invalid',
            'message' => 'Unable to identify the type of error.',
        ];

        // Return server related errors (500 range).
        if ($response->serverError()) {
            // TBA
        }
        // Return client related errors.
        elseif ($response->clientError()) {
            // If client error has a response code.
            if (isset($response['code'])) {
                // Switch between known CoinDCX error codes.
                switch ($response['code']) {
                    case '404':
                            $error = [
                                'code'    => '404',
                                'error'   => 'Resource Not Found',
                                'message' => 'We could not locate the resource or the endpoint given.',
                            ];
                            break;
                    case '401':
                        $error = [
                            'code'    => '401',
                            'error'   => 'Invalid Credentials',
                            'message' => 'Your API Request Signature is invalid.',
                        ];
                        break;
                }
            } else {
                // If client error a response status.
                if ($response->status() === 403) {
                    $error = [
                        'code'    => '403',
                        'error'   => 'Forbidden',
                        'message' => "You don't have permission to access this resouce.",
                    ];
                }
            }

            return $error;
        }
    }
}
