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
    },
    'generateInvoice' => function ($order_id) {

        $data = [];

        foreach (json_decode(file_get_contents(__DIR__.'/transactions.json'), true) as $value) {
            if ($value['order_id'] === $order_id) {
                $data = $value;
            }
        }

        if (empty($data)) {
            error_log('data was not found...');
        }

        // $data = $this->param('data');

        // if (!isset($data['order_id']) || !isset($data['invoice_number']) || !isset($data['customer_details'])) {
        //     return ['status' => 'error', 'message' => 'Order ID, invoice number, and customer details are required'];
        // }

        // Save invoice to a JSON file
        $invoiceData = [
            'invoice_id' => uniqid(),
            'order_id' => $data['order_id'] ?? 'null',
            'invoice_number' => $data['invoice_number'] ?? 'null',
            'due_date' => $data['due_date'] ?? 'null',
            'invoice_date' => $data['invoice_date'] ?? 'null',
            'customer_details' => $data['customer_details'] ?? [
                'name' => '',
                'email' => '',
                'phone' => '',
            ],
            'payment_type' => $data['payment_type'] ?? 'null',
            'reference' => $data['reference'] ?? 'null',
            'item_details' => $data['item_details'] ?? [
                [
                    'description' => '',
                    'price' => '',
                ],
            ],
            'notes' => $data['notes'] ?? 'null',
            'virtual_accounts' => $data['virtual_accounts'] ?? 'null',
            'amount' => $data['amount'] ?? [
                'vat' => '',
                'discount' => '',
                'shipping' => '',
            ],
            'status' => 'generated',
            'created' => time()
        ];

        $invoices = [];
        $filePath = __DIR__ . '/invoices.json';
        if (file_exists($filePath)) {
            $invoices = json_decode(file_get_contents($filePath), true);
        }
        $invoices[] = $invoiceData;
        file_put_contents($filePath, json_encode($invoices));

        // Generate PDF
        $pdf = new \Mpdf\Mpdf();
        // $html = $this->renderView('mitrans:views/invoice.php', ['invoice' => $invoiceData]);
        $html = $this->app->render(__DIR__.'/views/invoice.php', ['invoice' => $invoiceData]);
        $pdf->WriteHTML($html);
        $pdfDir = __DIR__ . '/generateInvoicePDF/';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        $pdfFilePath = $pdfDir . $invoiceData['invoice_id'] . '.pdf';
        $pdf->Output($pdfFilePath, 'F');

        return ['status' => 'success', 'message' => 'Invoice generated', 'pdf_url' => $pdfFilePath];
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

