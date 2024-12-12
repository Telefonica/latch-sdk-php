
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

    $paringCode = readInput('Enter the name displayed for the totp');
    $commonName = readInput('Do you want a alias for the pairing, it will be showed in admin panels like Latch Support Tool (L:ST). Optional, blank if none ') ?: null;

    $response = $api->createTotp($paringCode, $commonName);
    exitIfErrorResponse($response, "createTotp");
    printResponse($response);

    $totpId = $response->getData()->totpId;
    echo "Totp Id (Save it, you'll need it later): " . $totpId;
    echo "QR (Scan the QR with the app, you can open it with any browse):\n" . $response->getData()->qr . "\n";

    //Get info about a totp
    $response = $api->getTotp($totpId);
    exitIfErrorResponse($response, "getTotp");
    printResponse($response);


    //Validate totp
    $code = readInput("Enter the code generated");
    $response = $api->validateTotp($totpId, $code);
    //Validate responses is empty if code is valid
    $isCodeValid = !checkErrorResponse($response, "validateTotp");
    echo "Te code is valid: " . ($isCodeValid ? 'true' : 'false') . "\n";

    //Deleting totp
    $response = $api->deleteTotp($totpId);
    //deleteTotp responses is empty if all is correct
    $isTotpDeleted = !checkErrorResponse($response, "deleteTotp");
    echo "Te totpId has been deleted: " . ($isTotpDeleted ? 'true' : 'false') . "\n";
    $response = $api->getTotp($totpId);
    checkErrorResponse($response, "getTotp");