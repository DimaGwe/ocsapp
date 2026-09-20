<?php

namespace App\Helpers;

/**
 * StockValidator - Comprehensive Stock Validation System
 * Prevents overselling and provides real-time stock checks
 * 
 * Features:
 * - Real-time stock validation
 * - Multi-source stock checking (products + inventories)
 * - Stock reservation during checkout
 * - Low stock alerts
 */
class StockValidator
{
    private $db;
    
    public function __construct()
    {
        $this->db = \Database::getConnection();
    }
    
    /**
     * Check if sufficient stock is available for a product
     * 
     * @param int $productId
     * @param int $requestedQuantity
     * @param int|null $inventoryId Optional - check specific inventory
     * @return array ['available' => bool, 'stock' => int, 'message' => string]
     */
    public function checkAvailability(int $productId, int $requestedQuantity, ?int $inventoryId = null): array
    {
        try {
            // Get actual available stock
            $availableStock = $this->getAvailableStock($productId, $inventoryId);
            
            if ($availableStock === false) {
                return [
                    'available' => false,
                    'stock' => 0,
                    'message' => 'Product not found or unavailable'
                ];
            }
            
            if ($availableStock < $requestedQuantity) {
                return [
                    'available' => false,
                    'stock' => $availableStock,
                    'message' => "Only {$availableStock} available in stock"
                ];
            }
            
            return [
                'available' => true,
                'stock' => $availableStock,
                'message' => 'Stock available'
            ];
            
        } catch (\Exception $e) {
            logger("Stock validation error: " . $e->getMessage(), 'error');
            return [
                'available' => false,
                'stock' => 0,
                'message' => 'Error checking stock availability'
            ];
        }
    }
    
