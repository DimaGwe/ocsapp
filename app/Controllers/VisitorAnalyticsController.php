<?php

namespace App\Controllers;

/**
 * VisitorAnalyticsController - Track and analyze visitor behavior
 * Provides insights into page views, visitor sources, and engagement metrics
 */
class VisitorAnalyticsController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();

        // Ensure user is logged in as admin (super_admin, admin, admin_staff)
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    /**
     * Display visitor analytics dashboard
     */
    public function index(): void
    {
        try {
            // Period in days (default 30)
            $period = (int) get('period', 30);
            if (!in_array($period, [7, 30, 90, 365])) {
                $period = 30;
            }

            $endDate = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime("-{$period} days"));

            $topSearchTerms = [];

            // Initialize defaults
            $visitors = [
                'period_visitors' => 0,
                'new_visitors' => 0,
                'returning_visitors' => 0,
                'period_pageviews' => 0,
            ];
            $sessions = [
                'avg_duration' => 0,
                'avg_pages_per_session' => 0,
            ];
            $devices = [];
            $referrers = [];
            $browsers = [];
            $operatingSystems = [];
            $geoData = [];
            $cityData = [];
            $hourlyTraffic = [];
            $visitorTrend = [];
            $topPages = [];

            // --- Visitor Overview ---
            // Classify each visitor as new (had at least one is_new_visitor=1 hit) or returning (all hits are 0)
            // This prevents double-counting visitors who first visited and came back in the same period
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(DISTINCT visitor_id) as period_visitors,
                    COUNT(DISTINCT CASE WHEN has_new_flag = 1 THEN visitor_id END) as new_visitors,
                    COUNT(DISTINCT CASE WHEN has_new_flag = 0 THEN visitor_id END) as returning_visitors,
                    SUM(total_views) as period_pageviews
                FROM (
                    SELECT visitor_id, MAX(is_new_visitor) as has_new_flag, COUNT(*) as total_views
                    FROM visitor_analytics
                    WHERE visited_at BETWEEN ? AND ?
                    GROUP BY visitor_id
                ) as v
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                $visitors = [
                    'period_visitors' => (int) $row['period_visitors'],
                    'new_visitors' => (int) $row['new_visitors'],
                    'returning_visitors' => (int) $row['returning_visitors'],
                    'period_pageviews' => (int) $row['period_pageviews'],
                ];
            }

            // --- Session Stats ---
            $stmt = $this->db->prepare("
                SELECT
                    AVG(TIMESTAMPDIFF(SECOND, started_at, last_activity_at)) as avg_duration,
                    AVG(page_views) as avg_pages_per_session
                FROM visitor_sessions
                WHERE started_at BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                $sessions = [
                    'avg_duration' => (float) ($row['avg_duration'] ?? 0),
                    'avg_pages_per_session' => (float) ($row['avg_pages_per_session'] ?? 0),
                ];
            }

            // --- Device Distribution ---
            $stmt = $this->db->prepare("
                SELECT device_type, COUNT(*) as count
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                  AND device_type IS NOT NULL
                GROUP BY device_type
                ORDER BY count DESC
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $devices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Traffic Sources / Referrers ---
            $stmt = $this->db->prepare("
                SELECT
                    CASE
                        WHEN referrer IS NULL OR referrer = '' THEN 'Direct'
                        WHEN referrer LIKE '%google%' THEN 'Google'
                        WHEN referrer LIKE '%facebook%' THEN 'Facebook'
                        WHEN referrer LIKE '%instagram%' THEN 'Instagram'
                        WHEN referrer LIKE '%twitter%' OR referrer LIKE '%x.com%' THEN 'Twitter/X'
                        WHEN referrer LIKE '%bing%' THEN 'Bing'
                        WHEN referrer LIKE '%yahoo%' THEN 'Yahoo'
                        ELSE 'Other'
                    END as source,
                    COUNT(*) as visits
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                GROUP BY source
                ORDER BY visits DESC
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $referrers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Top Browsers ---
            $stmt = $this->db->prepare("
                SELECT browser, COUNT(*) as count
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                  AND browser IS NOT NULL AND browser != ''
                GROUP BY browser
                ORDER BY count DESC
                LIMIT 10
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $browsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Operating Systems ---
            $stmt = $this->db->prepare("
                SELECT operating_system, COUNT(*) as count
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                  AND operating_system IS NOT NULL AND operating_system != ''
                GROUP BY operating_system
                ORDER BY count DESC
                LIMIT 10
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $operatingSystems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Geographic Distribution (by country) ---
            $stmt = $this->db->prepare("
                SELECT
                    COALESCE(country, 'Unknown') as location,
                    COUNT(DISTINCT visitor_id) as visitors,
                    COUNT(*) as pageviews
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                GROUP BY country
                ORDER BY visitors DESC
                LIMIT 15
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $geoData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- City Data ---
            $stmt = $this->db->prepare("
                SELECT
                    COALESCE(city, 'Unknown') as location,
                    COUNT(DISTINCT visitor_id) as visitors,
                    COUNT(*) as pageviews
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                  AND city IS NOT NULL AND city != ''
                GROUP BY city
                ORDER BY visitors DESC
                LIMIT 15
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $cityData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Hourly Traffic Pattern ---
            $stmt = $this->db->prepare("
                SELECT HOUR(visited_at) as hour, COUNT(*) as visits
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                GROUP BY HOUR(visited_at)
                ORDER BY hour ASC
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $hourlyTraffic = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Visitor Trend (daily) ---
            $stmt = $this->db->prepare("
                SELECT
                    DATE(visited_at) as date,
                    COUNT(DISTINCT visitor_id) as visitors,
                    COUNT(*) as pageviews,
                    COUNT(DISTINCT CASE WHEN is_new_visitor = 1 THEN visitor_id END) as new_visitors
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                GROUP BY DATE(visited_at)
                ORDER BY date ASC
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $visitorTrend = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Top Pages ---
            $stmt = $this->db->prepare("
                SELECT
                    page_url,
                    COALESCE(page_title, page_url) as page_title,
                    COUNT(*) as views,
                    COUNT(DISTINCT visitor_id) as unique_visitors
                FROM visitor_analytics
                WHERE visited_at BETWEEN ? AND ?
                GROUP BY page_url, page_title
                ORDER BY views DESC
                LIMIT 20
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $topPages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // --- Top Search Terms ---
            $stmt = $this->db->prepare("
                SELECT
                    search_query,
                    COUNT(*) as searches,
                    ROUND(AVG(results_count), 0) as avg_results,
                    COUNT(DISTINCT ip_address) as unique_searchers
                FROM search_logs
                WHERE searched_at BETWEEN ? AND ?
                GROUP BY search_query
                ORDER BY searches DESC
                LIMIT 20
            ");
            $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $topSearchTerms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('admin/visitor-analytics/index', compact(
                'visitors',
                'sessions',
                'devices',
                'referrers',
                'browsers',
                'operatingSystems',
                'geoData',
                'cityData',
                'hourlyTraffic',
                'visitorTrend',
                'topPages',
                'topSearchTerms',
                'period',
                'startDate',
                'endDate'
            ));

        } catch (\Exception $e) {
            error_log('Visitor Analytics Error: ' . $e->getMessage());

            // Show empty analytics page with defaults
            $visitors = ['period_visitors' => 0, 'new_visitors' => 0, 'returning_visitors' => 0, 'period_pageviews' => 0];
            $sessions = ['avg_duration' => 0, 'avg_pages_per_session' => 0];
            $devices = [];
            $referrers = [];
            $browsers = [];
            $operatingSystems = [];
            $geoData = [];
            $cityData = [];
            $hourlyTraffic = [];
            $visitorTrend = [];
            $topPages = [];
            $period = 30;
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');

            view('admin/visitor-analytics/index', compact(
                'visitors',
                'sessions',
                'devices',
                'referrers',
                'browsers',
                'operatingSystems',
                'geoData',
                'cityData',
                'hourlyTraffic',
                'visitorTrend',
                'topPages',
                'topSearchTerms',
                'period',
                'startDate',
                'endDate'
            ));
        }
    }
}
