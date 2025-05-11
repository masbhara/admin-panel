<?php
/**
 * GitHub Webhook Auto Update Script
 * 
 * Script ini menangani webhook dari GitHub untuk otomatis mengupdate
 * aplikasi di server production ketika ada push ke branch main.
 * 
 * Cara penggunaan:
 * 1. Upload file ini ke server production (misalnya di /var/www/html/webhook.php)
 * 2. Pastikan script deploy-update-only.sh sudah ada dan bisa dieksekusi
 * 3. Konfigurasi GitHub webhook dengan URL ke file ini
 * 4. Tambahkan Secret di GitHub webhook dan di file ini
 */

// Konfigurasi
$secret = 'GANTI_DENGAN_SECRET_WEBHOOK_ANDA'; // Secret yang sama dengan di GitHub webhook
$branch = 'main'; // Branch yang ingin di-update
$logFile = __DIR__ . '/webhook.log';
$deployScript = __DIR__ . '/deploy-update-only.sh';
$autoUpdate = true; // Set false jika hanya ingin mencatat tanpa auto-update

// Fungsi untuk logging
function writeLog($message) {
    global $logFile;
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $message" . PHP_EOL, FILE_APPEND);
}

// Fungsi untuk verifikasi signature
function verifySignature($payload, $signature, $secret) {
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    return hash_equals($expectedSignature, $signature);
}

// Fungsi untuk menjalankan update
function runUpdate() {
    global $deployScript;
    writeLog("Menjalankan script update...");
    
    // Jalankan script update tanpa interaksi user
    $command = "bash $deployScript auto-update 2>&1";
    exec($command, $output, $returnCode);
    
    $outputStr = implode("\n", $output);
    writeLog("Output: $outputStr");
    writeLog("Return code: $returnCode");
    
    return $returnCode === 0;
}

// Main process
try {
    // Ambil payload dan signature
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    
    // Verifikasi request
    if (empty($payload)) {
        throw new Exception("Payload kosong");
    }
    
    if (empty($signature)) {
        throw new Exception("Signature tidak ditemukan");
    }
    
    // Verifikasi signature
    if (!verifySignature($payload, $signature, $secret)) {
        throw new Exception("Signature tidak valid");
    }
    
    // Parse payload
    $data = json_decode($payload, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Payload bukan JSON yang valid: " . json_last_error_msg());
    }
    
    // Cek jenis event
    $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
    if ($event !== 'push') {
        writeLog("Menerima event non-push: $event. Tidak ada aksi yang diambil.");
        echo json_encode(['status' => 'ignored', 'message' => "Event $event diabaikan"]);
        exit;
    }
    
    // Cek branch
    $ref = $data['ref'] ?? '';
    $receivedBranch = str_replace('refs/heads/', '', $ref);
    
    if ($receivedBranch !== $branch) {
        writeLog("Push ke branch $receivedBranch, bukan ke $branch. Tidak ada aksi yang diambil.");
        echo json_encode(['status' => 'ignored', 'message' => "Branch $receivedBranch diabaikan"]);
        exit;
    }
    
    // Log informasi commit
    $commits = $data['commits'] ?? [];
    $commitCount = count($commits);
    $headCommit = $data['head_commit'] ?? [];
    $headCommitId = $headCommit['id'] ?? 'unknown';
    $headCommitMessage = $headCommit['message'] ?? 'unknown';
    
    writeLog("Menerima $commitCount commit ke branch $branch");
    writeLog("Head commit: $headCommitId - $headCommitMessage");
    
    // Jalankan update jika diaktifkan
    if ($autoUpdate) {
        $success = runUpdate();
        if ($success) {
            writeLog("Update berhasil");
            echo json_encode(['status' => 'success', 'message' => 'Update berhasil']);
        } else {
            writeLog("Update gagal");
            echo json_encode(['status' => 'error', 'message' => 'Update gagal']);
        }
    } else {
        writeLog("Auto update dinonaktifkan. Tidak ada aksi yang diambil.");
        echo json_encode(['status' => 'success', 'message' => 'Webhook diterima, auto update dinonaktifkan']);
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    writeLog("Error: $errorMessage");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
} 