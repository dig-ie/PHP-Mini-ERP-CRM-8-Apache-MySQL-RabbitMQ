<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

use App\Controllers\AsaasController;


$customerData = [
    "name" => "Cliente teste 1",
    "cpfCnpj" => "08651423438",
    "email" => "john.doe@asaas.com.br",
    "phone" => "4738010919",
    "mobilePhone" => "4799376637",
    "address" => "Av. Paulista",
    "addressNumber" => "150",
    "complement" => "Sala 201",
    "province" => "Centro",
    "postalCode" => "01310-000",
    "externalReference" => "12987382",
    "notificationDisabled" => false,
    "additionalEmails" => "john.doe@asaas.com,john.doe.silva@asaas.com.br",
    "municipalInscription" => "46683695908",
    "stateInscription" => "646681195275",
    "observations" => "ótimo pagador, nenhum problema até o momento",
    "groupName" => null,
    "company" => null,
    "foreignCustomer" => false
];

echo "Testando conexão com a API da Asaas...\n";

try {
    $asaasController = new AsaasController();
    
    // Test connection first
    $connectionTest = $asaasController->testConnection();
    echo "Status da conexão: " . ($connectionTest['success'] ? '✅ OK' : '❌ ERRO') . "\n";
    echo "URL da API: " . $connectionTest['apiUrl'] . "\n";
    echo "Token configurado: " . ($connectionTest['hasAccessToken'] ? '✅ Sim' : '❌ Não') . "\n";
    
    if (!$connectionTest['success']) {
        echo "❌ Erro na conexão: " . $connectionTest['message'] . "\n";
        exit(1);
    }
    
    echo "\nVerificando se cliente já existe...\n";
    
    // Check if customer already exists by CPF/CNPJ
    $existingCustomer = $asaasController->findCustomerByCpfCnpj($customerData['cpfCnpj']);
    
    if ($existingCustomer['success']) {
        echo "⚠️  Cliente já existe!\n";
        echo "ID: " . $existingCustomer['data']['id'] . "\n";
        echo "Nome: " . $existingCustomer['data']['name'] . "\n";
        echo "Email: " . $existingCustomer['data']['email'] . "\n\n";
        
        echo "Deseja continuar mesmo assim? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim($line) !== 'y' && trim($line) !== 'Y') {
            echo "Operação cancelada.\n";
            exit(0);
        }
    }
    
    echo "Criando cliente na Asaas...\n";
    echo "Dados: " . json_encode($customerData, JSON_PRETTY_PRINT) . "\n\n";
    
    $result = $asaasController->createCustomer($customerData);
    
    echo "Resultado:\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
    
    if ($result['success']) {
        echo "\n✅ Cliente criado com sucesso!\n";
        echo "ID do cliente: " . $result['data']['id'] . "\n";
        echo "Nome: " . $result['data']['name'] . "\n";
        echo "Email: " . $result['data']['email'] . "\n";
        echo "CPF/CNPJ: " . $result['data']['cpfCnpj'] . "\n";
    } else {
        echo "\n❌ Erro ao criar cliente: " . $result['message'] . "\n";
        echo "\n🔍 Para mais detalhes, verifique os logs do servidor ou habilite o modo debug.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Erro: " . $e->getMessage() . "\n";
}