<?php

/**
 *
 * @OA\Tag(
 *   name="Mitrans",
 *   description="Payment gateway integration with Midtrans",
 * )
 */

$this->on('restApi.config', function($restApi) {

    // Endpoint untuk Midtrans checkout
    $restApi->addEndPoint('/paymentgateway/checkout', [

        /**
         * @OA\Post(
         *     path="/paymentgateway/checkout",
         *     tags={"Mitrans"},
         *     @OA\RequestBody(
         *         description="Transaction details",
         *         required=true,
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="transaction_details", type="object",
         *                 @OA\Property(property="order_id", type="string"),
         *                 @OA\Property(property="gross_amount", type="integer")
         *             ),
         *             @OA\Property(property="customer_details", type="object",
         *                 @OA\Property(property="first_name", type="string"),
         *                 @OA\Property(property="last_name", type="string"),
         *                 @OA\Property(property="email", type="string"),
         *                 @OA\Property(property="phone", type="string")
         *             ),
         *             @OA\Property(property="item_details", type="array",
         *                 @OA\Items(type="object",
         *                     @OA\Property(property="id", type="string"),
         *                     @OA\Property(property="price", type="integer"),
         *                     @OA\Property(property="quantity", type="integer"),
         *                     @OA\Property(property="name", type="string")
         *                 )
         *             ),
         *             @OA\Property(property="shipping_address", type="object",
         *                 @OA\Property(property="address", type="string"),
         *                 @OA\Property(property="city", type="string"),
         *                 @OA\Property(property="postal_code", type="string"),
         *                 @OA\Property(property="country_code", type="string")
         *             ),
         *             @OA\Property(property="payment_type", type="string"),
         *             @OA\Property(property="credit_card", type="object",
         *                 @OA\Property(property="secure", type="boolean")
         *             )
         *         )
         *     ),
         *     @OA\Response(response="200", description="Transaction created successfully", @OA\JsonContent(
         *         type="object",
         *         @OA\Property(property="token", type="string"),
         *         @OA\Property(property="redirect_url", type="string")
         *     )),
         *     @OA\Response(response="412", description="Order ID, amount, and customer details are required", @OA\JsonContent(
         *         type="object",
         *         @OA\Property(property="error", type="string")
         *     )),
         * )
         */
        'POST' => function($params, $app) {
            // Panggil Midtrans untuk proses pembayaran
            $mitrans = $this->module('mitrans');

            $data = $app->param('data');

            if (!isset($data['transaction_details']['order_id']) || 
                !isset($data['transaction_details']['gross_amount']) || 
                !isset($data['customer_details']) || 
                !isset($data['shipping_address'])) {
                return $app->stop(['error' => 'Order ID, amount, customer details, and shipping address are required'], 412);
            }

            // Menggunakan Midtrans untuk membuat transaksi
            $transaction = $mitrans->createTransaction($data);

            if (isset($transaction['error'])) {
                // Log the error message
                error_log('Midtrans API error: ' . $transaction['error']);
                return $app->stop(['error' => $transaction['error']], 412);
            }

            // Mengembalikan data transaksi
            return [
                'token' => $transaction['token'],
                'redirect_url' => $transaction['redirect_url']
            ];
        }

    ]);

    // Endpoint untuk mengecek status transaksi
    $restApi->addEndPoint('/mitrans/checkStatus', [
        'GET' => function($params, $app) {
            $orderId = $app->param('order_id');
            if (!$orderId) {
                return $app->stop(['error' => 'Order ID is required'], 412);
            }
            $transaction = $this->module('mitrans')->checkTransactionStatus($orderId);
            return $transaction;
        }
    ]);

    // Endpoint untuk mendapatkan transaksi
    $restApi->addEndPoint('/mitrans/getTransactions', [
        'GET' => function($params, $app) {
            $transactions = $this->module('mitrans')->getTransactions();
            return $transactions;
        }
    ]);

});
