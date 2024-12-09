<?php

namespace Mitrans\Controller;

use App\Controller\App;
use Mpdf\Mpdf;

class Mitrans extends App
{
    public function index()
    {
        $this->helper('theme')->favicon('mitrans:icon.svg');

        return $this->render('mitrans:views/index.php');
    }

    public function getTransactions() {
        // Fetch transactions from the JSON file
        $filePath = __DIR__ . '/../../transactions.json';
        if (file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }
        return [];
    }

    public function create() {
        if (!$this->app->helper('content.model')->exists("mitrans_transactions")) {
            $transactionsModel = [
                'name' => 'mitrans_transactions',
                'label' => 'Mitrans Transactions',
                'info' => 'This is a predefined transactions model by the Mitrans addon. DO NOT CHANGE THIS! It will certainly break the addon!',
                'type' => 'collection',
                'fields' => [
                    [
                        'name' => 'order_id',
                        'type' => 'text',
                        'label' => 'Order ID',
                        'info' => 'INTERNAL FIELD DO NOT CHANGE! This is the order ID of the transaction.',
                        'group' => '',
                        'i18n' => false,
                        'required' => false,
                        'multiple' => false,
                        'meta' => [],
                        'opts' => [
                            'multiline' => false,
                            'showCount' => true,
                            'readonly' => false,
                            'placeholder' => null,
                            'minlength' => null,
                            'maxlength' => null,
                            'list' => null
                        ]
                    ],
                    [
                        'name' => 'amount',
                        'type' => 'text',
                        'label' => 'Amount',
                        'info' => 'INTERNAL FIELD DO NOT CHANGE! This is the amount of the transaction.',
                        'group' => '',
                        'i18n' => false,
                        'required' => false,
                        'multiple' => false,
                        'meta' => [],
                        'opts' => [
                            'multiline' => false,
                            'showCount' => true,
                            'readonly' => false,
                            'placeholder' => null,
                            'minlength' => null,
                            'maxlength' => null,
                            'list' => null
                        ]
                    ],
                    [
                        'name' => 'customer',
                        'type' => 'text',
                        'label' => 'Customer',
                        'info' => 'INTERNAL FIELD DO NOT CHANGE! This is the customer of the transaction.',
                        'group' => '',
                        'i18n' => false,
                        'required' => false,
                        'multiple' => false,
                        'meta' => [],
                        'opts' => [
                            'multiline' => false,
                            'showCount' => true,
                            'readonly' => false,
                            'placeholder' => null,
                            'minlength' => null,
                            'maxlength' => null,
                            'list' => null
                        ]
                    ],
                    [
                        'name' => 'status',
                        'type' => 'text',
                        'label' => 'Status',
                        'info' => 'INTERNAL FIELD DO NOT CHANGE! This is the status of the transaction.',
                        'group' => '',
                        'i18n' => false,
                        'required' => false,
                        'multiple' => false,
                        'meta' => [],
                        'opts' => [
                            'multiline' => false,
                            'showCount' => true,
                            'readonly' => false,
                            'placeholder' => null,
                            'minlength' => null,
                            'maxlength' => null,
                            'list' => null
                        ]
                    ]
                ],
                'preview' => [],
                'group' => '',
                'meta' => null,
                'color' => '#e01b24',
                'revisions' => false
            ];

            if (!$this->isAllowed("content/:models/manage") && !$this->isAllowed("content/{$transactionsModel}/manage")) {
                return $this->stop(401);
            }

            $transactionsModel = $this->module('content')->saveModel("mitrans_transactions", $transactionsModel);
        }

        return "OK";
    }

    public function get() {
        if (!$this->app->helper('content.model')->exists("mitrans_transactions")) {
            return;
        }
        
        $items = $this->module('content')->items("mitrans_transactions");

        return $items;
    }

    public function generateInvoice() {
        $data = $this->param('data');

        if (!isset($data['order_id']) || !isset($data['invoice_number']) || !isset($data['customer_details'])) {
            return ['status' => 'error', 'message' => 'Order ID, invoice number, and customer details are required'];
        }

        // Save invoice to a JSON file
        $invoiceData = [
            'invoice_id' => uniqid(),
            'order_id' => $data['order_id'],
            'invoice_number' => $data['invoice_number'],
            'due_date' => $data['due_date'],
            'invoice_date' => $data['invoice_date'],
            'customer_details' => $data['customer_details'],
            'payment_type' => $data['payment_type'],
            'reference' => $data['reference'],
            'item_details' => $data['item_details'],
            'notes' => $data['notes'],
            'virtual_accounts' => $data['virtual_accounts'],
            'amount' => $data['amount'],
            'status' => 'generated',
            'created' => time()
        ];

        $invoices = [];
        $filePath = __DIR__ . '/../../invoices.json';
        if (file_exists($filePath)) {
            $invoices = json_decode(file_get_contents($filePath), true);
        }
        $invoices[] = $invoiceData;
        file_put_contents($filePath, json_encode($invoices));

        // Generate PDF
        $pdf = new Mpdf();
        $html = $this->renderView('mitrans:views/invoice.php', ['invoice' => $invoiceData]);
        $pdf->WriteHTML($html);
        $pdfDir = __DIR__ . '/../../generateInvoicePDF/';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        $pdfFilePath = $pdfDir . $invoiceData['invoice_id'] . '.pdf';
        $pdf->Output($pdfFilePath, 'F');

        return ['status' => 'success', 'message' => 'Invoice generated', 'pdf_url' => $pdfFilePath];
    }

    private function getTransactionById($orderId) {
        // Fetch transaction by order ID
        $transactions = $this->getTransactions();
        foreach ($transactions as $transaction) {
            if ($transaction['order_id'] === $orderId) {
                return $transaction;
            }
        }
        return null;
    }
}