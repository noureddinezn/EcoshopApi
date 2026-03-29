# 📋 PLAN DE DÉVELOPPEMENT - API REST ECOSHOP

## ✅ ÉTAPES COMPLÉTÉES (36 commits)
- [x] Configuration initiale de Laravel
- [x] Création de tous les modèles (User, Product, Category, Cart, Order, OrderItem)
- [x] Migrations de base de données
- [x] Contrôleurs API
- [x] Routes API
- [x] Structure de projet

---

## 🎯 PLAN D'ACTION - PROCHAINES ÉTAPES

### **PHASE 1: FONDATIONS (WEEK 1)**
**Priorité: CRITIQUE**

#### 1.1 - Form Requests & Validation
- [ ] `LoginRequest.php` - Validation login
- [ ] `RegisterRequest.php` - Validation inscription
- [ ] `StoreProductRequest.php` - Création produit
- [ ] `UpdateProductRequest.php` - Modification produit
- [ ] `StoreCategoryRequest.php` - Création catégorie
- [ ] `CreateOrderRequest.php` - Validation commande
- [ ] `AddToCartRequest.php` - Validation panier

**Fichiers à créer:**
```
app/Http/Requests/
├── Auth/
│   ├── LoginRequest.php
│   ├── RegisterRequest.php
│   └── ResetPasswordRequest.php
├── Product/
│   ├── StoreProductRequest.php
│   ├── UpdateProductRequest.php
│   └── SearchProductRequest.php
├── Category/
│   ├── StoreCategoryRequest.php
│   └── UpdateCategoryRequest.php
├── Order/
│   └── CreateOrderRequest.php
└── Cart/
    └── AddToCartRequest.php
```

#### 1.2 - API Resources (Response Formatting)
- [ ] `UserResource.php` - Formatage réponse utilisateur
- [ ] `ProductResource.php` - Formatage réponse produit
- [ ] `CategoryResource.php` - Formatage réponse catégorie
- [ ] `CartResource.php` - Formatage réponse panier
- [ ] `OrderResource.php` - Formatage réponse commande
- [ ] `OrderItemResource.php` - Formatage article commande

**Fichiers à créer:**
```
app/Http/Resources/
├── UserResource.php
├── ProductResource.php
├── CategoryResource.php
├── CartResource.php
├── CartItemResource.php
├── OrderResource.php
└── OrderItemResource.php
```

#### 1.3 - Traits & Helpers
- [ ] `ApiResponse.php` - Trait pour réponses JSON cohérentes
- [ ] `BaseController.php` - Améliorer le contrôleur de base

**Fichiers à créer:**
```
app/Traits/
└── ApiResponse.php

app/Http/Controllers/
└── Api/BaseController.php
```

---

### **PHASE 2: LOGIQUE MÉTIER (WEEK 2)**
**Priorité: HAUTE**

#### 2.1 - Services (Business Logic)
- [ ] `AuthService.php` - Authentification
- [ ] `ProductService.php` - Gestion produits
- [ ] `CategoryService.php` - Gestion catégories
- [ ] `CartService.php` - Gestion panier
- [ ] `OrderService.php` - Gestion commandes
- [ ] `PaymentService.php` - Intégration paiements (optionnel)

**Fichiers à créer:**
```
app/Services/
├── AuthService.php
├── ProductService.php
├── CategoryService.php
├── CartService.php
├── OrderService.php
└── PaymentService.php
```

#### 2.2 - Améliorer les Modèles
- [ ] Ajouter les méthodes de relation manquantes
- [ ] Ajouter les scopes utiles (approved, active, etc.)
- [ ] Ajouter les mutateurs/accesseurs
- [ ] Ajouter les casts appropriés

---

### **PHASE 3: ÉVÉNEMENTS & ASYNCHRONE (WEEK 2-3)**
**Priorité: HAUTE**

#### 3.1 - Events
- [ ] `OrderPlaced.php` - Événement commande créée
- [ ] `OrderShipped.php` - Événement commande expédiée
- [ ] `StockUpdated.php` - Événement stock mis à jour

**Fichiers à créer:**
```
app/Events/
├── OrderPlaced.php
├── OrderShipped.php
└── StockUpdated.php
```

