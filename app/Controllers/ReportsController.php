<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * ReportsController - Generate business reports and analytics
 * Provides sales, product, customer, and inventory reports
 */
class ReportsController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();

        // Ensure user is any admin tier
        if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            setFlash('error', 'Access denied. Admin role required.');
            redirect('/');
        }
    }

    /**
     * Reports dashboard - Overview of all available reports
     */
    public function index(): void
    {
        $startDate = get('start_date', date('Y-m-01'));
        $endDate = get('end_date', date('Y-m-d'));

        // Fetch real data from database
        $reportData = [
            'revenue' => 0,
            'orders' => 0,
            'customers' => 0,
            'products_sold' => 0,
            'avg_order' => 0
        ];

        $chartData = [
            'labels' => [],
            'revenue' => [],
            'orders' => [],
            'product_labels' => [],
            'product_sales' => [],
            'category_labels' => [],
            'category_revenue' => []
        ];

        try {
            // Get total revenue and orders
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as order_count,
                    COALESCE(SUM(total), 0) as total_revenue,
                    COALESCE(AVG(total), 0) as avg_order
                FROM orders
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status NOT IN ('cancelled', 'refunded')
            ");
            $stmt->execute([$startDate, $endDate]);
            $orderStats = $stmt->fetch(\PDO::FETCH_ASSOC);

            $reportData['revenue'] = $orderStats['total_revenue'] ?? 0;
            $reportData['orders'] = $orderStats['order_count'] ?? 0;
            $reportData['avg_order'] = $orderStats['avg_order'] ?? 0;

            // Get new customers count
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM users
                WHERE role = 'buyer'
                AND DATE(created_at) BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate, $endDate]);
            $customerStats = $stmt->fetch(\PDO::FETCH_ASSOC);
            $reportData['customers'] = $customerStats['count'] ?? 0;

            // Get products sold count
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM order_items oi
                INNER JOIN orders o ON oi.order_id = o.id
                WHERE DATE(o.created_at) BETWEEN ? AND ?
                AND o.status NOT IN ('cancelled', 'refunded')
            ");
            $stmt->execute([$startDate, $endDate]);
            $productStats = $stmt->fetch(\PDO::FETCH_ASSOC);
            $reportData['products_sold'] = $productStats['total_sold'] ?? 0;

            // Get daily sales for chart (last 30 days)
            $stmt = $this->db->prepare("
                SELECT
                    DATE(created_at) as date,
                    COUNT(*) as orders,
                    COALESCE(SUM(total), 0) as revenue
                FROM orders
                WHERE DATE(created_at) BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
                AND status NOT IN ('cancelled', 'refunded')
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->execute();
            $dailySales = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($dailySales as $day) {
                $chartData['labels'][] = date('M d', strtotime($day['date']));
                $chartData['revenue'][] = (float)$day['revenue'];
                $chartData['orders'][] = (int)$day['orders'];
            }

            // Get top products
            $stmt = $this->db->prepare("
                SELECT
                    p.name,
                    SUM(oi.quantity) as total_sold
                FROM order_items oi
                INNER JOIN products p ON oi.product_id = p.id
                INNER JOIN orders o ON oi.order_id = o.id
                WHERE DATE(o.created_at) BETWEEN ? AND ?
                AND o.status NOT IN ('cancelled', 'refunded')
                GROUP BY p.id, p.name
                ORDER BY total_sold DESC
                LIMIT 5
            ");
            $stmt->execute([$startDate, $endDate]);
            $topProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($topProducts as $product) {
                $chartData['product_labels'][] = $product['name'];
                $chartData['product_sales'][] = (int)$product['total_sold'];
            }

            // Get category revenue
            $stmt = $this->db->prepare("
                SELECT
                    c.name as category,
                    COALESCE(SUM(oi.quantity * oi.price), 0) as revenue
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id
                LEFT JOIN order_items oi ON oi.product_id = p.id
                LEFT JOIN orders o ON oi.order_id = o.id
                WHERE o.id IS NULL OR (
                    DATE(o.created_at) BETWEEN ? AND ?
                    AND o.status NOT IN ('cancelled', 'refunded')
                )
                GROUP BY c.id, c.name
                HAVING revenue > 0
                ORDER BY revenue DESC
                LIMIT 5
            ");
            $stmt->execute([$startDate, $endDate]);
            $categoryStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($categoryStats as $cat) {
                $chartData['category_labels'][] = $cat['category'];
                $chartData['category_revenue'][] = (float)$cat['revenue'];
            }

        } catch (\Exception $e) {
            logger("Reports dashboard error: " . $e->getMessage(), 'error');
        }

        // Ensure we have some data for charts even if empty
        if (empty($chartData['labels'])) {
            $chartData['labels'] = ['No Data'];
            $chartData['revenue'] = [0];
            $chartData['orders'] = [0];
        }
        if (empty($chartData['product_labels'])) {
            $chartData['product_labels'] = ['No Products'];
            $chartData['product_sales'] = [0];
        }
        if (empty($chartData['category_labels'])) {
            $chartData['category_labels'] = ['No Categories'];
            $chartData['category_revenue'] = [0];
        }

        view('admin/reports/index', compact('chartData', 'reportData'));
    }

    /**
     * Sales report
     */
    public function sales(): void
    {
        try {
            $startDate = get('start_date', date('Y-m-01'));
            $endDate = get('end_date', date('Y-m-d'));

            $salesData = [];
            $totalSales = 0;
            $totalOrders = 0;
            $averageOrderValue = 0;

            if ($this->tableExists('orders')) {
                // Total sales
                $stmt = $this->db->prepare("
                    SELECT
                        COUNT(*) as order_count,
                        SUM(total_amount) as total_sales,
                        AVG(total_amount) as avg_order_value
                    FROM orders
                    WHERE DATE(created_at) BETWEEN ? AND ?
                    AND status != 'cancelled'
                ");
                $stmt->execute([$startDate, $endDate]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                $totalOrders = $result['order_count'] ?? 0;
                $totalSales = $result['total_sales'] ?? 0;
                $averageOrderValue = $result['avg_order_value'] ?? 0;

                // Daily sales
                $stmt = $this->db->prepare("
                    SELECT
                        DATE(created_at) as date,
                        COUNT(*) as orders,
                        SUM(total_amount) as sales
                    FROM orders
                    WHERE DATE(created_at) BETWEEN ? AND ?
                    AND status != 'cancelled'
                    GROUP BY DATE(created_at)
                    ORDER BY date ASC
                ");
                $stmt->execute([$startDate, $endDate]);
                $salesData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            view('admin/reports/sales', compact(
                'salesData',
                'totalSales',
                'totalOrders',
                'averageOrderValue',
                'startDate',
                'endDate'
            ));

        } catch (\Exception $e) {
            logger('Sales Report Error: ' . $e->getMessage(), 'error');
            $salesData = [];
            $totalSales = 0;
            $totalOrders = 0;
            $averageOrderValue = 0;
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');

            view('admin/reports/sales', compact(
                'salesData',
                'totalSales',
                'totalOrders',
                'averageOrderValue',
                'startDate',
                'endDate'
            ));
        }
    }

    /**
     * Products report
     */
    public function products(): void
    {
        try {
            // Top selling products
            $topProducts = [];
            $lowStockProducts = [];

            if ($this->tableExists('products')) {
                // Top products by sales
                if ($this->tableExists('order_items')) {
                    $stmt = $this->db->query("
                        SELECT
                            p.name,
                            p.sku,
                            SUM(oi.quantity) as total_sold,
                            SUM(oi.quantity * oi.unit_price) as revenue
                        FROM products p
                        INNER JOIN order_items oi ON p.id = oi.product_id
                        GROUP BY p.id
                        ORDER BY total_sold DESC
                        LIMIT 20
                    ");
                    $topProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }

                // Low stock products
                $stmt = $this->db->query("
                    SELECT name, sku, stock_quantity
                    FROM products
                    WHERE stock_quantity < 10
                    AND stock_quantity > 0
                    ORDER BY stock_quantity ASC
                    LIMIT 20
                ");
                $lowStockProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            view('admin/reports/products', compact('topProducts', 'lowStockProducts'));

        } catch (\Exception $e) {
            logger('Products Report Error: ' . $e->getMessage(), 'error');
            $topProducts = [];
            $lowStockProducts = [];
            view('admin/reports/products', compact('topProducts', 'lowStockProducts'));
        }
    }

    /**
     * Customers report
     */
    public function customers(): void
    {
        try {
            // Top customers
            $topCustomers = [];
            $newCustomers = [];

            if ($this->tableExists('users') && $this->tableExists('orders')) {
                // Top customers by order value
                $stmt = $this->db->query("
                    SELECT
                        u.first_name,
                        u.last_name,
                        u.email,
                        COUNT(o.id) as order_count,
                        SUM(o.total_amount) as total_spent
                    FROM users u
                    INNER JOIN orders o ON u.id = o.user_id
                    WHERE u.role = 'buyer'
                    GROUP BY u.id
                    ORDER BY total_spent DESC
                    LIMIT 20
                ");
                $topCustomers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // New customers this month
                $stmt = $this->db->query("
                    SELECT first_name, last_name, email, created_at
                    FROM users
                    WHERE role = 'buyer'
                    AND MONTH(created_at) = MONTH(CURRENT_DATE())
                    AND YEAR(created_at) = YEAR(CURRENT_DATE())
                    ORDER BY created_at DESC
                    LIMIT 20
                ");
                $newCustomers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            view('admin/reports/customers', compact('topCustomers', 'newCustomers'));

        } catch (\Exception $e) {
            logger('Customers Report Error: ' . $e->getMessage(), 'error');
            $topCustomers = [];
            $newCustomers = [];
            view('admin/reports/customers', compact('topCustomers', 'newCustomers'));
        }
    }

    /**
     * Inventory report
     */
    public function inventory(): void
    {
        try {
            $outOfStock = [];
            $lowStock = [];
            $overStock = [];

            if ($this->tableExists('products')) {
                // Out of stock
                $stmt = $this->db->query("
                    SELECT name, sku, stock_quantity
                    FROM products
                    WHERE stock_quantity = 0
                    ORDER BY name ASC
                ");
                $outOfStock = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Low stock (less than 10)
                $stmt = $this->db->query("
                    SELECT name, sku, stock_quantity
                    FROM products
                    WHERE stock_quantity > 0 AND stock_quantity < 10
                    ORDER BY stock_quantity ASC
                ");
                $lowStock = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Over stock (more than 100)
                $stmt = $this->db->query("
                    SELECT name, sku, stock_quantity
                    FROM products
                    WHERE stock_quantity > 100
                    ORDER BY stock_quantity DESC
                ");
                $overStock = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            view('admin/reports/inventory', compact('outOfStock', 'lowStock', 'overStock'));

        } catch (\Exception $e) {
            logger('Inventory Report Error: ' . $e->getMessage(), 'error');
            $outOfStock = [];
            $lowStock = [];
            $overStock = [];
            view('admin/reports/inventory', compact('outOfStock', 'lowStock', 'overStock'));
        }
    }

    /**
     * Export report data
     */
    public function export(): void
    {
        $type = get('type', 'sales');
        $format = get('format', 'csv');

        // TODO: Implement export functionality
        setFlash('info', 'Export functionality coming soon');
        redirect('admin/reports');
    }

    /**
     * Check if a table exists
     */
    private function tableExists(string $tableName): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$tableName]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
