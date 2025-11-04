# Closet Fashion - Sistema de Gerenciamento de Guarda-Roupa Digital

## Visão Geral

O **Closet Fashion** é um sistema completo de gerenciamento de guarda-roupa digital desenvolvido em Laravel, utilizando arquitetura MVC (Model-View-Controller) e implementando o padrão Repository como camada de persistência de dados. O sistema oferece uma solução moderna e intuitiva para organização pessoal de roupas e acessórios.

## Características Técnicas

- **Framework**: Laravel 10.x
- **Linguagem**: PHP 8.1+
- **Banco de Dados**: SQLite (configurável para MySQL/PostgreSQL)
- **Arquitetura**: MVC com padrão Repository
- **Frontend**: Bootstrap 5 + CSS customizado
- **Autenticação**: Laravel Auth
- **Design**: Interface minimalista e responsiva

## Funcionalidades Implementadas

### 1. **Cadastro de Usuário**
Sistema completo de registro e autenticação com:
- Registro de novos usuários
- Login/logout seguro
- Validação de dados
- Sessões persistentes

### 2. **Perfil de Usuário**
Gerenciamento completo de dados pessoais:
- Informações básicas do usuário
- Preferências personalizadas
- Configurações de conta

### 3. **Inventário Digital**
Catálogo completo de roupas e acessórios:
- Cadastro de itens com detalhes
- Upload de imagens
- Organização por categorias
- Busca e filtros avançados

### 4. **Sistema de Favoritos**
Marcação e organização de peças favoritas:
- Adicionar/remover favoritos
- Visualização rápida de itens preferidos
- Organização personalizada

### 5. **Categorização Inteligente**
Organização por categorias personalizáveis:
- Categorias pré-definidas
- Criação de categorias customizadas
- Filtros por categoria
- Estatísticas por categoria

### 6. **Filtros de Busca Avançados**
Sistema completo de pesquisa:
- Busca por nome, cor, marca
- Filtros múltiplos
- Ordenação personalizada
- Resultados em tempo real

### 7. **Gestão de Promoções** ✨ **NOVO**
Sistema de promoções e descontos:
- Cadastro de promoções
- Vinculação com lojas
- Controle de validade
- Notificações de ofertas

### 8. **Controle de Orçamentos** ✨ **NOVO**
Planejamento financeiro inteligente:
- Definição de orçamentos mensais
- Controle de gastos
- Relatórios financeiros
- Alertas de limite

### 9. **Sistema de Recordações**
Anotações e memórias das peças:
- Notas personalizadas
- Histórico de uso
- Ocasiões especiais
- Lembretes importantes

### 10. **Cadastro de Lojas Parceiras**
Registro e gestão de empresas:
- Informações completas das lojas
- Contatos e localização
- Redes sociais integradas
- Classificação por tipo (física/online)

## Estrutura do Projeto

```
closet-fashion/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ItemController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── FavoriteController.php
│   │   │   ├── BudgetController.php
│   │   │   ├── MemoryController.php
│   │   │   ├── StoreController.php
│   │   │   └── PromotionController.php
│   │   └── Middleware/
│   │       └── CheckResourceOwnership.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── UserProfile.php
│   │   ├── Item.php
│   │   ├── Category.php
│   │   ├── Favorite.php
│   │   ├── Budget.php
│   │   ├── Memory.php
│   │   ├── Store.php
│   │   └── Promotion.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── BaseRepositoryInterface.php
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── ItemRepositoryInterface.php
│   │   │   ├── CategoryRepositoryInterface.php
│   │   │   └── BudgetRepositoryInterface.php
│   │   └── Eloquent/
│   │       ├── BaseRepository.php
│   │       ├── UserRepository.php
│   │       ├── ItemRepository.php
│   │       ├── CategoryRepository.php
│   │       └── BudgetRepository.php
│   └── Providers/
│       └── RepositoryServiceProvider.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard.blade.php
│       ├── items/
│       │   └── index.blade.php
│       └── stores/
│           └── index.blade.php
└── routes/
    └── web.php
```

