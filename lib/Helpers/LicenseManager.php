<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Cms\App;
use Kirby\Exception\Exception;

/**
 * LicenseManager class for managing the license
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class LicenseManager
{
    private const PREFIX = 'KEM';
    private const LICENSE_FILE = '.kem_licence';

    private App $kirby;
    private ?array $licenseData = null;
    private ?bool $isValid = null;

    public function __construct()
    {
        $this->kirby = App::instance();
    }

    /**
     * Checks if the license is activated
     */
    public function isActivated(): bool
    {
        if ($this->isValid === null) {
            $this->isValid = $this->validateLicense();
        }

        return $this->isValid;
    }

    /**
     * Returns the license information
     */
    public function getLicenseInfo(): ?array
    {
        if (!$this->isActivated()) {
            return null;
        }

        if ($this->licenseData === null) {
            $this->loadLicenseData();
        }

        return $this->licenseData;
    }

    /**
     * Validates the license file
     */
    private function validateLicense(): bool
    {
        $licenseFile = $this->getLicenseFilePath();

        if (!file_exists($licenseFile)) {
            return false;
        }

        try {
            $this->loadLicenseData();
            return $this->validateLicenseData();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Loads the license data from the file
     */
    private function loadLicenseData(): void
    {
        $licenseFile = $this->getLicenseFilePath();

        if (!file_exists($licenseFile)) {
            throw new Exception('License file not found');
        }

        $content = file_get_contents($licenseFile);
        if ($content === false) {
            throw new Exception('Could not read license file');
        }

        $this->licenseData = json_decode($content, true);
        if ($this->licenseData === null) {
            throw new Exception('Invalid license file format');
        }
    }

    /**
     * Validates the license data
     */
    private function validateLicenseData(): bool
    {
        if (!$this->licenseData) {
            return false;
        }

        // check required fields
        $requiredFields = ['product', 'code', 'domain', 'issued_at', 'signature'];
        foreach ($requiredFields as $field) {
            if (!isset($this->licenseData[$field])) {
                return false;
            }
        }

        // check product prefix
        if ($this->licenseData['product'] !== self::PREFIX) {
            return false;
        }

        // check domain (if set)
        if (isset($this->licenseData['domain']) && !empty($this->licenseData['domain'])) {
            $currentDomain = $this->getCurrentDomain();
            if ($this->licenseData['domain'] !== $currentDomain) {
                return false;
            }
        }

        // Signatur-Validierung mit RSA
        $signatureValid = $this->validateSignature();
        if (!$signatureValid) {
            return false;
        }

        return true;
    }

    /**
     * Validates the license signature using RSA
     */
    private function validateSignature(): bool
    {
        if (!isset($this->licenseData['signature']) || empty($this->licenseData['signature'])) {
            return false;
        }

        // Get the public key
        $pubKeyPath = __DIR__ . '/../../config/kem.pub';
        if (!file_exists($pubKeyPath)) {
            return false;
        }

        $pubKey = file_get_contents($pubKeyPath);
        if ($pubKey === false) {
            return false;
        }

        // Create signature data (all fields except signature)
        $data = json_encode($this->signatureData(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = base64_decode($this->licenseData['signature']);

        // Verify signature
        $result = openssl_verify($data, $signature, $pubKey, OPENSSL_ALGO_SHA256);

        if ($result === -1) {
            return false;
        }

        return $result === 1;
    }

    /**
     * Creates the data array for signature validation (all fields except signature)
     */
    private function signatureData(): array
    {
        $data = $this->licenseData;
        unset($data['signature']); // remove signature from data
        return $data;
    }

    /**
     * Returns the current domain
     */
    private function getCurrentDomain(): string
    {
        $url = $this->kirby->url();
        $parsed = parse_url($url);
        return $parsed['host'] ?? 'localhost';
    }

    /**
     * Returns the path to the license file
     */
    private function getLicenseFilePath(): string
    {
        return $this->kirby->root('config') . '/' . self::LICENSE_FILE;
    }

    /**
     * Returns the email from the license (for automatic renewal)
     */
    public function getEmail(): ?string
    {
        $info = $this->getLicenseInfo();
        return $info['email'] ?? null;
    }

    /**
     * Returns the license key from the license (for automatic renewal)
     */
    public function getLicenseKey(): ?string
    {
        $info = $this->getLicenseInfo();
        return $info['code'] ?? null;
    }

    /**
     * Checks if the license needs renewal (subscription expired + grace period passed)
     */
    public function needsRenewal(): bool
    {
        if (!$this->isActivated()) {
            return false;
        }

        if ($this->licenseData === null) {
            $this->loadLicenseData();
        }

        if (!$this->licenseData || $this->licenseData['type'] !== 'subscription') {
            return false;
        }

        if (!isset($this->licenseData['period']['ends_at'])) {
            return false;
        }

        $endDate = new \DateTime($this->licenseData['period']['ends_at']);
        $graceDays = $this->licenseData['grace_days'] ?? 0;
        $endDate->add(new \DateInterval("P{$graceDays}D"));

        $now = new \DateTime();
        return $now > $endDate;
    }

    /**
     * Attempts to automatically renew the license using the plugin API
     */
    public function attemptAutoRenewal(): bool
    {
        if (!$this->needsRenewal()) {
            return false;
        }

        $email = $this->getEmail();
        $licenseKey = $this->getLicenseKey();

        if (!$email || !$licenseKey) {
            return false;
        }

        // Call plugin API for renewal (same as manual activation)
        $domain = $this->getCurrentDomain();

        $data = [
            'license_key' => $licenseKey,
            'email' => $email,
            'domain' => $domain
        ];

        $licenseFile = $this->getLicenseFilePath();
        if (file_exists($licenseFile)) {
            unlink($licenseFile);
        }

        $response = $this->callPluginApi('/licenses/activate', $data);

        if ($response && isset($response['license'])) {
            // Save new license
            $licenseFile = $this->getLicenseFilePath();
            $licenseData = json_encode($response['license'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $success = file_put_contents($licenseFile, $licenseData) !== false;

            if ($success) {
                // Reset cached data to force reload
                $this->licenseData = null;
                $this->isValid = null;
            }

            return $success;
        }

        return false;
    }

    /**
     * Makes API call to plugin API (same as manual activation)
     */
    private function callPluginApi(string $endpoint, array $data): ?array
    {
        $url = 'https://philippoehrlein.de' . $endpoint;

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            return null;
        }

        return json_decode($result, true);
    }
}
