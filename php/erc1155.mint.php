<?php

#test send ether via hardhat


/*

composer require web3p/web3.php
composer require web3p/ethereum-tx

*/

require 'vendor/autoload.php';


use Web3\Contract;
use Web3\Utils;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '/.env');
// $dotenv->load();
$timeout = 30;
$web3 = new Web3(new HttpProvider(new HttpRequestManager('http://localhost:8545', $timeout)));

#erc1155 - use contract address from deployed contract in hardhat
$contractAddress = "0x5FbDB2315678afecb367f032d93F642f64180aa3";


#to address, use hardhat account 1
$destinationAddress = "0x70997970c51812dc3a010c7d01b50e0d17dc79c8";

#from, use hardhat account 0
$fromAddress = "0xf39fd6e51aad88f6f4ce6ab8827279cfffb92266";
$fromAddressPrivateKey = "0xac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80";



$secondsToWaitForReceiptString = 300;
$secondsToWaitForReceipt = intval($secondsToWaitForReceiptString);

$factorToMultiplyGasEstimateString = 50000;
$factorToMultiplyGasEstimate = intval($factorToMultiplyGasEstimateString);

$amountIn = Utils::toWei('50000000', 'ether');
$amountInWholeNumber = Utils::toBn($amountIn);

#hardhat chainID
$chainId = 31337;


#make sure this abi path location exists
$abi = file_get_contents(__DIR__ . '/resources/MyToken.json');

$provider = $web3->getProvider();



################

$contract = new Contract($provider, $abi);

$eth = $contract->eth;

$goldId = 0;

$contract->at($contractAddress)->call('balanceOf', $fromAddress, $goldId, function ($err, $results) use ($contract) {
    if ($err !== null) {
        echo $err->getMessage() . PHP_EOL;
    }
    if (isset($results)) {
        foreach ($results as &$result) {
            $bn = Utils::toBn($result);
            echo 'BEFORE fromAddress balance ' . $bn->toString() . PHP_EOL;
        }
    }
});

$contract->at($contractAddress)->call('balanceOf', $destinationAddress, $goldId, function ($err, $results) use ($contract) {
    if ($err !== null) {
        echo $err->getMessage() . PHP_EOL;
    }
    if (isset($results)) {
        foreach ($results as &$result) {
            $bn = Utils::toBn($result);
            echo 'BEFORE destinationAddress balance ' . $bn->toString() . PHP_EOL;
        }
    }
});

$contract->at($contractAddress)->send('mint', $fromAddress, $goldId, 1, "0x000", [
    'from' => $fromAddress,
    'gas' => '0x200b20'
], function ($err, $result) use ($contract) {
    if ($err !== null) {
        throw $err;
    }
    if ($result) {
        echo "\nTransaction has made:) id: " . $result . "\n";
    }
    $transactionId = $result;

    $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
        if ($err !== null) {
            throw $err;
        }
        if ($transaction) {
            echo "\nTransaction has mind:) block number: " . $transaction->blockNumber . "\nTransaction dump:\n";
            // var_dump($transaction);
        }
    });
});


$contract->at($contractAddress)->call('balanceOf', $fromAddress, 0, function ($err, $results) use ($contract) {
    if ($err !== null) {
        echo $err->getMessage() . PHP_EOL;
    }
    if (isset($results)) {
        foreach ($results as &$result) {
            $bn = Utils::toBn($result);
            echo 'AFTER minting fromAddress balance ' . $bn->toString() . PHP_EOL;
        }
    }
});

$contract->at($contractAddress)->call('balanceOf', $destinationAddress, 0, function ($err, $results) use ($contract) {
    if ($err !== null) {
        echo $err->getMessage() . PHP_EOL;
    }
    if (isset($results)) {
        foreach ($results as &$result) {
            $bn = Utils::toBn($result);
            echo 'AFTER minting destinationAddress balance ' . $bn->toString() . PHP_EOL;
        }
    }
});

$contract->at($contractAddress)->send('safeTransferFrom', $fromAddress,$destinationAddress, $goldId, 1, "0x000", [
    'from' => $fromAddress,
    'gas' => '0x200b20'
], function ($err, $result) use ($contract) {
    if ($err !== null) {
        throw $err;
    }
    if ($result) {
        echo "\nTransaction has made:) id: " . $result . "\n";
    }
    $transactionId = $result;

    $contract->eth->getTransactionReceipt($transactionId, function ($err, $transaction) {
        if ($err !== null) {
            throw $err;
        }
        if ($transaction) {
            echo "\nTransaction has mind:) block number: " . $transaction->blockNumber . "\nTransaction dump:\n";
            // var_dump($transaction);
        }
    });
});

$contract->at($contractAddress)->call('balanceOf', $fromAddress, 0, function ($err, $results) use ($contract) {
    if ($err !== null) {
        echo $err->getMessage() . PHP_EOL;
    }
    if (isset($results)) {
        foreach ($results as &$result) {
            $bn = Utils::toBn($result);
            echo 'AFTER transfer fromAddress balance ' . $bn->toString() . PHP_EOL;
        }
    }
});

$contract->at($contractAddress)->call('balanceOf', $destinationAddress, 0, function ($err, $results) use ($contract) {
    if ($err !== null) {
        echo $err->getMessage() . PHP_EOL;
    }
    if (isset($results)) {
        foreach ($results as &$result) {
            $bn = Utils::toBn($result);
            echo 'AFTER transfer destinationAddress balance ' . $bn->toString() . PHP_EOL;
        }
    }
});

##########