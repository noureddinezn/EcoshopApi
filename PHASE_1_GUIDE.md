# 🚀 PHASE 1: FONDATIONS - GUIDE DE DÉMARRAGE

## Objectif
Mettre en place la base solide du projet avec validation, ressources API et traits utilitaires.

## Fichiers à Créer

### 1️⃣ FORM REQUESTS (Validation)

#### `app/Http/Requests/Auth/LoginRequest.php`
```php
<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez fournir une adresse email valide.',
            'email.exists' => 'Cet email n\'est pas enregistré.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ];
    }
}
```

#### `app/Http/Requests/Auth/RegisterRequest.php`
```php
<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ];
    }
}
```

#### `app/Http/Requests/Product/StoreProductRequest.php`
```php
<?php
namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
        ];
    }
}
```

#### `app/Http/Requests/Product/UpdateProductRequest.php`
```php
<?php
namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255', 'unique:products,name,' . $this->product->id],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
        ];
    }
}
```

#### `app/Http/Requests/Category/StoreCategoryRequest.php`
```php
<?php
namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ];
    }
}
```

#### `app/Http/Requests/Cart/AddToCartRequest.php`
```php
<?php
namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
```

#### `app/Http/Requests/Order/CreateOrderRequest.php`
```php
<?php
namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string'],
            'billing_address' => ['nullable', 'string'],
            'payment_method' => ['required', 'string', 'in:credit_card,paypal,stripe'],
        ];
    }
}
```

---

### 2️⃣ API RESOURCES (Formatage des réponses)

#### `app/Http/Resources/UserResource.php`
```php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
```

#### `app/Http/Resources/ProductResource.php`
```php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'image_url' => $this->image_url,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
        ];
    }
}
```

#### `app/Http/Resources/CategoryResource.php`
```php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
```

#### `app/Http/Resources/CartResource.php`
```php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $total = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->items_count ?? $this->items->count(),
            'total' => (float) $total,
            'created_at' => $this->created_at,
        ];
    }
}
```

#### `app/Http/Resources/OrderResource.php`
```php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_price' => (float) $this->total_price,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

---

### 3️⃣ TRAITS & HELPERS

#### `app/Traits/ApiResponse.php`
```php
<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send a successful response
     */
    public function success($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send a created response
     */
    public function created($data = null, $message = 'Resource created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    /**
     * Send an error response
     */
    public function error($message = 'Error', $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Send a not found response
     */
    public function notFound($message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Send an unauthorized response
     */
    public function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Send a forbidden response
     */
    public function forbidden($message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Send a validation error response
     */
    public function validationError($errors): JsonResponse
    {
        return $this->error('Validation failed', 422, $errors);
    }
}
```

---

## 📋 CHECKLIST PHASE 1

### Form Requests
- [ ] `LoginRequest.php`
- [ ] `RegisterRequest.php`
- [ ] `StoreProductRequest.php`
- [ ] `UpdateProductRequest.php`
- [ ] `StoreCategoryRequest.php`
- [ ] `UpdateCategoryRequest.php`
- [ ] `AddToCartRequest.php`
- [ ] `CreateOrderRequest.php`

### API Resources
- [ ] `UserResource.php`
- [ ] `ProductResource.php`
- [ ] `CategoryResource.php`
- [ ] `CartResource.php`
- [ ] `CartItemResource.php`
- [ ] `OrderResource.php`
- [ ] `OrderItemResource.php`

### Traits
- [ ] `ApiResponse.php`

### Contrôleurs (Mise à jour)
- [ ] Mettre à jour tous les contrôleurs pour utiliser les Form Requests
- [ ] Mettre à jour tous les contrôleurs pour retourner les Resources
- [ ] Mettre à jour tous les contrôleurs pour utiliser le trait ApiResponse

---

## 🔄 WORKFLOW POUR PHASE 1

```bash
# 1. Créer les répertoires
mkdir -p app/Http/Requests/Auth
mkdir -p app/Http/Requests/Product
mkdir -p app/Http/Requests/Category
mkdir -p app/Http/Requests/Cart
mkdir -p app/Http/Requests/Order
mkdir -p app/Http/Resources
mkdir -p app/Traits

# 2. Créer les fichiers (copier-coller du guide ci-dessus)

# 3. Mettre à jour les contrôleurs

# 4. Tester avec Postman

# 5. Commit
git add .
git commit -m "feat: Implement form requests, API resources, and ApiResponse trait"
```

---

## 📝 EXEMPLE D'UTILISATION DANS UN CONTRÔLEUR

```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        // Form request déjà validé automatiquement
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful', 200);
    }
}
```

---

## ✨ BÉNÉFICES

✅ Validation centralisée et réutilisable
✅ Réponses API cohérentes et formatées
✅ Code plus lisible et maintenable
✅ Tests plus faciles à écrire
✅ Documentation auto-générée plus claire
✅ Travail facilité avec le frontend

