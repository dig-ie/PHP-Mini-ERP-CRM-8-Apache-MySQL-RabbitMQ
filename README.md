# CRM ERP

## Integração com Asaas

Este projeto inclui uma integração com a API da Asaas para gerenciamento de clientes e cobranças.

### Configuração

1. Copie o arquivo `env.example` para `.env`:

   ```
   cp env.example .env
   ```

2. Configure as variáveis de ambiente da Asaas no arquivo `.env`:

   ```
   ASAAS_API_URL=https://api-sandbox.asaas.com/v3
   ASAAS_ACCESS_TOKEN=seu_token_aqui
   ```

   - Para ambiente de sandbox, use: `https://api-sandbox.asaas.com/v3`
   - Para ambiente de produção, use: `https://api.asaas.com/v3`

### Exemplo de uso do serviço Asaas

```php
// Criar um cliente
$asaasService = new \App\Services\AsaasService();

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

try {
    $customer = $asaasService->createCustomer($customerData);
    print_r($customer);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
```

### Documentação da API

Para mais informações sobre a API da Asaas, consulte a [documentação oficial](https://docs.asaas.com/reference).
