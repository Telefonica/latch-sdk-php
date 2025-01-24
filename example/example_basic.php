
<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    use Telefonica\Latch\Latch;


    function readInput($message) {
        echo $message . ": ";
        $input = trim(fgets(STDIN));
        return $input;
    }

    function printResponse($response) {
        echo "Data:\n";
        $dataResponse = $response->getData();
        echo print_r($dataResponse) . "\n";
    }

    function exitIfErrorResponse($response, $methodName) {
        if (checkErrorResponse($response, $methodName)) {
            exit(1);
        }
    }

    function checkErrorResponse($response, $methodName) {
        $errorResponse = $response->getError();
        $result = isset($errorResponse) && !is_null($errorResponse);
        if ($result) {
            $errorCode = $errorResponse->getCode();
            $errorMessage = $errorResponse->getMessage();
            echo "Error in $methodName request with error_code: $errorCode and message: $errorMessage \n"; 
        }
        return $result;
    }
    

    function checkStatus($api, $accountId, $elementId) {
        // Fetch the response
        $response = $api->status($accountId);

        // Check for errors
        exitIfErrorResponse($response,"checkStatus");

        $responseData = $response->getData();
        // Evaluate the status
        if ($responseData->operations->$elementId->status === 'on') {
            echo "Your latch is open and you are able to perform action\n";
        } elseif ($responseData->operations->$elementId->status === 'off') {
            echo "Your latch is locked and you are not allowed to perform action\n";
        } else {
            echo "Error processing the response\n";
            exit(1);
        }
    }
    
    // To run an example just fill in the value of the constants and run the example.
    $appId = "Dnc4ftijVZrpyzj9RpWA";
    $secretKey = "dBUs8Rgbxk8DPX8uyV2JrMnTvVTYghuNtiQTLWzN";
    
    $api = new Latch($appId, $secretKey);

    // $paringCode = readInput('Generated pairing token from the user account');
    // $commonName = readInput('Do you want a alias for the pairing, it will be showed in admin panels like Latch Support Tool (L:ST). Optional, blank if none ') ?: null;

    // $response = $api->pair($paringCode, null, null, $commonName);
    // exitIfErrorResponse($response, "pair");
    // printResponse($response);

    // echo "Store the accountId for future uses\n";
    // $accountId = $response->getData()->accountId;

    // //Check status account
    // //When the state is checked, it can be checked at different levels. Application, Operation or Instance
    // checkStatus($api, $accountId, $appId);

    // //Lock the account
    // $response = $api->lock($accountId);
    // exitIfErrorResponse($response, "lock");
    // //Lock responses is empty if all is correct
    // checkStatus($api, $accountId, $appId);


    // //Unlock the account
    // $response = $api->unlock($accountId);
    // exitIfErrorResponse($response, "unlock");
    // //Unlock responses is empty if all is correct
    // checkStatus($api, $accountId, $appId);


    // //Unpairing process
    // $response = $api->unpair($accountId);
    // exitIfErrorResponse($response, "unpair");
    // //Unpair responses is empty if all is correct
    // checkStatus($api, $accountId, $appId);

    checkStatus($api, "w4zdL8yC8kDgrLDYRLU3iMVTMWDd2DAdRckNNszuVXsNArVGR32Qy3fFKik4ubkz", $appId);