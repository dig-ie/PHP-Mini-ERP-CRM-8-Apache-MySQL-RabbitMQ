<?php

namespace App\Services;

use Exception;

class AsaasService
{
    private string $apiUrl;
    private string $accessToken;
    private bool $debugMode = false;

    public function __construct()
    {
        $this->apiUrl = getenv('ASAAS_API_URL') ?: 'https://api-sandbox.asaas.com/v3';
        $this->accessToken = getenv('ASAAS_ACCESS_TOKEN');
        
        if (empty($this->accessToken)) {
            throw new Exception('ASAAS_ACCESS_TOKEN not configured in .env file');
        }
    }

    /**
     * Creates a new customer in Asaas
     * 
     * @param array $customerData Customer data
     * @return array Created customer data
     * @throws Exception
     */
    public function createCustomer(array $customerData): array
    {
        $endpoint = '/customers';
        
        $requiredFields = ['name', 'cpfCnpj'];
        foreach ($requiredFields as $field) {
            if (empty($customerData[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }
        
        if (!empty($customerData['postalCode'])) {
            unset($customerData['city']);
            unset($customerData['province']);
            unset($customerData['address']);
        }
        
        return $this->sendRequest('POST', $endpoint, $customerData);
    }

    /**
     * Lists customers from Asaas
     * 
     * @param array $filters Search filters (name, email, cpfCnpj, etc.)
     * @return array Customer list
     * @throws Exception
     */
    public function listCustomers(array $filters = []): array
    {
        $endpoint = '/customers';
        
        if (!empty($filters)) {
            $endpoint .= '?' . http_build_query($filters);
        }
        
        return $this->sendRequest('GET', $endpoint);
    }

    /**
     * Gets a specific customer by ID
     * 
     * @param string $customerId Customer ID
     * @return array Customer data
     * @throws Exception
     */
    public function getCustomer(string $customerId): array
    {
        $endpoint = "/customers/{$customerId}";
        
        return $this->sendRequest('GET', $endpoint);
    }

    /**
     * Updates an existing customer
     * 
     * @param string $customerId Customer ID
     * @param array $customerData Updated customer data
     * @return array Updated customer data
     * @throws Exception
     */
    public function updateCustomer(string $customerId, array $customerData): array
    {
        $endpoint = "/customers/{$customerId}";
        
        return $this->sendRequest('PUT', $endpoint, $customerData);
    }

    /**
     * Deletes a customer
     * 
     * @param string $customerId Customer ID
     * @return array API response
     * @throws Exception
     */
    public function deleteCustomer(string $customerId): array
    {
        $endpoint = "/customers/{$customerId}";
        
        return $this->sendRequest('DELETE', $endpoint);
    }

    /**
     * Gets city information by ID
     * 
     * @param string $cityId City ID
     * @return array City information
     * @throws Exception
     */
    public function getCity(string $cityId): array
    {
        $endpoint = "/cities/{$cityId}";
        
        return $this->sendRequest('GET', $endpoint);
    }

    /**
     * Checks if a customer already exists based on CPF/CNPJ
     * 
     * @param string $cpfCnpj Customer CPF or CNPJ
     * @return array|null Customer data if found, null otherwise
     * @throws Exception
     */
    public function findCustomerByCpfCnpj(string $cpfCnpj): ?array
    {
        try {
            $customers = $this->listCustomers(['cpfCnpj' => $cpfCnpj]);
            
            if (!empty($customers['data']) && count($customers['data']) > 0) {
                return $customers['data'][0];
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Enables or disables debug mode
     * 
     * @param bool $debug
     */
    public function setDebugMode(bool $debug = true): void
    {
        $this->debugMode = $debug;
    }

    /**
     * Tests connection with Asaas API
     * 
     * @return array Connection information
     * @throws Exception
     */
    public function testConnection(): array
    {
        try {
            $response = $this->sendRequest('GET', '/customers?limit=1');
            return [
                'success' => true,
                'message' => 'Connection with Asaas API established successfully',
                'apiUrl' => $this->apiUrl,
                'hasAccessToken' => !empty($this->accessToken)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error connecting to Asaas API: ' . $e->getMessage(),
                'apiUrl' => $this->apiUrl,
                'hasAccessToken' => !empty($this->accessToken)
            ];
        }
    }

    /**
     * Creates a payment (cobrança) in Asaas
     *
     * @param array $paymentData Required: customer, billingType, value, dueDate
     * @return array Created payment payload
     * @throws Exception
     */
    public function createPayment(array $paymentData): array
    {
        $endpoint = '/payments';

        $required = ['customer', 'billingType', 'value', 'dueDate'];
        foreach ($required as $field) {
            if (!isset($paymentData[$field]) || $paymentData[$field] === '' || $paymentData[$field] === null) {
                throw new Exception("Field {$field} is required to create payment");
            }
        }

        return $this->sendRequest('POST', $endpoint, $paymentData);
    }

    /**
     * Sends a request to Asaas API
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint
     * @param array $data Data to be sent
     * @return array API response
     * @throws Exception
     */
    private function sendRequest(string $method, string $endpoint, array $data = []): array
    {
        $curl = curl_init();
        
        $url = $this->apiUrl . $endpoint;
        $headers = [
            'accept: application/json',
            'access_token: ' . $this->accessToken,
            'content-type: application/json',
            'user-agent: Asaas API PHP Client'
        ];
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
        
        if ($method === 'POST' || $method === 'PUT') {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }
        
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlInfo = curl_getinfo($curl);
        
        curl_close($curl);
        
        if ($this->debugMode) {
            error_log("=== ASAAS API DEBUG ===");
            error_log("URL: $url");
            error_log("Method: $method");
            error_log("Headers: " . json_encode($headers));
            error_log("Data: " . json_encode($data, JSON_UNESCAPED_UNICODE));
            error_log("Response Code: $httpCode");
            error_log("Response: $response");
            error_log("cURL Info: " . json_encode($curlInfo));
            error_log("========================");
        }
        
        if ($err) {
            throw new Exception("cURL Error: $err | URL: $url | Method: $method");
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = 'Error in Asaas API request';
            $errorDetails = '';
            $debugInfo = '';
            
            error_log("Asaas API Error Response (HTTP $httpCode): " . $response);
            
            if (isset($responseData['errors']) && is_array($responseData['errors'])) {
                $errors = [];
                foreach ($responseData['errors'] as $error) {
                    $field = $error['field'] ?? 'unspecified field';
                    $description = $error['description'] ?? 'unspecified error';
                    $errors[] = "Field '{$field}': {$description}";
                }
                $errorMessage = 'Validation error in Asaas API';
                $errorDetails = ' | Errors: ' . implode('; ', $errors);
            } elseif (!empty($responseData['message'])) {
                $errorMessage = $responseData['message'];
            } elseif (!empty($responseData['error'])) {
                $errorMessage = $responseData['error'];
            }
            
            $debugInfo = " | URL: $url | Method: $method";
            if (!empty($data)) {
                $debugInfo .= " | Sent data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            $debugInfo .= " | Complete response: " . $response;
            
            throw new Exception($errorMessage . ' (HTTP ' . $httpCode . ')' . $errorDetails . $debugInfo, $httpCode);
        }
        
        return $responseData;
    }
}