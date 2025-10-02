# Mini ERP/CRM — Boilerplate (Apache + PHP 8 + MySQL + RabbitMQ)

## 🚀 Início Rápido

### Opção 1: Script Automático (Recomendado)

```bash
# Windows
start.bat

# Linux/Mac
chmod +x start.sh
./start.sh
```

### Opção 2: Docker Compose Manual

```bash
# Na raiz do projeto
docker-compose up -d
```

## 🌐 Acessos

- **Aplicação Web:** http://localhost:8080
- **RabbitMQ Management:** http://localhost:15672 (guest/guest)
- **MySQL:** localhost:3307 (erp_user/erp_pass)

## 🔧 Comandos Úteis

```bash
# Ver status dos containers
docker-compose ps

# Ver logs do worker
docker logs -f worker

# Acessar container da aplicação
docker exec -it php_apache bash

# Parar sistema
docker-compose down
```

### Banco de dados

- MySQL exposto em 3307 (host) -> 3306 (container)
- DB: erp, user: erp_user, pass: erp_pass
- Schema inicial em `sql/init.sql` (admin: admin@example.com / senha: admin123)

### Estrutura

- `public/` DocumentRoot e roteador (`index.php`)
- `src/` Controllers, Models, Services, Queue
- `config/` `db.php` (PDO)
- `bin/` `worker.php` (consumidor RabbitMQ)

## 🏭 Diferenças para Produção

### Desenvolvimento (Atual)

- ✅ Worker automático via Docker Compose
- ✅ Restart automático (`restart: unless-stopped`)
- ✅ Logs centralizados
- ✅ Ambiente isolado

### Produção (Recomendações)

- 🔄 **Process Manager:** Supervisor, PM2, ou Systemd
- 📊 **Monitoramento:** Prometheus + Grafana
- 🔒 **Segurança:** Secrets management, HTTPS
- 📈 **Escalabilidade:** Kubernetes, Docker Swarm
- 🗄️ **Banco:** RDS, Cloud SQL (gerenciado)
- 🐰 **RabbitMQ:** Amazon MQ, CloudAMQP (gerenciado)
- 📝 **Logs:** ELK Stack, CloudWatch
- 🔄 **CI/CD:** GitHub Actions, GitLab CI

### Exemplo de Produção (Kubernetes)

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: order-worker
spec:
  replicas: 3
  template:
    spec:
      containers:
        - name: worker
          image: php-app:latest
          command: ["php", "bin/worker.php"]
          resources:
            requests:
              memory: "128Mi"
              cpu: "100m"
            limits:
              memory: "256Mi"
              cpu: "200m"
```

## 📝 Observações para processos seletivos

- **Ambiente legado:** Apache + mod_php (simulado em Docker)
- **Mensageria:** RabbitMQ com `php-amqplib`
- **SQL:** PDO com prepared statements
- **Arquitetura:** Front Controller + MVC + Worker Pattern
- **Containerização:** Docker multi-service
