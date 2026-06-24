<?php

namespace App\Controllers;

/**
 * WishlistController
 * Handles POST /api/wishlist/toggle — add or remove a product from the user's wishlist.
 */
class WishlistController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Toggle a product in/out of the user's wishlist.
     * POST /api/wishlist/toggle
     * Body: { product_id: int }
     */
    public function toggle(): void
    {
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login to use wishlist'], 401);
            return;
        }

        // CSRF check
        $token = $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? (json_decode(file_get_contents('php://input'), true)[env('CSRF_TOKEN_NAME', '_csrf_token')] ?? '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $productId = intval($input['product_id'] ?? post('product_id', 0));

        if ($productId <= 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid product']);
            return;
        }

        $userId = userId();

        try {
            // Check if already in wishlist
            $check = $this->db->prepare(
                "SELECT id FROM wishlist_items WHERE user_id = ? AND product_id = ? LIMIT 1"
            );
            $check->execute([$userId, $productId]);
            $existing = $check->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                // Remove from wishlist
                $del = $this->db->prepare(
                    "DELETE FROM wishlist_items WHERE user_id = ? AND product_id = ?"
                );
                $del->execute([$userId, $productId]);
                jsonResponse(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
            } else {
                // Add to wishlist
                $ins = $this->db->prepare(
                    "INSERT INTO wishlist_items (user_id, product_id, created_at) VALUES (?, ?, NOW())"
                );
                $ins->execute([$userId, $productId]);
                jsonResponse(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
            }
        } catch (\PDOException $e) {
            logger("Wishlist toggle failed for user #{$userId}, product #{$productId}: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Could not update wishlist'], 500);
        }
    }
}
