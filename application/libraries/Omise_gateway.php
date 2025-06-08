<?php
// application/libraries/Omise_gateway.php

require_once APPPATH . 'third_party/omise-php/lib/Omise.php'; // ตรวจสอบ Path ให้ถูกต้อง

class Omise_gateway implements PaymentGatewayInterface
{
    private $public_key;
    private $secret_key;
    private $mode; // live or test

    public function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('payment_model'); // โหลด Model เพื่อใช้งาน

        // กำหนด API keys จาก payment_config
        $configs = $ci->payment_model->get_payment_configs();
        if (isset($configs['omise_public_key'])) {
            $this->public_key = $configs['omise_public_key'];
            $this->secret_key = $configs['omise_secret_key'];
            // Omise doesn't directly have a 'mode' in config, it's determined by keys.
            // For simplicity, we assume test keys mean 'test' mode, live keys mean 'live'.
            // You might add a 'omise_mode' field to payment_config table if needed.
            // \Omise::setPublicKey($this->public_key); // Set public key here if needed for server-side
            // \Omise::setSecretKey($this->secret_key); // Set secret key here if needed for server-side
        }
    }

    // สำหรับใช้ set secret key ใน Controller ก่อนเรียก chargeToken
    public function set_secret_key($secret_key)
    {
        $this->secret_key = $secret_key;
        \Omise::setSecretKey($this->secret_key);
    }

    public function initPayment(array $payment_details)
    {
        // สำหรับ Omise Gateway, initPayment จะพิจารณาว่าเป็นการ์ดหรือ QR
        // หากเป็นการ์ด, จะ return 'form_display' เพื่อให้แสดงฟอร์ม
        // หากเป็น QR, จะสร้าง source และ return 'qrcode_display'

        $amount_in_cents = round($payment_details['amount'] * 100); // Omise expects amount in cents/satang

        // ตรวจสอบว่าเป็น Mobile Banking QR หรือ Credit Card
        // นี่คือ Logic ที่ Omise_gateway ควรมี
        // สมมติว่า Omise_gateway ถูกเรียกจาก selected_gateway='omise_card' หรือ 'omise_mobile_banking'
        // คุณอาจจะต้องส่ง parameter เพิ่มเติมเข้ามาใน initPayment เพื่อบอกว่าเป็นการชำระเงินแบบไหน

        if ($payment_details['gateway_type'] === 'card') { // ถ้ามาจาก 'omise_card'
            // ไม่ต้องทำอะไรมาก แค่บอกให้ Controller แสดงฟอร์ม
            return [
                'status' => 'form_display',
                'message' => 'Display Omise card form.',
                'transaction_id' => null // ยังไม่มี transaction ID จนกว่าจะ charge
            ];
        } elseif ($payment_details['gateway_type'] === 'mobile_banking_qr') { // ถ้ามาจาก 'omise_mobile_banking'
            try {
                \Omise::setSecretKey($this->secret_key); // Ensure secret key is set for API calls

                $source = \OmiseSource::create([
                    'amount' => $amount_in_cents,
                    'currency' => strtoupper($payment_details['currency']),
                    'type' => 'promptpay', // หรือ 'mobile_banking' ขึ้นอยู่กับ Omise API version
                                         // 'mobile_banking' covers PromptPay, KBank, SCB, etc.
                ]);

                if ($source['object'] === 'source' && $source['status'] === 'pending') {
                    $qr_image_svg = file_get_contents($source['qr']['url']); // ดึง SVG จาก Omise

                    return [
                        'status' => 'qrcode_display',
                        'message' => 'Omise QR Code generated.',
                        'transaction_id' => $source['id'], // Omise Source ID
                        'qr_data' => [
                            'qr_image_svg' => $qr_image_svg,
                            'amount' => $payment_details['amount'],
                            'transaction_id' => $source['id'],
                            'expiry_time' => strtotime($source['expires_at']), // Convert to Unix timestamp
                            'redirect_url' => $source['redirect']['mobile_banking_app_url'] ?? null // สำหรับ deep link
                        ]
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => $source['message'] ?? 'Failed to create Omise source.',
                        'transaction_id' => null
                    ];
                }
            } catch (Exception $e) {
                return [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'transaction_id' => null
                ];
            }
        }
    }


    public function chargeToken(array $charge_details, $our_payment_transaction_id)
    {
        try {
            // ตั้งค่า Secret Key อีกครั้งก่อน Charge (เพื่อให้แน่ใจว่าใช้ Key ที่ถูกต้อง)
            \Omise::setSecretKey($this->secret_key);

            $charge = \OmiseCharge::create([
                'amount' => round($charge_details['amount'] * 100), // Omise expects amount in cents/satang
                'currency' => strtoupper($charge_details['currency']),
                'card' => $charge_details['token'],
                'description' => $charge_details['description'],
                'return_uri' => base_url('payment/payment_callback/omise'), // URL ที่ Omise จะ Redirect กลับมา
                'metadata' => [ // ส่งข้อมูล Transaction ID ของเรากลับไป
                    'our_transaction_id' => $our_payment_transaction_id,
                    'order_id' => $charge_details['order_id'] ?? null
                ]
            ]);

            if ($charge['object'] === 'charge') {
                if ($charge['status'] === 'successful') {
                    return [
                        'status' => 'completed',
                        'message' => 'Payment successful.',
                        'transaction_id' => $charge['id'], // Omise Charge ID
                        'paid_amount' => $charge['amount'] / 100,
                        'metadata' => $charge // เก็บ Charge object ทั้งหมด
                    ];
                } elseif ($charge['status'] === 'pending' && isset($charge['authorize_uri'])) {
                    // สำหรับ 3D Secure หรือช่องทางที่ต้องมีการยืนยันเพิ่มเติม
                    return [
                        'status' => 'pending',
                        'message' => 'Payment requires further action (e.g., 3D Secure).',
                        'transaction_id' => $charge['id'],
                        'redirect_url' => $charge['authorize_uri']
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => $charge['failure_message'] ?? 'Payment failed.',
                        'transaction_id' => $charge['id'],
                        'metadata' => $charge
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to create Omise charge object.',
                    'transaction_id' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'transaction_id' => null
            ];
        }
    }

    public function handleCallback(array $callback_data)
    {
        // สำหรับ Omise Webhook/Callback
        // Omise จะส่ง Webhook มาที่ URL ที่เรากำหนดไว้
        // ต้องตรวจสอบ Signature ของ Webhook เพื่อความปลอดภัย
        try {
            \Omise::setSecretKey($this->secret_key); // Set secret key for Webhook verification

            $event = \OmiseEvent::retrieve(
                $callback_data['id'] // ID ของ Event ที่ Omise ส่งมา
            );

            // ตรวจสอบว่า Event ถูกต้องหรือไม่ (object = 'event')
            if ($event['object'] !== 'event') {
                 return ['status' => 'error', 'message' => 'Invalid Omise event object.'];
            }

            // ตรวจสอบ Event Type (เช่น charge.complete, charge.failed)
            if ($event['key'] === 'charge.complete') {
                $charge = $event['data'];
                if ($charge['status'] === 'successful') {
                     return [
                        'status' => 'completed',
                        'transaction_id' => $charge['id'],
                        'paid_amount' => $charge['amount'] / 100,
                        'order_id' => $charge['metadata']['order_id'] ?? null,
                        'our_transaction_id' => $charge['metadata']['our_transaction_id'] ?? null,
                        'message' => 'Charge completed successfully.'
                    ];
                }
            } elseif ($event['key'] === 'charge.failed') {
                $charge = $event['data'];
                return [
                    'status' => 'failed',
                    'transaction_id' => $charge['id'],
                    'order_id' => $charge['metadata']['order_id'] ?? null,
                    'our_transaction_id' => $charge['metadata']['our_transaction_id'] ?? null,
                    'message' => $charge['failure_message'] ?? 'Charge failed.'
                ];
            }
            // สามารถเพิ่มการจัดการ event types อื่นๆ ได้ตามต้องการ (เช่น refund, expire)

            return ['status' => 'unhandled', 'message' => 'Unhandled Omise event type.'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