#### 3.2 - Listeners
- [ ] `SendOrderConfirmationEmail.php` - Email confirmation
- [ ] `UpdateProductStock.php` - Mise à jour stock
- [ ] `NotifyAdminNewOrder.php` - Notification admin

**Fichiers à créer:**
```
app/Listeners/
├── SendOrderConfirmationEmail.php
├── UpdateProductStock.php
└── NotifyAdminNewOrder.php
```

#### 3.3 - Jobs (pour Queue)
- [ ] `SendEmailJob.php` - Job email
- [ ] `ProcessOrderJob.php` - Job traitement commande
- [ ] `UpdateInventoryJob.php` - Job inventaire

**Fichiers à créer:**
```
app/Jobs/
├── SendEmailJob.php
├── ProcessOrderJob.php
└── UpdateInventoryJob.php
```

#### 3.4 - Mail Classes
- [ ] `OrderConfirmationMail.php` - Email commande
- [ ] `OrderShippingMail.php` - Email expédition
- [ ] `AdminNotificationMail.php` - Email admin

**Fichiers à créer:**
```
app/Mail/
├── OrderConfirmationMail.php
├── OrderShippingMail.php
└── AdminNotificationMail.php
```

---

### **PHASE 4: SÉCURITÉ & PERMISSIONS (WEEK 3)**
**Priorité: HAUTE**

#### 4.1 - Policies (Autorisation)
- [ ] `ProductPolicy.php` - Admin peut créer/modifier/supprimer produits
- [ ] `OrderPolicy.php` - User voit ses commandes, admin voit tout
- [ ] `CartPolicy.php` - User gère son panier

**Fichiers à créer:**
```
app/Policies/
├── ProductPolicy.php
├── OrderPolicy.php
└── CartPolicy.php
```

#### 4.2 - Middleware
- [ ] `AdminOnly.php` - Vérifier rôle admin
- [ ] `EnsureTokenValid.php` - Valider token Sanctum

**Fichiers à créer:**
```
app/Http/Middleware/
├── AdminOnly.php
└── EnsureTokenValid.php
```

#### 4.3 - Roles & Permissions (optionnel avec Spatie)
- [ ] `seeders/RoleAndPermissionSeeder.php`

---

### **PHASE 5: TESTS AUTOMATISÉS (WEEK 4)**
**Priorité: HAUTE**

#### 5.1 - Tests Feature
- [ ] `AuthTest.php` - Tests authentification
  - [ ] test_user_can_register
  - [ ] test_user_can_login
  - [ ] test_user_can_logout
  - [ ] test_unauthenticated_user_cannot_access_protected_routes

- [ ] `ProductTest.php` - Tests produits
  - [ ] test_user_can_view_products
  - [ ] test_user_can_filter_products_by_category
  - [ ] test_admin_can_create_product
  - [ ] test_admin_can_update_product
  - [ ] test_admin_can_delete_product

- [ ] `CartTest.php` - Tests panier
  - [ ] test_user_can_add_item_to_cart
  - [ ] test_user_can_remove_item_from_cart
  - [ ] test_user_can_view_cart
  - [ ] test_cart_total_calculated_correctly

- [ ] `OrderTest.php` - Tests commandes
  - [ ] test_user_can_create_order
  - [ ] test_order_created_event_triggered
  - [ ] test_email_sent_on_order_creation
  - [ ] test_stock_updated_on_order
  - [ ] test_user_can_view_own_orders

**Fichiers à créer:**
```
tests/Feature/
├── AuthTest.php
├── ProductTest.php
├── CartTest.php
├── OrderTest.php
└── CategoryTest.php
```

#### 5.2 - Tests Unit
- [ ] `UserTest.php`
- [ ] `ProductTest.php`
- [ ] `OrderTest.php`

**Fichiers à créer:**
```
tests/Unit/
├── UserTest.php
├── ProductTest.php
└── OrderTest.php
```

---

### **PHASE 6: DOCUMENTATION (WEEK 4)**
**Priorité: MOYENNE**

