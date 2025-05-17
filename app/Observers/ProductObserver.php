<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\EmailService;

class ProductObserver
{
    protected $emailService;
    protected $lowStockThreshold = 5; // Umbral para considerar bajo stock
    
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    
    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        // Verificar si el stock ha cambiado
        if ($product->isDirty('stock')) {
            $newStock = $product->stock;
            
            // Si el stock es menor o igual al threshold, enviar notificación
            if ($newStock <= $this->lowStockThreshold) {
                try {
                    $this->emailService->sendLowStockNotification($product);
                    \Log::info("Notificación de bajo stock enviada para el producto: {$product->name}");
                } catch (\Exception $e) {
                    \Log::error("Error al enviar notificación de bajo stock: " . $e->getMessage());
                }
            }
        }
    }
}