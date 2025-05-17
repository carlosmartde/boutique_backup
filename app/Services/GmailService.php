<?php

namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;

class GmailService
{
    protected $client;
    protected $service;
    protected $senderEmail;

    public function __construct()
    {
        $this->senderEmail = 'proyectopoo1223@gmail.com';
        $this->client = $this->getClient();
        $this->service = new Google_Service_Gmail($this->client);
    }

    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Boutique Inventario');
        $client->setAuthConfig(storage_path('app/credentials/client_secret.json'));
        $client->setAccessType('offline');
        $client->setScopes([Google_Service_Gmail::GMAIL_SEND]);
        
        // Utilizar la contraseña de aplicación
        $accessToken = [
            'access_token' => 'ya29.a0ARrdaM_ktyu_dyyd_tqjp_tjoj',
            'expires_in' => 3599,
            'scope' => 'https://www.googleapis.com/auth/gmail.send',
            'token_type' => 'Bearer',
            'created' => time(),
        ];
        
        $client->setAccessToken($accessToken);
        
        return $client;
    }

    public function sendLowStockNotification($product)
    {
        $to = $this->senderEmail;
        $subject = "ALERTA: Producto con bajo stock - {$product->name}";
        
        $messageBody = "
        <html>
        <head>
            <title>Alerta de Bajo Stock</title>
        </head>
        <body>
            <h2>Alerta de Inventario: Bajo Stock</h2>
            <p>Se ha detectado que el siguiente producto tiene un nivel bajo de inventario:</p>
            <ul>
                <li><strong>Producto:</strong> {$product->name}</li>
                <li><strong>Stock actual:</strong> {$product->stock}</li>
                <li><strong>Categoría:</strong> {$product->category->name}</li>
            </ul>
            <p>Por favor revisar el inventario y realizar pedidos si es necesario.</p>
            <p>Este es un mensaje automático del sistema de inventario de Boutique.</p>
        </body>
        </html>
        ";
        
        $this->sendEmail($to, $subject, $messageBody);
    }

    private function sendEmail($to, $subject, $messageBody)
    {
        $message = new Google_Service_Gmail_Message();
        
        $rawMessageString = "From: {$this->senderEmail}\r\n";
        $rawMessageString .= "To: {$to}\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
        $rawMessageString .= base64_encode($messageBody);
        
        $rawMessage = base64_encode($rawMessageString);
        $message->setRaw($rawMessage);
        
        try {
            $this->service->users_messages->send('me', $message);
            \Log::info('Email enviado correctamente: ' . $subject);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error al enviar email: ' . $e->getMessage());
            return false;
        }
    }
}