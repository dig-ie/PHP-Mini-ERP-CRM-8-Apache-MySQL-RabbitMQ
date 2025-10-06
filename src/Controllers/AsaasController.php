<?php

namespace App\Controllers;

use App\Services\AsaasService;
use Exception;

class AsaasController
{
    private AsaasService $asaasService;

    public function __construct()
    {
        $this->asaasService = new AsaasService();
        $this->asaasService->setDebugMode(true);
    }

    /**
     * Creates a new customer in Asaas
     * 
     * @param array $data Customer data
     * @return array API response
     */
    public function createCustomer(array $data): array
    {
        try {
            $requiredFields = ['name', 'cpfCnpj'];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "Field {$field} is required"
                    ];
                }
            }
            
            $customerData = [
                'name' => $data['name'],
                'cpfCnpj' => $data['cpfCnpj']
            ];
            
            $optionalFields = [
                'email', 'phone', 'mobilePhone', 'address', 'addressNumber',
                'complement', 'province', 'postalCode', 'city', 'externalReference',
                'notificationDisabled', 'additionalEmails', 'municipalInscription',
                'stateInscription', 'observations', 'groupName', 'company', 'foreignCustomer'
            ];
            
            foreach ($optionalFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $customerData[$field] = $data[$field];
                }
            }
            
            $response = $this->asaasService->createCustomer($customerData);
            
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Lists customers from Asaas
     * 
     * @param array $filters Search filters
     * @return array Customer list
     */
    public function listCustomers(array $filters = []): array
    {
        try {
            $response = $this->asaasService->listCustomers($filters);
            
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Gets a specific customer by ID
     * 
     * @param string $customerId Customer ID
     * @return array Customer data
     */
    public function getCustomer(string $customerId): array
    {
        try {
            $response = $this->asaasService->getCustomer($customerId);
            
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Updates an existing customer
     * 
     * @param string $customerId Customer ID
     * @param array $data Updated customer data
     * @return array API response
     */
    public function updateCustomer(string $customerId, array $data): array
    {
        try {
            $response = $this->asaasService->updateCustomer($customerId, $data);
            
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Deletes a customer
     * 
     * @param string $customerId Customer ID
     * @return array API response
     */
    public function deleteCustomer(string $customerId): array
    {
        try {
            $response = $this->asaasService->deleteCustomer($customerId);
            
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Checks if a customer already exists based on CPF/CNPJ
     * 
     * @param string $cpfCnpj Customer CPF or CNPJ
     * @return array Customer data if found
     */
    public function findCustomerByCpfCnpj(string $cpfCnpj): array
    {
        try {
            $customer = $this->asaasService->findCustomerByCpfCnpj($cpfCnpj);
            
            if ($customer) {
                return [
                    'success' => true,
                    'data' => $customer,
                    'message' => 'Customer found'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Customer not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Gets city information by ID
     * 
     * @param string $cityId City ID
     * @return array City information
     */
    public function getCity(string $cityId): array
    {
        try {
            $response = $this->asaasService->getCity($cityId);
            
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Tests connection with Asaas API
     * 
     * @return array Connection information
     */
    public function testConnection(): array
    {
        try {
            $response = $this->asaasService->testConnection();
            
            return $response;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