#### 6.1 - API Documentation
- [ ] `README.md` - Documentation projet
- [ ] `API_DOCUMENTATION.md` - Endpoints API
- [ ] Postman Collection `EcoAPI.postman_collection.json`
- [ ] OpenAPI/Swagger `openapi.yaml`

#### 6.2 - Diagrammes UML (Déjà fait)
- [x] Class Diagram - `API_UML_CLASS_DIAGRAM.puml`
- [x] Use Case - `API_USE_CASE_DIAGRAM.puml`
- [ ] Sequence Diagram - `API_SEQUENCE_DIAGRAM.puml`
- [ ] Activity Diagram - `API_ACTIVITY_DIAGRAM.puml`

---

### **PHASE 7: OPTIMISATIONS & FEATURES AVANCÉES (WEEK 5+)**
**Priorité: MOYENNE/BASSE**

#### 7.1 - Search & Filtering
- [ ] Elasticsearch ou Algolia (optionnel)
- [ ] Pagination
- [ ] Tri (sort)
- [ ] Recherche plein texte

#### 7.2 - Caching
- [ ] Cache des produits
- [ ] Cache des catégories
- [ ] Cache du panier

#### 7.3 - Rate Limiting
- [ ] Throttle par utilisateur
- [ ] Throttle par IP

#### 7.4 - Soft Deletes
- [ ] Ajouter soft deletes aux modèles
- [ ] Migrations

#### 7.5 - Pagination & Relationships
- [ ] Eager loading optimisé
- [ ] Inclusion/exclusion de relations

---

## 📊 MATRICE DE DÉPENDANCES

```
Phase 1 (Validation, Resources) 
    ↓
Phase 2 (Services)
    ↓
Phase 3 (Events, Jobs, Queues)
    ↓
Phase 4 (Policies, Middleware)
    ↓
Phase 5 (Tests automatisés)
    ↓
Phase 6 (Documentation)
    ↓
Phase 7 (Optimisations)
```

---

## 🚀 COMMANDES UTILES

### Créer un Service
```bash
php artisan make:service Services/AuthService
```

### Créer un Event
```bash
php artisan make:event Events/OrderPlaced
```

### Créer un Listener
```bash
php artisan make:listener Listeners/SendOrderConfirmationEmail --event=OrderPlaced
```

### Créer un Job
```bash
php artisan make:job Jobs/SendEmailJob
```

### Créer une Policy
```bash
php artisan make:policy ProductPolicy --model=Product
```

### Créer un Test
```bash
php artisan make:test Feature/ProductTest
php artisan make:test Unit/ProductTest --unit
```

### Générer les ressources
```bash
php artisan make:resource ProductResource
```

---

## 📈 ESTIMATION TEMPS

| Phase | Durée | Points de Story |
|-------|-------|-----------------|
| Phase 1: Validation & Resources | 2-3 jours | 8 |
| Phase 2: Services | 3-4 jours | 13 |
| Phase 3: Events & Async | 4-5 jours | 21 |
| Phase 4: Sécurité | 2-3 jours | 13 |
| Phase 5: Tests | 5-7 jours | 34 |
| Phase 6: Documentation | 2-3 jours | 8 |
| Phase 7: Optimisations | 3-5 jours | 13 |
| **TOTAL** | **3-4 semaines** | **110** |

---

## ✨ RECOMMANDATIONS

1. **Commencer par Phase 1** - Mettre en place la fondation
2. **Utiliser Pest pour les tests** - Plus moderne que PHPUnit
3. **Implémenter les Events/Listeners** - Respecter le cahier des charges
4. **Documentation OpenAPI** - Auto-générer avec outils
5. **Git workflow** - 1 commit par feature
6. **Code Review** - Même en solo, relire le code

---

## 🔗 RESSOURCES UTILES

- [Laravel Form Requests](https://laravel.com/docs/requests#form-request-validation)
- [Laravel Eloquent Resources](https://laravel.com/docs/eloquent-resources)
- [Laravel Events](https://laravel.com/docs/events)
- [Laravel Queues](https://laravel.com/docs/queues)
- [Pest Testing](https://pestphp.com/)
- [Postman for documenting](https://www.postman.com/)

