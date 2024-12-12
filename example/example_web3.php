
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
            echo "Your latch is lock and you can not be allowed to perform action\n";
        } else {
            echo "Error processing the response\n";
            exit(1);
        }
    }
    
    // To run an example just fill in the value of the constants and run the example.
    $appId = "<MY_APPID>";
    $secretKey = "<MY_SECRETKEY>";
    
    $api = new Latch($appId, $secretKey);

    $paringCode = readInput('Generated pairing token from the user account');
    $wallet = readInput('Your public key of your wallet');
    $signature = readInput('Sign the message "Latch-Web3" with your wallet');
    $commonName = readInput('Do you want a alias for the pairing, it will be showed in admin panels like Latch Support Tool (L:ST). Optional, blank if none ') ?: null;

    $response = $api->pair($paringCode, $wallet, $signature, $commonName);
    exitIfErrorResponse($response, "pair");
    printResponse($response);

    echo "Store the accountId for future uses\n";
    $accountId = $response->getData()->accountId;

    //Check status account
    //When the state is checked, it can be checked at different levels. Application, Operation or Instance
    checkStatus($api, $accountId, $appId);