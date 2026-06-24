<?php

namespace App\Controllers;

/**
 * DashboardController
 * Legacy routes — all redirected to the unified AccountController equivalents.
 */
class DashboardController
{
    public function index(): void
    {
        redirect(url('account'));
    }

    public function buyerDashboard(): void
    {
        redirect(url('account'));
    }

    public function showProfile(): void
    {
        redirect(url('account/settings'));
    }

    public function updateProfile(): void
    {
        // Delegate POST to AccountController
        $controller = new AccountController();
        $controller->updateProfile();
    }

    public function changePassword(): void
    {
        // Delegate POST to AccountController
        $controller = new AccountController();
        $controller->updatePassword();
    }

    public function advertiserDashboard(): void
    {
        setFlash('info', 'Advertiser dashboard coming soon.');
        redirect(url('account'));
    }

    public function affiliateDashboard(): void
    {
        setFlash('info', 'Affiliate dashboard coming soon.');
        redirect(url('account'));
    }
}
