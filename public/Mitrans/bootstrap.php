<?php
define('COCKPIT_DIR', './'); // Adjust the path as needed
$this->helpers['system']    = 'System\\Helper\\System';
$this->helpers['api']       = 'System\\Helper\\Api';
// Pastikan Composer Autoload sudah dipanggil
include_once(__DIR__.'/vendor/autoload.php');

// Include Cockpit Storage class
include_once(COCKPIT_DIR.'/lib/MongoLite/Client.php');
include_once(COCKPIT_DIR.'/lib/MongoLite/Database.php');
include_once(COCKPIT_DIR.'/lib/MongoLite/Collection.php');

// Load configuration
$config = include(__DIR__.'/config/config.php');

// Inisialisasi konfigurasi Midtrans
$serverKey = $config['server_key'];
$clientKey = $config['client_key'];
$isProduction = $config['is_production'];

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = $serverKey;
\Midtrans\Config::$clientKey = $clientKey;
\Midtrans\Config::$isProduction = $isProduction;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Menambahkan fungsi untuk membuat transaksi Midtrans
$this->module('mitrans')->extend([

    'createTransaction' => function(array $data) {
        // Membuat transaksi di Midtrans
        try {
            $transaction = \Midtrans\Snap::createTransaction($data);

            // Save transaction to a JSON file
            $transactionData = [
                'order_id' => $data['transaction_details']['order_id'],
                'amount' => $data['transaction_details']['gross_amount'],
                'customer' => $data['customer_details']['first_name'] . ' ' . $data['customer_details']['last_name'],
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'],
                'payment_type' => $data['payment_type'],
                'created' => time()
            ];

            $transactions = [];
            $filePath = __DIR__ . '/transactions.json';
            if (file_exists($filePath)) {
                $transactions = json_decode(file_get_contents($filePath), true);
            }
            $transactions[] = $transactionData;
            file_put_contents($filePath, json_encode($transactions));

            // Mengembalikan hasil transaksi
            return [
                'token' => $transaction->token,
                'redirect_url' => $transaction->redirect_url
            ];
        } catch (\Exception $e) {
            // Log the error message
            error_log('Midtrans createTransaction error: ' . $e->getMessage());
            // Menangani error jika terjadi masalah saat membuat transaksi
            return ['error' => $e->getMessage()];
        }
    },
    'getTransactions' => function() {
        $filePath = __DIR__ . '/transactions.json';
        if (file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }
        return [];
    },
    'checkTransactionStatus' => function($orderId) {
        try {
            $status = \Midtrans\Transaction::status($orderId);

            $filePath = __DIR__ . '/transactions.json';
            if (file_exists($filePath)) {
                $transactions = json_decode(file_get_contents($filePath), true);
                foreach ($transactions as &$transaction) {
                    if ($transaction['order_id'] === $orderId) {
                        $transaction['status'] = is_object($status) && isset($status->transaction_status) ? $status->transaction_status : 'unknown';
                        file_put_contents($filePath, json_encode($transactions));
                        return $transaction;
                    }
                }
            }
            return ['error' => 'Transaction not found'];
        } catch (\Exception $e) {
            error_log('Midtrans checkTransactionStatus error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

]);

$this->on(
    'app.admin.init', function () {
        include __DIR__.'/admin.php';
    }
);

// Memasukkan kode API
$this->on('app.api.request', function() {
    include(__DIR__.'/api.php');
});

// Register API endpoint for transactions
$this->on('cockpit.rest.init', function($routes) {
    $routes['mitrans'] = 'Mitrans\\Controller\\Mitrans';
});

