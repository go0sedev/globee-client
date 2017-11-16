<?php

namespace GustavTrenwith\GloBeeClient;

use GustavTrenwith\Guid\Guid;
use GuzzleHttp\Client;
use Phactor\Signature;

/**
 * Class GloBeeClient
 * @package GustavTrenwith\GloBeeClient
 * @author Gustav Trenwith <gtrenwith@gmail.com>
 */
class GloBeeClient
{
    public static function GenerateECDSAKeys()
    {
        return (new \Phactor\Key)->GenerateKeypair();
    }

    /**
     * Creates an invoice on the GloBee system and returns the response for the user to be redirected to the payment
     * interstitial
     *
     * @param array $data Array containing the required values that will be sent to GloBee
     * @param boolean $production True if the live GloBee system is to be used, else it will hit https://test.globee.com
     * @return string Json Encoded String
     */
    public static function createInvoice($data = [], $production = false)
    {
        if ( ! is_array($data)) {
            return json_encode([
                'success' => false,
                'response' => 'The first parameter should be an array'
            ]);
        }

        if ( ! isset($data['currency_code'])) {
            return json_encode([
                'success' => false,
                'response' => 'The first parameter should contain the \'currency_code\' key'
            ]);
        }

        if ( ! isset($data['order_id'])) {
            return json_encode([
                'success' => false,
                'response' => 'The first parameter should contain the \'order_id\' key. This value should be unique to identify the order'
            ]);
        }

        if ( ! isset($data['notification_url'])) {
            return json_encode([
                'success' => false,
                'response' => 'The first parameter should contain the \'notification_url\' key. This URL should handle the payment callback'
            ]);
        }

        if ( ! isset($data['redirect_url'])) {
            return json_encode([
                'success' => false,
                'response' => 'The first parameter should contain the \'redirect_url\' key. This URL should be where the user is directed after they have made payment'
            ]);
        }

        if ( ! isset($data['guid'])) {
            $data['guid'] = Guid::createGuid();
        }

        if ( ! isset($data['description'])) {
            $data['description'] = '';
        }

        if ( ! isset($data['notification_email'])) {
            $data['notification_email'] = '';
        }

        if ( ! isset($data['passthrough_data'])) {
            $data['passthrough_data'] = '';
        }

        if ( ! isset($data['transaction_speed'])) {
            $data['transaction_speed'] = 'normal';
        }

        if ( ! isset($data['buyer_id'])) {
            $data['buyer_id'] = '';
        }

        if ( ! isset($data['buyer_name'])) {
            $data['buyer_name'] = '';
        }

        if ( ! isset($data['buyer_email'])) {
            $data['buyer_email'] = '';
        }

        $url = "https://test.globee.com/invoices";
        if ($production === true) {
            $url = "https://globee.com/invoices";
        }
        $guid = Guid::createGuid();

        $body = [
            'price' => (float) $data['price'],
            'currency' => $data['currency_code'],
            'token' => env('ECDSA_SIN'),
            'guid' => $data['guid'],
            'orderId' => $data['order_id'],
            'itemDesc' => $data['description'],
            'notificationEmail' => $data['notification_email'],
            'notificationURL' => $data['notification_url'],
            'redirectURL' => $data['redirect_url'],
            'posData' => $data['passthrough_data'],
            'transactionSpeed' => $data['transaction_speed'],
            'buyer' => $data['buyer_id'],
            'name' => $data['buyer_name'],
            'email' => $data['buyer_email']
        ];
        $headers = [
            'x-identity' => env('ECDSA_PUBLIC_KEY_COMPRESSED'),
            'x-signature'=> (new Signature)->generate($url . json_encode($body), env('ECDSA_PRIVATE_KEY_HEX')),
            'x-accept-version' => '2.0.0',
            'content-type' => 'application/x-www-form-urlencoded',
        ];
        $client = new Client();
        $request = $client->post($url, [
            'headers' => $headers,
            'form_params' => $body
        ]);
        $response = $request->getBody();
        if ( ! $response) {
            return json_encode([
                'success' => false,
                'response' => $response
            ]);
        }
        $response =  $response->read($response->getSize());
        $response = json_decode($response);

        if ( ! isset($response->data->url)) {
            return json_encode([
                'success' => false,
                'response' => 'No redirect URL returned'
            ]);
        }
        return json_encode([
            'success' => true,
            'response' => 'Please redirect the user to the given URL so they can make payment',
            'redirect_url' => $response->data->url
        ]);
    }
}
