<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\EmailService;
use Illuminate\Http\Request;

class NotificationTestController extends Controller
{
    protected $emailService;
    
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    
    /**
     * Prueba la notificación de bajo stock para un producto específico
     */
    public function testLowStockNotification($productId)
    {
        try {
            $product = Product::with('category')->findOrFail($productId);
            
            $result = $this->emailService->sendLowStockNotification($product);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => "Notificación de prueba enviada para el producto: {$product->name}"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Error al enviar la notificación. Revisa los logs para más detalles."
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error al enviar la notificación: " . $e->getMessage()
            ], 500);
        }
    }
}