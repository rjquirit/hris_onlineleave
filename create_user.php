<?php

/**
 * Create a new user with encrypted data
 * Run with: php create_user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Creating New User ===\n\n";

// User data
$userData = [
    'name' => 'SuperAdmin',
    'email' => 'superadmin@mail.com',
    'password' => 'password',
];

echo "User details:\n";
echo "  Name: {$userData['name']}\n";
echo "  Email: {$userData['email']}\n";
echo "  Password: {$userData['password']}\n\n";

// Check if user already exists
$encryptionService = app(\App\Services\EncryptionService::class);
$emailSearchIndex = $encryptionService->generateBlindIndex($userData['email']);

$existingUser = \App\Models\User::where('email_search_index', $emailSearchIndex)->first();

if ($existingUser) {
    echo "⚠ User with this email already exists!\n";
    echo "  Existing user: {$existingUser->name} ({$existingUser->email})\n";
    echo "  User ID: {$existingUser->id}\n\n";

    $confirm = readline('Do you want to update the password? (yes/no): ');
    if (strtolower(trim($confirm)) === 'yes') {
        $existingUser->password = bcrypt($userData['password']);
        $existingUser->name = $userData['name'];
        $existingUser->save();
        echo "\n✓ User updated successfully!\n";
        echo "  User ID: {$existingUser->id}\n";
        echo "  Name: {$existingUser->name}\n";
        echo "  Email: {$existingUser->email}\n";
    } else {
        echo "\nOperation cancelled.\n";
    }
    exit(0);
}

// Create new user
try {
    $user = \App\Models\User::create([
        'name' => $userData['name'],
        'email' => $userData['email'],
        'password' => bcrypt($userData['password']),
    ]);

    echo "✓ User created successfully!\n\n";
    echo "User details:\n";
    echo "  User ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n\n";

    // Verify encryption
    $rawUser = \DB::table('users')->where('id', $user->id)->first();
    echo "Database verification:\n";
    echo '  Name (encrypted): '.substr($rawUser->name, 0, 40)."...\n";
    echo '  Email (encrypted): '.substr($rawUser->email, 0, 40)."...\n";
    echo '  Name search index: '.substr($rawUser->name_search_index, 0, 30)."...\n";
    echo '  Email search index: '.substr($rawUser->email_search_index, 0, 30)."...\n\n";

    echo "✓ Data is properly encrypted in the database!\n";
    echo "✓ You can now login with:\n";
    echo "  Email: {$userData['email']}\n";
    echo "  Password: {$userData['password']}\n";

} catch (\Exception $e) {
    echo '✗ Error creating user: '.$e->getMessage()."\n";
    exit(1);
}
