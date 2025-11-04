# Documentação da API Pública

Esta documentação descreve os endpoints da API pública disponíveis para integração.

## Autenticação

Atualmente, a maioria dos endpoints públicos não requer autenticação. Para endpoints que exigem autenticação (como o registro de eventos de analytics associados a um usuário/empresa), a autenticação via `sanctum` ou `oauth` pode ser implementada.

## Endpoints Disponíveis

### 1. Status da API

Verifica se a API está funcionando.

- **GET** `/api/ping`

**Resposta de Sucesso:**
```json
{
  "message": "API funcionando ✅"
}
```

### 2. Detalhes da Empresa

Retorna informações detalhadas sobre uma empresa específica.

- **GET** `/api/companies/{company_id}`

**Parâmetros de Path:**
- `company_id` (inteiro, obrigatório): O ID da empresa.

**Resposta de Sucesso:**
```json
{
  "id": 1,
  "name": "Nome da Empresa",
  "cnpj": "XX.XXX.XXX/XXXX-XX",
  "email": "empresa@example.com",
  "phone": "(XX) XXXX-XXXX",
  "address": "Rua Exemplo, 123",
  "city": "Cidade",
  "state": "Estado",
  "zip_code": "XXXXX-XXX",
  "description": "Descrição da empresa aqui.",
  "website": "http://www.empresa.com",
  "logo": "logos/logo_empresa.png",
  "is_active": 1,
  "created_at": "2023-01-01T10:00:00.000000Z",
  "updated_at": "2023-01-01T10:00:00.000000Z"
}
```

### 3. Itens do Marketplace

Lista todos os itens disponíveis no marketplace com paginação.

- **GET** `/api/marketplace/items`

**Parâmetros de Query (Opcionais):**
- `page` (inteiro): O número da página a ser retornada (padrão: 1).
- `per_page` (inteiro): O número de itens por página (padrão: 10).

**Resposta de Sucesso:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "company_id": 1,
      "category_id": 1,
      "store_id": 1,
      "name": "Vestido Floral de Verão",
      "description": "Vestido leve e arejado com estampa floral vibrante, perfeito para o verão.",
      "brand": "Marca A",
      "size": "M",
      "colors": ["vermelho", "verde"],
      "condition": "novo",
      "purchase_price": "99.99",
      "purchase_date": "2023-05-10",
      "images": ["item_images/vestido1.jpg"],
      "tags": ["verão", "floral"],
      "usage_count": 5,
      "last_used": "2023-09-01",
      "is_favorite": false,
      "season": "verão",
      "occasion": "casual",
      "created_at": "2023-01-01T10:00:00.000000Z",
      "updated_at": "2023-01-01T10:00:00.000000Z",
      "user": { ... }, // Detalhes do usuário
      "category": { ... } // Detalhes da categoria
    }
  ],
  "first_page_url": "http://localhost:8000/api/marketplace/items?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost:8000/api/marketplace/items?page=1",
  "links": [ ... ],
  "next_page_url": null,
  "path": "http://localhost:8000/api/marketplace/items",
  "per_page": 10,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

### 4. Detalhes de um Item do Marketplace

Retorna informações detalhadas sobre um item específico do marketplace.

- **GET** `/api/marketplace/items/{item_id}`

**Parâmetros de Path:**
- `item_id` (inteiro, obrigatório): O ID do item.

**Resposta de Sucesso:**
```json
{
  "id": 1,
  "user_id": 1,
  "company_id": 1,
  "category_id": 1,
  "store_id": 1,
  "name": "Vestido Floral de Verão",
  "description": "Vestido leve e arejado com estampa floral vibrante, perfeito para o verão.",
  "brand": "Marca A",
  "size": "M",
  "colors": ["vermelho", "verde"],
  "condition": "novo",
  "purchase_price": "99.99",
  "purchase_date": "2023-05-10",
  "images": ["item_images/vestido1.jpg"],
  "tags": ["verão", "floral"],
  "usage_count": 5,
  "last_used": "2023-09-01",
  "is_favorite": false,
  "season": "verão",
  "occasion": "casual",
  "created_at": "2023-01-01T10:00:00.000000Z",
  "updated_at": "2023-01-01T10:00:00.000000Z",
  "user": { ... }, // Detalhes do usuário
  "company": { ... }, // Detalhes da empresa
  "category": { ... }, // Detalhes da categoria
  "store": { ... } // Detalhes da loja
}
```

### 5. Registrar Evento de Analytics

Permite registrar eventos de interação do usuário ou da empresa na plataforma.

- **POST** `/api/analytics/event`

**Corpo da Requisição (JSON):**
```json
{
  "event_type": "item_viewed",
  "payload": {
    "item_id": 123,
    "source": "mobile_app"
  }
}
```

**Parâmetros do Corpo:**
- `event_type` (string, obrigatório): O tipo de evento (ex: `item_viewed`, `promotion_clicked`, `search_performed`).
- `payload` (objeto, opcional): Dados adicionais relevantes para o evento.

**Resposta de Sucesso (201 Created):**
```json
{
  "user_id": 1,
  "company_id": null,
  "event_type": "item_viewed",
  "payload": {
    "item_id": 123,
    "source": "mobile_app"
  },
  "updated_at": "2023-10-13T22:30:00.000000Z",
  "created_at": "2023-10-13T22:30:00.000000Z",
  "id": 1
}
```

### 6. Relatório de Analytics

Retorna um relatório agregado de eventos de analytics.

- **GET** `/api/analytics/report`

**Parâmetros de Query:**
- `event_type` (string, obrigatório): O tipo de evento para o qual o relatório será gerado.
- `start_date` (data, opcional): Data de início para o filtro (formato YYYY-MM-DD).
- `end_date` (data, opcional): Data de fim para o filtro (formato YYYY-MM-DD).

**Resposta de Sucesso:**
```json
[
  {
    "date": "2023-10-10",
    "total": 50
  },
  {
    "date": "2023-10-11",
    "total": 75
  }
]
```

