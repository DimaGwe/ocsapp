<?php

namespace App\Controllers;

/**
 * BuyerController
 * Legacy routes — redirected to unified AccountController equivalents.
 */
class BuyerController
{
    public function orders(): void
    {
        redirect(url('account/orders'));
    }

    public function orderDetail(): void
    {
        // Extract {id} from the URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($uri, '/'));
        // URL pattern: /buyer/orders/{id}
        $id = end($parts);
        if (is_numeric($id)) {
            redirect(url('account/orders/' . $id));
        } else {
            redirect(url('account/orders'));
        }
    }
}