    /**
     * Get available stock for a product
     * Checks both products table and inventories table
     * 
     * @param int $productId
     * @param int|null $inventoryId
     * @return int|false Available stock quantity or false if not found
     */
    public function getAvailableStock(int $productId, ?int $inventoryId = null)
    {
        try {
            if ($inventoryId) {
                // Check specific inventory item
                $stmt = $this->db->prepare("
                    SELECT i.stock_quantity
                    FROM inventories i
                    WHERE i.id = :inventory_id 
                    AND i.product_id = :product_id
                    AND i.status = 'active'
                ");
                $stmt->execute([
                    'inventory_id' => $inventoryId,
                    'product_id' => $productId
                ]);
            } else {
                // Check product's general stock or sum of all active inventories
                $stmt = $this->db->prepare("
                    SELECT 
                        COALESCE(
                            (SELECT SUM(stock_quantity) 
                             FROM inventories 
                             WHERE product_id = p.id AND status = 'active'),
                            p.stock_quantity,
                            0
                        ) as available_stock
                    FROM products p
                    WHERE p.id = :product_id AND p.status = 'active'
                ");
                $stmt->execute(['product_id' => $productId]);
            }
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false;
            }
            
            return max(0, (int)($result['stock_quantity'] ?? $result['available_stock'] ?? 0));
            
        } catch (\Exception $e) {
            logger("Get stock error: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Validate entire cart stock before checkout
     * 
     * @param array $cart Session cart array
     * @return array ['valid' => bool, 'errors' => array, 'updates' => array]
     */
    public function validateCart(array $cart): array
    {
        $errors = [];
        $updates = [];
        $allValid = true;
        
        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? (is_numeric($cartKey) ? (int)$cartKey : 0);
            $requestedQty = $item['quantity'] ?? 1;
            $inventoryId = $item['inventory_id'] ?? null;
            
            if (!$productId) {
                continue;
            }
            
            $stockCheck = $this->checkAvailability($productId, $requestedQty, $inventoryId);
            
            if (!$stockCheck['available']) {
                $allValid = false;
                $errors[$cartKey] = [
                    'product_id' => $productId,
                    'product_name' => $item['name'] ?? 'Unknown Product',
                    'requested' => $requestedQty,
                    'available' => $stockCheck['stock'],
                    'message' => $stockCheck['message']
                ];
                
                // Suggest update if some stock available
                if ($stockCheck['stock'] > 0) {
                    $updates[$cartKey] = $stockCheck['stock'];
                }
            }
        }
        
        return [
            'valid' => $allValid,
            'errors' => $errors,
            'updates' => $updates
        ];
    }
    
    /**
     * Get stock status badge info
     * 
     * @param int $stockQuantity
     * @return array ['badge' => string, 'class' => string, 'show_count' => bool]
     */
    public function getStockBadge(int $stockQuantity): array
    {
        if ($stockQuantity <= 0) {
            return [
                'badge' => 'Out of Stock',
                'class' => 'badge-danger',
                'show_count' => false
            ];
        } elseif ($stockQuantity < 5) {
            return [
                'badge' => "Only {$stockQuantity} left!",
                'class' => 'badge-warning',
                'show_count' => true
            ];
        } elseif ($stockQuantity < 10) {
            return [
                'badge' => "Low Stock ({$stockQuantity} available)",
                'class' => 'badge-info',
                'show_count' => true
            ];
        } else {
            return [
                'badge' => 'In Stock',
                'class' => 'badge-success',
                'show_count' => false
            ];
        }
    }
    
    /**
     * Check if product is low on stock
     * 
     * @param int $stockQuantity
     * @param int $threshold Default is 10
     * @return bool
     */
    public function isLowStock(int $stockQuantity, int $threshold = 10): bool
    {
        return $stockQuantity > 0 && $stockQuantity <= $threshold;
    }
    
    /**
     * Check if product is out of stock
     * 
     * @param int $stockQuantity
     * @return bool
     */
    public function isOutOfStock(int $stockQuantity): bool
    {
        return $stockQuantity <= 0;
    }
    
    /**
     * Get products that are low on stock (for seller alerts)
     * 
     * @param int $shopId
     * @param int $threshold
     * @return array
     */
    public function getLowStockProducts(int $shopId, int $threshold = 10): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.id,
                    p.name,
                    p.sku,
                    i.stock_quantity,
                    i.id as inventory_id
                FROM inventories i
                INNER JOIN products p ON i.product_id = p.id
                WHERE i.shop_id = :shop_id
                AND i.status = 'active'
                AND i.stock_quantity > 0
                AND i.stock_quantity <= :threshold
                ORDER BY i.stock_quantity ASC
            ");
            
            $stmt->execute([
                'shop_id' => $shopId,
                'threshold' => $threshold
            ]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            logger("Get low stock products error: " . $e->getMessage(), 'error');
            return [];
        }
    }
    
    /**
     * Get out of stock products (for seller alerts)
     * 
     * @param int $shopId
     * @return array
     */
    public function getOutOfStockProducts(int $shopId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.id,
                    p.name,
                    p.sku,
                    i.id as inventory_id
                FROM inventories i
                INNER JOIN products p ON i.product_id = p.id
                WHERE i.shop_id = :shop_id
                AND i.status = 'active'
                AND i.stock_quantity <= 0
                ORDER BY p.name ASC
            ");
            
            $stmt->execute(['shop_id' => $shopId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            logger("Get out of stock products error: " . $e->getMessage(), 'error');
            return [];
        }
    }
    
    /**
     * Update product stock quantity
     * 
     * @param int $inventoryId
     * @param int $quantity Can be negative to reduce stock
     * @param string $reason Reason for stock change
     * @param int|null $orderId Related order ID
     * @return bool
     */
    public function updateStock(int $inventoryId, int $quantity, string $reason = 'manual', ?int $orderId = null): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Get current stock
            $stmt = $this->db->prepare("
                SELECT stock_quantity, product_id, shop_id 
                FROM inventories 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $inventoryId]);
            $inventory = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$inventory) {
                $this->db->rollBack();
                return false;
            }
            
            $oldQuantity = (int)$inventory['stock_quantity'];
            $newQuantity = max(0, $oldQuantity + $quantity);
            
            // Update inventory
            $stmt = $this->db->prepare("
                UPDATE inventories 
                SET stock_quantity = :new_quantity,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'new_quantity' => $newQuantity,
                'id' => $inventoryId
            ]);
            
            // Log stock history - FIXED column names to match database
            $stmt = $this->db->prepare("
                INSERT INTO inventory_stock_history (
                    inventory_id,
                    quantity_before,
                    quantity_after,
                    quantity_change,
                    reason,
                    reference_type,
                    reference_id,
                    order_id,
                    notes,
                    created_by,
                    created_at
                ) VALUES (
                    :inventory_id,
                    :quantity_before,
                    :quantity_after,
                    :quantity_change,
                    :reason,
                    :reference_type,
                    :reference_id,
                    :order_id,
                    :notes,
                    :created_by,
                    NOW()
                )
            ");
            
            $stmt->execute([
                'inventory_id' => $inventoryId,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $newQuantity,
                'quantity_change' => $quantity,
                'reason' => 'order', // Use 'order' from enum
                'reference_type' => $orderId ? 'order' : null,
                'reference_id' => $orderId,
                'order_id' => $orderId,
                'notes' => "Stock updated via {$reason}",
                'created_by' => userId() ?? 0
            ]);
            
            $this->db->commit();
            
            logger("Stock updated for inventory #{$inventoryId}: {$oldQuantity} → {$newQuantity} (Reason: {$reason})", 'info');
            
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            logger("Update stock error: " . $e->getMessage(), 'error');
            return false;
        }
    }
}