<?php

namespace App\Services;

use App\Enums\SiteConfigEnum;
use Illuminate\Support\Facades\Http;

class IPaymu
{
  private $va;
  private $apiKey;
  private $ngrokUrl;
  private $frontendUrl;
  private $apiUrl;

  public function __construct()
  {
    $this->va = config(SiteConfigEnum::IPAYMU_VA);
    $this->apiKey = config(SiteConfigEnum::IPAYMU_KEY);
    $this->ngrokUrl = config(SiteConfigEnum::NGROK_URL);
    $this->frontendUrl = config('app.frontend_url');
    $this->apiUrl = 'https://sandbox.ipaymu.com/api/v2/payment';
  }

  public function generateRequestBody(array $product, array $qty, array $price, string $referenceId): array
  {
    $body['product']    = $product;
    $body['qty']        = $qty;
    $body['price']      = $price;
    $body['returnUrl']  = $this->generateUrl('returnUrl');
    $body['cancelUrl']  = $this->generateUrl('cancelUrl');
    $body['notifyUrl']  = $this->generateUrl('notifyUrl');
    $body['referenceId'] = $referenceId;
    $body['autoRedirect'] = 10;

    $jsonBody     = json_encode($body, JSON_UNESCAPED_SLASHES);
    $requestBody  = strtolower(hash('sha256', $jsonBody));
    return [
      'encrypted' => $requestBody,
      'array' => $body,
      'json' => $jsonBody
    ];
  }

  public function makeRedirectPayment(array $product, array $qty, array $price, string $referenceId)
  {
    $requestBody = $this->generateRequestBody($product, $qty, $price, $referenceId);
    $stringToSign = strtoupper('POST') . ':' . $this->va . ':' . $requestBody['encrypted'] . ':' . $this->apiKey;
    $signature    = hash_hmac('sha256', $stringToSign, $this->apiKey);

    $response = $this->sendRequest($signature, $requestBody['array']);

    if ($response->ok()) {
      $json = $response->json();
      return [
        'session_id' => $json['Data']['SessionID'],
        'url' => $json['Data']['Url']
      ];
    } else {
      throw new \Exception($response->body(), $response->status());
    }
  }

  private function sendRequest(string $signature, array $data)
  {
    return Http::withoutVerifying()
      ->withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'va' => $this->va,
        'signature' => $signature,
        'timestamp' => $this->generateTimestamp(),
      ])->post($this->apiUrl, $data);
  }

  private function generateUrl($type)
  {
    if ($type == 'returnUrl') {
      return "{$this->frontendUrl}/order/thank-you";
    }

    if ($type == 'cancelUrl') {
      return "{$this->frontendUrl}/order/cancel";
    }

    if ($type == 'notifyUrl') {
      return "{$this->ngrokUrl}/api/payment-gateway/ipaymu";
    }
  }

  private function generateTimestamp()
  {
    return date("Ymdhis");
  }
}
