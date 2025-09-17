<?php
/**
 * WhatsApp Notification Handler
 * Sistem notifikasi WhatsApp untuk SPMB Cendekia Muslim
 */

require_once 'config.php';
require_once 'functions.php';

class WhatsAppHandler {
    private $api_url;
    private $api_key;
    private $sender_number;
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        
        // Load WhatsApp API configuration from database
        $this->loadConfiguration();
    }
    
    private function loadConfiguration() {
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'whatsapp_%'");
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $this->api_url = $settings['whatsapp_api_url'] ?? '';
        $this->api_key = $settings['whatsapp_api_key'] ?? '';
        $this->sender_number = $settings['whatsapp_sender_number'] ?? '';
    }
    
    /**
     * Send WhatsApp message
     */
    public function sendMessage($phone, $message, $type = 'text') {
        if (empty($this->api_url) || empty($this->api_key)) {
            $this->logMessage($phone, $message, 'failed', 'WhatsApp API not configured');
            return false;
        }
        
        // Format phone number
        $phone = $this->formatPhoneNumber($phone);
        
        if (!$phone) {
            $this->logMessage($phone, $message, 'failed', 'Invalid phone number');
            return false;
        }
        
        try {
            $response = $this->callAPI($phone, $message, $type);
            
            if ($response['success']) {
                $this->logMessage($phone, $message, 'sent', 'Message sent successfully');
                return true;
            } else {
                $this->logMessage($phone, $message, 'failed', $response['error'] ?? 'Unknown error');
                return false;
            }
        } catch (Exception $e) {
            $this->logMessage($phone, $message, 'failed', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send registration notification
     */
    public function sendRegistrationNotification($student_data) {
        $message = $this->getRegistrationMessage($student_data);
        return $this->sendMessage($student_data['no_hp_ortu'], $message);
    }
    
    /**
     * Send payment notification
     */
    public function sendPaymentNotification($phone, $payment_data) {
        $message = $this->getPaymentMessage($payment_data);
        return $this->sendMessage($phone, $message);
    }
    
    /**
     * Send status update notification
     */
    public function sendStatusNotification($phone, $status, $additional_info = '') {
        $message = $this->getStatusMessage($status, $additional_info);
        return $this->sendMessage($phone, $message);
    }
    
    /**
     * Send bulk notifications
     */
    public function sendBulkNotification($recipients, $message) {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $phone = is_array($recipient) ? $recipient['phone'] : $recipient;
            $custom_message = is_array($recipient) && isset($recipient['message']) 
                ? $recipient['message'] : $message;
            
            $results[] = [
                'phone' => $phone,
                'success' => $this->sendMessage($phone, $custom_message)
            ];
            
            // Add delay between messages to avoid rate limiting
            usleep(500000); // 0.5 second delay
        }
        
        return $results;
    }
    
    /**
     * Send template message
     */
    public function sendTemplate($phone, $template_name, $parameters = []) {
        $message = $this->getTemplateMessage($template_name, $parameters);
        return $this->sendMessage($phone, $message);
    }
    
    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if phone number is valid
        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return false;
        }
        
        // Convert Indonesian format to international
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Call WhatsApp API
     */
    private function callAPI($phone, $message, $type = 'text') {
        $data = [
            'number' => $phone,
            'message' => $message,
            'type' => $type
        ];
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key
            ],
        ]);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        $decoded_response = json_decode($response, true);
        
        if ($http_code !== 200) {
            throw new Exception('HTTP Error: ' . $http_code . ' - ' . $response);
        }
        
        return $decoded_response;
    }
    
    /**
     * Get registration message template
     */
    private function getRegistrationMessage($student_data) {
        $template = "🎉 *PENDAFTARAN BERHASIL* 🎉\n\n";
        $template .= "Assalamu'alaikum Wr. Wb.\n\n";
        $template .= "Pendaftaran santri baru atas nama:\n";
        $template .= "📝 *Nama:* {nama_lengkap}\n";
        $template .= "🆔 *No. Pendaftaran:* {registration_number}\n";
        $template .= "👤 *Username:* {username}\n";
        $template .= "🔐 *Password:* {password}\n\n";
        $template .= "📱 Silakan login ke sistem dengan menggunakan username dan password di atas.\n\n";
        $template .= "🔗 *Link Login:* {login_url}\n\n";
        $template .= "📋 Langkah selanjutnya:\n";
        $template .= "1. Login ke sistem\n";
        $template .= "2. Lengkapi data dan upload dokumen\n";
        $template .= "3. Tunggu verifikasi dari admin\n";
        $template .= "4. Lakukan pembayaran setelah disetujui\n\n";
        $template .= "💬 Untuk informasi lebih lanjut, hubungi:\n";
        $template .= "📞 {contact_phone}\n";
        $template .= "📧 {contact_email}\n\n";
        $template .= "Jazakallahu khair 🤲\n\n";
        $template .= "*SPMB Cendekia Muslim*\n";
        $template .= "_{institution_name}_";
        
        // Replace placeholders
        $replacements = [
            '{nama_lengkap}' => $student_data['nama_lengkap'],
            '{registration_number}' => $student_data['registration_number'],
            '{username}' => $student_data['username'],
            '{password}' => $student_data['password'],
            '{login_url}' => $this->getSetting('base_url') . '/login.php',
            '{contact_phone}' => $this->getSetting('contact_phone'),
            '{contact_email}' => $this->getSetting('contact_email'),
            '{institution_name}' => $this->getSetting('institution_name')
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    /**
     * Get payment message template
     */
    private function getPaymentMessage($payment_data) {
        $template = "💰 *INFORMASI PEMBAYARAN* 💰\n\n";
        $template .= "Assalamu'alaikum Wr. Wb.\n\n";
        
        if ($payment_data['status'] === 'success') {
            $template .= "✅ Alhamdulillah, pembayaran Anda telah berhasil dikonfirmasi!\n\n";
            $template .= "📋 *Detail Pembayaran:*\n";
            $template .= "🆔 Kode: {payment_code}\n";
            $template .= "💵 Jumlah: Rp {amount}\n";
            $template .= "📅 Tanggal: {payment_date}\n";
            $template .= "🏷️ Jenis: {payment_type}\n\n";
            $template .= "📚 Terima kasih atas kepercayaan Anda kepada kami.\n";
            $template .= "Proses selanjutnya akan segera kami informasikan.\n\n";
        } else {
            $template .= "📋 *Instruksi Pembayaran:*\n";
            $template .= "🆔 Kode Pembayaran: {payment_code}\n";
            $template .= "💵 Jumlah: Rp {amount}\n";
            $template .= "🏷️ Jenis: {payment_type}\n\n";
            $template .= "🏦 *Detail Rekening:*\n";
            $template .= "{bank_details}\n\n";
            $template .= "⚠️ *PENTING:*\n";
            $template .= "- Transfer sesuai jumlah yang tertera\n";
            $template .= "- Sertakan kode pembayaran di berita transfer\n";
            $template .= "- Upload bukti transfer di sistem\n";
            $template .= "- Konfirmasi akan dilakukan max 1x24 jam\n\n";
        }
        
        $template .= "💬 Butuh bantuan? Hubungi:\n";
        $template .= "📞 {contact_phone}\n\n";
        $template .= "Jazakallahu khair 🤲\n\n";
        $template .= "*SPMB Cendekia Muslim*";
        
        // Replace placeholders
        $replacements = [
            '{payment_code}' => $payment_data['payment_code'],
            '{amount}' => number_format($payment_data['amount'], 0, ',', '.'),
            '{payment_date}' => isset($payment_data['payment_date']) ? 
                date('d/m/Y H:i', strtotime($payment_data['payment_date'])) : '',
            '{payment_type}' => $this->getPaymentTypeName($payment_data['payment_type']),
            '{bank_details}' => $this->getBankDetails(),
            '{contact_phone}' => $this->getSetting('contact_phone')
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    /**
     * Get status message template
     */
    private function getStatusMessage($status, $additional_info = '') {
        $templates = [
            'approved' => "✅ *DATA DISETUJUI* ✅\n\nAssalamu'alaikum Wr. Wb.\n\nAlhamdulillah, data pendaftaran Anda telah disetujui oleh admin.\n\nSilakan lakukan pembayaran untuk melanjutkan proses pendaftaran.\n\nLogin ke sistem untuk melihat detail pembayaran.\n\nJazakallahu khair 🤲",
            
            'rejected' => "❌ *DATA PERLU DIPERBAIKI* ❌\n\nAssalamu'alaikum Wr. Wb.\n\nMohon maaf, data pendaftaran Anda perlu diperbaiki.\n\n📝 *Alasan:*\n{additional_info}\n\nSilakan login ke sistem dan perbaiki data sesuai catatan di atas.\n\nJazakallahu khair 🤲",
            
            'payment_approved' => "💰 *PEMBAYARAN DIKONFIRMASI* 💰\n\nAssalamu'alaikum Wr. Wb.\n\nAlhamdulillah, pembayaran Anda telah dikonfirmasi.\n\nProses pendaftaran telah selesai. Informasi selanjutnya akan kami sampaikan melalui email dan WhatsApp.\n\nJazakallahu khair 🤲",
            
            'payment_rejected' => "❌ *PEMBAYARAN DITOLAK* ❌\n\nAssalamu'alaikum Wr. Wb.\n\nMohon maaf, bukti pembayaran Anda tidak dapat dikonfirmasi.\n\n📝 *Alasan:*\n{additional_info}\n\nSilakan upload ulang bukti pembayaran yang benar.\n\nJazakallahu khair 🤲"
        ];
        
        $message = $templates[$status] ?? "📢 *NOTIFIKASI* 📢\n\nAssalamu'alaikum Wr. Wb.\n\n{additional_info}\n\nJazakallahu khair 🤲";
        
        return str_replace('{additional_info}', $additional_info, $message);
    }
    
    /**
     * Get template message
     */
    private function getTemplateMessage($template_name, $parameters = []) {
        $stmt = $this->pdo->prepare("SELECT template_content FROM whatsapp_templates WHERE template_name = ? AND is_active = 1");
        $stmt->execute([$template_name]);
        $template = $stmt->fetchColumn();
        
        if (!$template) {
            throw new Exception("Template '{$template_name}' not found");
        }
        
        // Replace parameters
        foreach ($parameters as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        
        return $template;
    }
    
    /**
     * Log WhatsApp message
     */
    private function logMessage($phone, $message, $status, $response = '') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO whatsapp_logs (phone, message, status, response, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$phone, $message, $status, $response]);
        } catch (Exception $e) {
            error_log("WhatsApp log error: " . $e->getMessage());
        }
    }
    
    /**
     * Get setting value
     */
    private function getSetting($key) {
        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() ?: '';
    }
    
    /**
     * Get payment type name
     */
    private function getPaymentTypeName($type) {
        $types = [
            'registration' => 'Biaya Pendaftaran',
            'admission' => 'Biaya Masuk',
            'monthly' => 'SPP Bulanan'
        ];
        
        return $types[$type] ?? $type;
    }
    
    /**
     * Get bank details
     */
    private function getBankDetails() {
        $details = [];
        
        $stmt = $this->pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1 AND method_type = 'bank_transfer'");
        $stmt->execute();
        $banks = $stmt->fetchAll();
        
        foreach ($banks as $bank) {
            $details[] = "🏦 {$bank['bank_name']}\n💳 {$bank['account_number']}\n👤 {$bank['account_name']}";
        }
        
        return implode("\n\n", $details);
    }
    
    /**
     * Test WhatsApp connection
     */
    public function testConnection() {
        try {
            $test_message = "Test connection from SPMB Cendekia Muslim system";
            $test_phone = $this->getSetting('test_phone_number');
            
            if (empty($test_phone)) {
                throw new Exception("Test phone number not configured");
            }
            
            return $this->sendMessage($test_phone, $test_message);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get WhatsApp logs
     */
    public function getLogs($limit = 100, $status = null) {
        $sql = "SELECT * FROM whatsapp_logs";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get message statistics
     */
    public function getStatistics($days = 30) {
        $stmt = $this->pdo->prepare("
            SELECT 
                status,
                COUNT(*) as count,
                DATE(created_at) as date
            FROM whatsapp_logs 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY status, DATE(created_at)
            ORDER BY date DESC
        ");
        $stmt->execute([$days]);
        
        return $stmt->fetchAll();
    }
}

// Usage functions for easy access
function sendWhatsAppMessage($phone, $message) {
    $wa = new WhatsAppHandler();
    return $wa->sendMessage($phone, $message);
}

function sendRegistrationWhatsApp($student_data) {
    $wa = new WhatsAppHandler();
    return $wa->sendRegistrationNotification($student_data);
}

function sendPaymentWhatsApp($phone, $payment_data) {
    $wa = new WhatsAppHandler();
    return $wa->sendPaymentNotification($phone, $payment_data);
}

function sendStatusWhatsApp($phone, $status, $additional_info = '') {
    $wa = new WhatsAppHandler();
    return $wa->sendStatusNotification($phone, $status, $additional_info);
}

function sendBulkWhatsApp($recipients, $message) {
    $wa = new WhatsAppHandler();
    return $wa->sendBulkNotification($recipients, $message);
}
?>