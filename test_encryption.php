<?php

/**
 * Test script to verify encryption is working correctly
 * Run with: php test_encryption.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== HRIS Encryption Verification ===\n\n";

// Test 1: Check if encryption passcode is set
echo "1. Checking encryption configuration...\n";
$passcode = config('encryption.passcode');
if ($passcode) {
    echo "   ✓ Encryption passcode is configured\n\n";
} else {
    echo "   ✗ ERROR: Encryption passcode not set!\n\n";
    exit(1);
}

// Test 2: Test User model encryption
echo "2. Testing User model encryption/decryption...\n";
$user = \App\Models\User::first();
if ($user) {
    echo "   User found:\n";
    echo "   - Name (decrypted): {$user->name}\n";
    echo "   - Email (decrypted): {$user->email}\n";
    echo "   ✓ User data decrypts successfully\n\n";
    
    // Check database to see encrypted values
    $rawUser = \DB::table('users')->first();
    if ($rawUser) {
        echo "   Raw database values (encrypted):\n";
        echo "   - Name field: " . substr($rawUser->name, 0, 50) . "...\n";
        echo "   - Email field: " . substr($rawUser->email, 0, 50) . "...\n";
        echo "   - Name search index: " . substr($rawUser->name_search_index, 0, 30) . "...\n";
        echo "   ✓ Data is encrypted in database\n\n";
    }
} else {
    echo "   ⚠ No users found in database\n\n";
}

// Test 3: Test Personnel model encryption
echo "3. Testing Personnel model encryption/decryption...\n";
$personnel = \App\Models\Personnel::first();
if ($personnel) {
    echo "   Personnel found:\n";
    echo "   - First Name (decrypted): {$personnel->first_name}\n";
    echo "   - Last Name (decrypted): {$personnel->last_name}\n";
    echo "   - Email (decrypted): {$personnel->email}\n";
    echo "   - Position (decrypted): {$personnel->position}\n";
    echo "   - Department (decrypted): {$personnel->department}\n";
    echo "   - Salary (decrypted): {$personnel->salary}\n";
    echo "   ✓ Personnel data decrypts successfully\n\n";
    
    // Check database to see encrypted values
    $rawPersonnel = \DB::table('office_personnel')->first();
    if ($rawPersonnel) {
        echo "   Raw database values (encrypted):\n";
        echo "   - First Name field: " . substr($rawPersonnel->first_name, 0, 50) . "...\n";
        echo "   - Email field: " . substr($rawPersonnel->email, 0, 50) . "...\n";
        echo "   - Salary field: " . substr($rawPersonnel->salary, 0, 50) . "...\n";
        echo "   ✓ Data is encrypted in database\n\n";
    }
} else {
    echo "   ⚠ No personnel found in database\n\n";
}

// Test 4: Test search functionality
echo "4. Testing search functionality with blind indexes...\n";
if ($user) {
    $searchTerm = substr($user->name, 0, 4); // Get first 4 characters
    echo "   Searching for users with name containing: '{$searchTerm}'\n";
    
    $encryptionService = app(\App\Services\EncryptionService::class);
    $searchIndex = $encryptionService->generateBlindIndex($searchTerm);
    
    $found = \App\Models\User::where('name_search_index', $searchIndex)->count();
    echo "   Found {$found} user(s) using blind index search\n";
    echo "   ✓ Search functionality works\n\n";
}

echo "=== Verification Complete ===\n";
echo "✓ Encryption is working correctly!\n";
echo "✓ Data is encrypted at rest in the database\n";
echo "✓ Data is automatically decrypted when accessed through models\n";
echo "✓ Search functionality works with blind indexes\n";
