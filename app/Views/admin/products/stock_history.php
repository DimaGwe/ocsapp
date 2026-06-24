<?php
$pageTitle = 'Stock History';
ob_start();
?>

<!-- Back Button -->
<div class="mb-6">
    <a href="<?= url('admin/products/stock') ?>" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left mr-2"></i> Back to Stock Management
    </a>
</div>

<!-- Product Header -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= sanitize($product['name']) ?></h1>
            <p class="text-gray-600 mt-2">SKU: <?= sanitize($product['sku'] ?? 'N/A') ?></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Current Stock</p>
            <p class="text-4xl font-bold text-gray-900"><?= number_format($product['stock_quantity']) ?></p>
            <p class="text-sm text-gray-500 mt-1">Low stock alert: <?= number_format($product['low_stock_threshold']) ?></p>
        </div>
    </div>
</div>

<!-- Stock History -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Stock Movement History</h3>
        <p class="text-sm text-gray-600 mt-1">Last 100 stock changes</p>
    </div>

    <?php if (!empty($history)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operation</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Old Stock</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Change</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">New Stock</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($history as $log): ?>
                        <tr class="hover:bg-gray-50">
                            <!-- Date & Time -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= formatDate($log['created_at'], 'M d, Y') ?></div>
                                <div class="text-xs text-gray-500"><?= formatDate($log['created_at'], 'h:i A') ?></div>
                            </td>

                            <!-- User -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm">
                                        <?= strtoupper(substr($log['first_name'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= sanitize($log['first_name'] . ' ' . $log['last_name']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500"><?= sanitize($log['email']) ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Operation -->
                            <td class="px-6 py-4">
                                <?php
                                $operationIcons = [
                                    'set' => 'fa-edit',
                                    'add' => 'fa-plus',
                                    'subtract' => 'fa-minus',
                                    'bulk_update' => 'fa-layer-group',
                                    'order' => 'fa-shopping-cart',
                                    'return' => 'fa-undo',
                                ];
                                $icon = $operationIcons[$log['operation']] ?? 'fa-edit';
                                ?>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas <?= $icon ?> mr-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $log['operation'])) ?>
                                </span>
                            </td>

                            <!-- Old Stock -->
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-medium text-gray-900">
                                    <?= number_format($log['old_quantity']) ?>
                                </span>
                            </td>

                            <!-- Change -->
                            <td class="px-6 py-4 text-center">
                                <?php
                                $change = $log['change_quantity'];
                                $changeColor = $change > 0 ? 'text-green-600' : ($change < 0 ? 'text-red-600' : 'text-gray-600');
                                $changeIcon = $change > 0 ? 'fa-arrow-up' : ($change < 0 ? 'fa-arrow-down' : 'fa-minus');
                                ?>
                                <span class="inline-flex items-center <?= $changeColor ?> font-medium text-sm">
                                    <i class="fas <?= $changeIcon ?> mr-1"></i>
                                    <?= abs($change) ?>
                                </span>
                            </td>

                            <!-- New Stock -->
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">
                                    <?= number_format($log['new_quantity']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="px-6 py-12 text-center">
            <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">No stock history available</p>
            <p class="text-gray-400 text-sm mt-2">Stock changes will appear here</p>
        </div>
    <?php endif; ?>
</div>

<!-- Summary Cards -->
<?php if (!empty($history)): ?>
    <?php
    $totalAdded = array_sum(array_map(fn($log) => max(0, $log['change_quantity']), $history));
    $totalSubtracted = abs(array_sum(array_map(fn($log) => min(0, $log['change_quantity']), $history)));
    $netChange = $totalAdded - $totalSubtracted;
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Added -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Added</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">+<?= number_format($totalAdded) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Subtracted -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Subtracted</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">-<?= number_format($totalSubtracted) ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-minus text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Net Change -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Net Change</p>
                    <p class="text-2xl font-bold <?= $netChange >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
                        <?= $netChange >= 0 ? '+' : '' ?><?= number_format($netChange) ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>