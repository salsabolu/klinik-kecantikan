<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Dashboard Queries After PostgreSQL Migration...\n";
echo "=" . str_repeat("=", 60) . "\n";

try {
    // Test 1: Basic Transaction count
    echo "1. Testing Transaction count today...\n";
    $transactionCount = \App\Models\Transaction::whereDate('tanggal_transaksi', today())->count();
    echo "   ✅ Transactions today: {$transactionCount}\n";

    // Test 2: Revenue calculation (the problematic query)
    echo "2. Testing Revenue calculation (fixed query)...\n";
    $revenue = \App\Models\Transaction::whereDate('tanggal_transaksi', today())
        ->where('payment_status', 'paid')
        ->sum('final_amount');
    echo "   ✅ Revenue today: Rp " . number_format($revenue, 0, ',', '.') . "\n";

    // Test 3: Other stats
    echo "3. Testing other dashboard stats...\n";
    $patientCount = \App\Models\Patient::count();
    $reservationCount = \App\Models\Reservation::whereDate('tanggal_reservasi', today())->count();
    
    echo "   ✅ Total patients: {$patientCount}\n";
    echo "   ✅ Reservations today: {$reservationCount}\n";
    
    echo "\n🎉 ALL TESTS PASSED! Dashboard queries are working correctly.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
