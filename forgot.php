<?php
// This file serves as an example; modify it to your liking
require __DIR__ . '/vendor/autoload.php';

use src\exceptions\tokens\RelationShipTokenException;
use src\exceptions\tokens\TokenExpiredException;
use src\exceptions\tokens\TokenNotFoundException;
use src\exceptions\tokens\TokenException;

use src\Logger;

$configPath = __DIR__ . '/config/config.php';

$dulceAuth = new src\DulceAuth($configPath);

try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenPassword($token, $userId)) {
            $dulceAuth->insertNewPassword('new password here', $userId);
            echo 'Password changed successfully';
        }
    } else {
        echo 'The token or userID is empty';
    }
} catch (RelationShipTokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenExpiredException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
