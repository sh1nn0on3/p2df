<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;

/**
 * EmailMetadataParser - Parse email metadata từ CSV hoặc raw email
 * 
 * Hỗ trợ:
 * - Parse metadata từ CSV với format mở rộng
 * - Parse từ raw email headers
 * - Tự động extract thông tin quan trọng cho điều tra số
 */
class EmailMetadataParser
{
    /**
     * Parse metadata từ CSV row
     * 
     * Format cơ bản: from,to,subject,body
     * Format mở rộng: from,to,subject,body,date_sent,date_received,cc,bcc,reply_to,message_id,sender_ip,mailer
     * 
     * @param array $row CSV row data
     * @param array|null $headers CSV headers (optional)
     * @return array Metadata array
     */
    public function parseFromCsv(array $row, ?array $headers = null): array
    {
        $metadata = [
            'from' => null,
            'to' => null,
            'subject' => null,
            'body' => null,
            'date_sent' => null,
            'date_received' => null,
            'cc' => null,
            'bcc' => null,
            'reply_to' => null,
            'message_id' => null,
            'headers' => null,
            'sender_ip' => null,
            'attachments_info' => null,
            'mailer' => null,
        ];

        // Nếu có headers, map theo tên cột
        if ($headers) {
            foreach ($headers as $index => $headerName) {
                $headerName = strtolower(trim($headerName));
                if (isset($row[$index])) {
                    $value = trim($row[$index]);
                    
                    // Map các trường cơ bản
                    if (in_array($headerName, ['from', 'to', 'subject'])) {
                        $metadata[$headerName] = $value;
                    }
                    elseif ($headerName === 'body') {
                        // Không trim body để giữ nguyên format
                        $metadata['body'] = $row[$index] ?? '';
                    }
                    // Map metadata
                    elseif ($headerName === 'date_sent' || $headerName === 'sent_date') {
                        $metadata['date_sent'] = $this->parseDate($value);
                    }
                    elseif ($headerName === 'date_received' || $headerName === 'received_date') {
                        $metadata['date_received'] = $this->parseDate($value);
                    }
                    elseif (in_array($headerName, ['cc', 'bcc', 'reply_to', 'reply-to'])) {
                        $metadata[$headerName === 'reply-to' ? 'reply_to' : $headerName] = $value;
                    }
                    elseif ($headerName === 'message_id' || $headerName === 'message-id') {
                        $metadata['message_id'] = $value;
                    }
                    elseif ($headerName === 'sender_ip' || $headerName === 'ip') {
                        $metadata['sender_ip'] = $value;
                    }
                    elseif ($headerName === 'mailer' || $headerName === 'x-mailer') {
                        $metadata['mailer'] = $value;
                    }
                    elseif ($headerName === 'headers') {
                        $metadata['headers'] = $this->parseHeadersJson($value);
                    }
                    elseif ($headerName === 'attachments' || $headerName === 'attachments_info') {
                        $metadata['attachments_info'] = $this->parseAttachmentsJson($value);
                    }
                }
            }
        } else {
            // Format cơ bản: from,to,subject,body
            // Hoặc format mở rộng: from,to,subject,body,date_sent,date_received,cc,bcc,reply_to,message_id,...
            
            if (count($row) >= 4) {
                $metadata['from'] = trim($row[0]);
                $metadata['to'] = trim($row[1]);
                $metadata['subject'] = trim($row[2]);
                // Không trim body để giữ nguyên format và whitespace
                $metadata['body'] = $row[3];
            }

            // Parse các trường metadata bổ sung (nếu có)
            if (count($row) >= 5) {
                $metadata['date_sent'] = $this->parseDate($row[4] ?? null);
            }
            if (count($row) >= 6) {
                $metadata['date_received'] = $this->parseDate($row[5] ?? null);
            }
            if (count($row) >= 7 && !empty($row[6])) {
                $metadata['cc'] = trim($row[6]);
            }
            if (count($row) >= 8 && !empty($row[7])) {
                $metadata['bcc'] = trim($row[7]);
            }
            if (count($row) >= 9 && !empty($row[8])) {
                $metadata['reply_to'] = trim($row[8]);
            }
            if (count($row) >= 10 && !empty($row[9])) {
                $metadata['message_id'] = trim($row[9]);
            }
            if (count($row) >= 11 && !empty($row[10])) {
                $metadata['sender_ip'] = trim($row[10]);
            }
            if (count($row) >= 12 && !empty($row[11])) {
                $metadata['mailer'] = trim($row[11]);
            }
        }

        // Set default date_sent nếu chưa có (dùng thời gian hiện tại)
        if (!$metadata['date_sent']) {
            $metadata['date_sent'] = now();
        }

        // Set default date_received nếu chưa có (bằng date_sent)
        if (!$metadata['date_received']) {
            $metadata['date_received'] = $metadata['date_sent'];
        }

        // Generate message_id nếu chưa có
        if (!$metadata['message_id']) {
            $metadata['message_id'] = $this->generateMessageId();
        }

        return $metadata;
    }

