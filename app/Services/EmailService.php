<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected $mailer;
    protected $senderEmail;
    protected $senderPassword;

    public function __construct()
    {
        $this->senderEmail = 'proyectopoo1223@gmail.com';
        $this->senderPassword = 'ktyu dyyd tqjp tjoj'; // Contraseña de aplicación
        $this->mailer = $this->configureMailer();
    }

    private function configureMailer()
    {
        $mailer = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mailer->isSMTP();
            $mailer->Host = 'smtp.gmail.com';
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->senderEmail;
            $mailer->Password = $this->senderPassword;
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->Port = 587;
            
            // Configuración del remitente
            $mailer->setFrom($this->senderEmail, 'Sistema de Inventario Boutique');
            
            // Configuración general
            $mailer->isHTML(true);
            $mailer->CharSet = 'UTF-8';
            
            return $mailer;
        } catch (Exception $e) {
            \Log::error('Error al configurar PHPMailer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendLowStockNotification($product)
    {
        try {
            // Resetear los destinatarios para evitar acumulación
            $this->mailer->clearAddresses();
              // Agregar destinatario
            $this->mailer->addAddress('proyectopoo1223@gmail.com', 'Administrador');
            
            // Configurar el asunto
            $this->mailer->Subject = "Alerta: Stock Bajo - {$product->name}";
            
            // Crear el cuerpo del mensaje
            $body = "
                <h2>Alerta de Stock Bajo</h2>
                <p>El siguiente producto ha alcanzado un nivel bajo de stock:</p>
                <ul>
                    <li><strong>Producto:</strong> {$product->name}</li>
                    <li><strong>Código:</strong> {$product->code}</li>
                    <li><strong>Stock Actual:</strong> {$product->stock} unidades</li>
                    <li><strong>Marca:</strong> {$product->brand}</li>
                </ul>
                <p>Por favor, considere realizar un nuevo pedido para reabastecer el inventario.</p>
            ";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            // Enviar el correo
            $this->mailer->send();
            
            return true;
        } catch (Exception $e) {
            \Log::error('Error al enviar notificación de stock bajo: ' . $e->getMessage());
            return false;
        }
    }
}