## Padrão Repository Implementado

O sistema utiliza o padrão Repository para abstrair a camada de acesso a dados:

### Interface Base
```php
interface BaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
```

### Implementação
```php
class BaseRepository implements BaseRepositoryInterface
{
    protected $model;
    
    public function __construct($model)
    {
        $this->model = $model;
    }
    
    // Métodos implementados...
}
```

## Design e Interface

O sistema apresenta um design minimalista e moderno com:

- **Paleta de Cores**: Tons neutros com rosa claro e cinza
- **Layout**: Sidebar fixa com navegação intuitiva
- **Responsividade**: Adaptável a dispositivos móveis
- **Tipografia**: Fonte Inter para melhor legibilidade
- **Componentes**: Cards organizados e botões estilizados

## Instalação e Configuração

### Pré-requisitos
- PHP 8.1 ou superior
- Composer
- Node.js (opcional, para assets)

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone <repository-url>
cd closet-fashion
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```bash
# Para SQLite (padrão)
touch database/database.sqlite

# Para MySQL, edite o .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=closet_fashion
DB_USERNAME=root
DB_PASSWORD=
```

5. **Execute as migrações**
```bash
php artisan migrate --seed
```

6. **Configure o storage**
```bash
php artisan storage:link
```

7. **Inicie o servidor**
```bash
php artisan serve
```

## Uso do Sistema

### Acesso Inicial
1. Acesse `http://localhost:8000`
2. Clique em "Cadastrar" para criar uma conta
3. Preencha os dados solicitados
4. Faça login com suas credenciais

### Navegação Principal
- **Perfil**: Informações do usuário
- **Meu Armário**: Inventário de roupas
- **Lista de Compras**: Categorias organizadas
- **Filtro de Busca**: Pesquisa avançada
- **Comunicação**: Interações do sistema
- **Lojist**: Lojas parceiras cadastradas

### Funcionalidades Principais

#### Gerenciamento de Itens
1. Acesse "Meu Armário"
2. Clique em "Adicionar Item"
3. Preencha as informações da peça
4. Faça upload da imagem
5. Selecione a categoria
6. Salve o item

#### Organização por Categorias
1. Acesse "Lista de Compras"
2. Visualize as categorias existentes
3. Crie novas categorias conforme necessário
4. Organize seus itens por categoria

#### Sistema de Favoritos
1. Na listagem de itens, clique no ícone de coração
2. Acesse a seção "Favoritos" para ver itens marcados
3. Remova favoritos quando necessário

#### Controle de Orçamentos
1. Defina orçamentos mensais
2. Registre gastos com roupas
3. Acompanhe relatórios financeiros
4. Receba alertas de limite

## Segurança

O sistema implementa várias camadas de segurança:

- **Autenticação**: Sistema robusto do Laravel
- **Autorização**: Middleware personalizado
- **Validação**: Validação de dados em todas as entradas
- **CSRF Protection**: Proteção contra ataques CSRF
- **SQL Injection**: Proteção via Eloquent ORM

## Performance

Otimizações implementadas:

- **Eager Loading**: Carregamento otimizado de relacionamentos
- **Paginação**: Listagens paginadas para melhor performance
- **Caching**: Cache de consultas frequentes
- **Indexação**: Índices de banco de dados otimizados

## Testes

Para executar os testes:

```bash
php artisan test
```

## Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a MIT License - veja o arquivo [LICENSE.md](LICENSE.md) para detalhes.

## Suporte

Para suporte técnico ou dúvidas:
- Email: suporte@closetfashion.com
- Issues: GitHub Issues
- Documentação: Wiki do projeto

## Roadmap

### Próximas Funcionalidades
- [ ] App mobile (React Native)
- [ ] Integração com redes sociais
- [ ] Sistema de recomendações IA
- [ ] Marketplace integrado
- [ ] Analytics avançados
- [ ] API pública
- [ ] Integração com e-commerce

---

**Desenvolvido  usando Laravel e tecnologias modernas**