    /**
     * Parse raw email headers
     * 
     * @param string $rawHeaders Raw email headers string
     * @return array Parsed headers
     */
    public function parseRawHeaders(string $rawHeaders): array
    {
        $headers = [];
        $lines = explode("\n", $rawHeaders);

        foreach ($lines as $line) {
            if (preg_match('/^([^:]+):\s*(.+)$/i', trim($line), $matches)) {
                $headerName = strtolower(trim($matches[1]));
                $headerValue = trim($matches[2]);
                $headers[$headerName] = $headerValue;
            }
        }

        return $headers;
    }

    /**
     * Extract metadata từ raw email headers
     * 
     * @param string $rawHeaders Raw email headers
     * @return array Extracted metadata
     */
    public function extractMetadataFromHeaders(string $rawHeaders): array
    {
        $headers = $this->parseRawHeaders($rawHeaders);

        $metadata = [
            'from' => $headers['from'] ?? null,
            'to' => $headers['to'] ?? null,
            'cc' => $headers['cc'] ?? null,
            'bcc' => $headers['bcc'] ?? null,
            'reply_to' => $headers['reply-to'] ?? null,
            'subject' => $headers['subject'] ?? null,
            'message_id' => $headers['message-id'] ?? null,
            'date_sent' => isset($headers['date']) ? $this->parseDate($headers['date']) : null,
            'mailer' => $headers['x-mailer'] ?? $headers['user-agent'] ?? null,
            'sender_ip' => $this->extractSenderIp($headers),
            'headers' => json_encode($headers),
        ];

        return $metadata;
    }

    /**
     * Parse date string thành Carbon instance
     * 
     * @param string|null $dateString Date string
     * @return Carbon|null
     */
    private function parseDate(?string $dateString): ?Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Thử parse với nhiều format
            $formats = [
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'd/m/Y H:i:s',
                'd/m/Y H:i',
                'd/m/Y',
                'Y-m-d\TH:i:s\Z',
                'Y-m-d\TH:i:s.u\Z',
                Carbon::RFC2822,
                Carbon::RFC3339,
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $dateString);
                } catch (Exception $e) {
                    continue;
                }
            }

            // Thử parse tự động
            return Carbon::parse($dateString);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Generate Message-ID
     * 
     * @return string Message-ID
     */
    private function generateMessageId(): string
    {
        $domain = config('app.url') ?? 'localhost';
        $domain = parse_url($domain, PHP_URL_HOST) ?? 'localhost';
        $uniqueId = uniqid() . '.' . time() . '@' . $domain;
        
        return '<' . $uniqueId . '>';
    }

    /**
     * Extract sender IP từ headers
     * 
     * @param array $headers Parsed headers
     * @return string|null IP address
     */
    private function extractSenderIp(array $headers): ?string
    {
        // Thử các header thường chứa IP
        $ipHeaders = [
            'x-forwarded-for',
            'x-real-ip',
            'x-client-ip',
            'remote-addr',
            'client-ip',
            'x-originating-ip',
        ];

        foreach ($ipHeaders as $header) {
            if (isset($headers[$header])) {
                $ip = trim(explode(',', $headers[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return null;
    }

    /**
     * Parse headers JSON string
     * 
     * @param string|null $jsonString JSON string
     * @return array|null
     */
    private function parseHeadersJson(?string $jsonString): ?array
    {
        if (empty($jsonString)) {
            return null;
        }

        try {
            $decoded = json_decode($jsonString, true);
            return is_array($decoded) ? $decoded : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Parse attachments JSON string
     * 
     * @param string|null $jsonString JSON string
     * @return array|null
     */
    private function parseAttachmentsJson(?string $jsonString): ?array
    {
        if (empty($jsonString)) {
            return null;
        }

        try {
            $decoded = json_decode($jsonString, true);
            return is_array($decoded) ? $decoded : null;
        } catch (Exception $e) {
            // Nếu không phải JSON, có thể là comma-separated list
            $attachments = array_map('trim', explode(',', $jsonString));
            return array_filter($attachments);
        }
    }

    /**
     * Format metadata để hiển thị
     * 
     * @param array $metadata Metadata array
     * @return array Formatted metadata
     */
    public function formatForDisplay(array $metadata): array
    {
        $formatted = $metadata;

        // Format dates
        if ($formatted['date_sent'] instanceof Carbon) {
            $formatted['date_sent_formatted'] = $formatted['date_sent']->format('Y-m-d H:i:s');
        }
        if ($formatted['date_received'] instanceof Carbon) {
            $formatted['date_received_formatted'] = $formatted['date_received']->format('Y-m-d H:i:s');
        }

        // Parse headers JSON nếu có
        if (is_string($formatted['headers'])) {
            $formatted['headers_parsed'] = json_decode($formatted['headers'], true);
        }

        // Parse attachments JSON nếu có
        if (is_string($formatted['attachments_info'])) {
            $formatted['attachments_parsed'] = json_decode($formatted['attachments_info'], true);
        }

        return $formatted;
    }
}

