<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Location;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\ProductBaseUnit;
use App\Models\ProductAdditionalUnit;
use App\Models\ProductTierPricing;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    public function login(Request $request) {
        if ($request->method() == 'POST') {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user && !$user->email_verified_at) {
                return back()->withErrors([
                    'email' => 'Please verify your email address before logging in.',
                ])->onlyInput('email');
            }

            if (auth()->guard('customer')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                self::saveAccount(auth()->guard('customer')->user()->id);

                return redirect()->intended(route('home'));
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        return view('front.login');
    }

    public static function saveAccount($id)
    {
        $savedAccounts = session()->get('saved_accounts', []);

        if (!in_array($id, $savedAccounts)) {
            $savedAccounts[] = $id;

            session()->put('saved_accounts', $savedAccounts);
        }
    }

    public function register(Request $request) {
        if ($request->method() == 'POST') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $token = Str::random(64);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'verification_token' => $token,
                'verification_token_expires_at' => now()->addMinutes(30)
            ]);

            \App\Jobs\SendVerificationEmail::dispatch($user, $token);

            return redirect()->route('login')->with('success', 'Registration successful! Please check your email to verify your account.');
        }

        return view('front.register');
    }

    public function verifyEmail($token) {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid verification token.');
        }

        if ($user->verification_token_expires_at < now()) {
            return redirect()->route('login')->with('error', 'Verification token has expired.');
        }

        self::saveAccount(auth()->guard('customer')->user()->id);

        $user->update([
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_token_expires_at' => null,
            'status' => 1,
        ]);

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now login.');
    }

    public function logout(Request $request)
    {
        if (!auth()?->guard('customer')?->check()) {
            return redirect()->route('home');
        }

        $savedAccounts = $request->session()->get('saved_accounts');

        $id = auth()?->guard('customer')?->user()?->id;
        auth()->guard('customer')->logout();

        $request->session()->invalidate();

        if ($savedAccounts) {
            $request->session()->put('saved_accounts', $savedAccounts);

            $savedAccounts = session()->get('saved_accounts', []);

            if (($key = array_search($id, $savedAccounts)) !== false) {
                unset($savedAccounts[$key]);
                session()->put('saved_accounts', array_values($savedAccounts));
            }
        }

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function switchAccount(Request $request)
    {
        $savedAccounts = session()->get('saved_accounts', []);

        if (empty($savedAccounts)) {
            return redirect()->route('login');
        }

        $accounts = User::select('id', 'name', 'profile')->whereIn('id', $savedAccounts)->get();

        return view('front.switch-account', compact('accounts'));
    }

    public function removeAccount($id)
    {
        if (auth()?->guard('customer')?->check() && auth()?->guard('customer')?->user()?->id == $id) {
            $savedAccounts = request()->session()->get('saved_accounts');

            auth()->guard('customer')->logout();

            request()->session()->invalidate();

            if ($savedAccounts) {
                request()->session()->put('saved_accounts', $savedAccounts);
            }

            request()->session()->regenerateToken();

            return redirect()->route('switch-account')->with('success', 'Account removed successfully.');
        } else {
            $savedAccounts = session()->get('saved_accounts', []);

            if (($key = array_search($id, $savedAccounts)) !== false) {
                unset($savedAccounts[$key]);
                session()->put('saved_accounts', array_values($savedAccounts));
            }

            return redirect()->route('switch-account')->with('success', 'Account removed successfully.');
        }
    }

    public function addNewAccount()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('switch-account')->with('error', 'you need to logout first from current logged in account');
        }

        return redirect()->route('login');
    }

    public function index(Request $request) {
        $sections = \App\Models\HomePageSetting::oldest('ordering')->get();
        $topSellingProduct = Product::where('is_best_seller', 1)->limit(4)->get();
        $topSellingProductCount = Product::where('is_best_seller', 1)->count();
        
        return view('front.home', compact('sections', 'topSellingProduct', 'topSellingProductCount'));
    }

    public function search(Request $request) {
        $term = trim($request->input('q', ''));

        if ($request->ajax() || $request->wantsJson() || $request->boolean('ajax')) {
            if ($term === '') {
                return response()->json([
                    'products'   => [],
                    'categories' => [],
                ]);
            }

            $products = Product::query()
                ->select('id', 'name', 'slug', 'short_url', 'sku')
                ->active()
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%')
                        ->orWhere('sku', 'like', '%' . $term . '%');
                })
                ->orderBy('name')
                ->limit(6)
                ->get()
                ->map(function ($product) {
                    return [
                        'id'   => $product->id,
                        'name' => $product->name,
                        'sku'  => $product->sku,
                        'url'  => route('product.index', [
                            'product_slug' => $product->slug,
                            'short_url'    => $product->short_url,
                        ]),
                    ];
                });

            $categories = \App\Models\Category::query()
                ->select('id', 'name', 'slug', 'short_url')
                ->where('status', 1)
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')
                ->limit(6)
                ->get()
                ->map(function ($category) {
                    return [
                        'id'   => $category->id,
                        'name' => $category->name,
                        'url'  => route('category.index', [
                            'category_slug' => $category->slug,
                            'short_url'     => $category->short_url,
                        ]),
                    ];
                });

            return response()->json([
                'products'   => $products,
                'categories' => $categories,
            ]);
        }

        // Fallback: just redirect home for now.
        return redirect()->route('home');
    }

    public function category(Request $request, $category_slug = null, $short_url = null) {
        $category = \App\Models\Category::where('short_url', $short_url)
            ->where('status', 1)
            ->firstOrFail();

        $productIds = \App\Models\ProductCategory::where('category_id', $category->id)
            ->pluck('product_id')
            ->unique()
            ->values()
            ->all();

        $productsQuery = Product::with(['primaryImage', 'primaryCategory.category'])
            ->whereIn('id', $productIds)
            ->active();

        // Attribute filters
        $selectedAttributeIdsEncoded = $request->input('attributes', []);
        $selectedAttributeIds = array_filter(array_map(function ($encoded) {
            $decoded = base64_decode($encoded, true);
            return $decoded !== false ? (int) $decoded : null;
        }, is_array($selectedAttributeIdsEncoded) ? $selectedAttributeIdsEncoded : []));

        if (!empty($selectedAttributeIds)) {
            $productsQuery->whereHas('variants.attributes', function ($q) use ($selectedAttributeIds) {
                $q->whereIn('attribute_id', $selectedAttributeIds);
            });
        }

        // Price range filter (based on single_product_price)
        $priceRange = $request->input('price_range');
        if ($priceRange) {
            [$min, $max] = match ($priceRange) {
                'under_50'   => [0, 50],
                '50_100'     => [50, 100],
                '100_200'    => [100, 200],
                '200_500'    => [200, 500],
                'above_500'  => [500, null],
                default      => [null, null],
            };

            if ($min !== null) {
                $productsQuery->where('single_product_price', '>=', $min);
            }
            if ($max !== null) {
                $productsQuery->where('single_product_price', '<=', $max);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'az');
        switch ($sort) {
            case 'za':
                $productsQuery->orderBy('name', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $productsQuery->orderBy('created_at', 'asc');
                break;
            default:
                $productsQuery->orderBy('name', 'asc');
                break;
        }

        $products = $productsQuery->paginate(12)->appends($request->query());

        // Build available attribute filters from products in this category
        $variantIds = \App\Models\ProductVariant::whereIn('product_id', $productIds)
            ->active()
            ->pluck('id');

        $attributeMap = [];

        if ($variantIds->isNotEmpty()) {
            $attributeVariants = \App\Models\ProductAttributeVariant::with('attribute')
                ->whereIn('variant_id', $variantIds)
                ->get();

            foreach ($attributeVariants as $attributeVariant) {
                $attribute = $attributeVariant->attribute;
                if (!$attribute) {
                    continue;
                }

                $group = $attribute->title;
                $id    = $attribute->id;
                $label = $attribute->value;

                if (!isset($attributeMap[$group])) {
                    $attributeMap[$group] = [];
                }

                if (!isset($attributeMap[$group][$id])) {
                    $attributeMap[$group][$id] = [
                        'id'       => $id,
                        'label'    => $label,
                        'encoded'  => base64_encode((string) $id),
                        'selected' => in_array($id, $selectedAttributeIds, true),
                    ];
                }
            }
        }

        // Normalise attribute filters for the view
        $attributeFilters = [];
        foreach ($attributeMap as $groupTitle => $values) {
            $attributeFilters[] = [
                'title'  => $groupTitle,
                'values' => array_values($values),
            ];
        }

        return view('front.category', [
            'category'          => $category,
            'products'          => $products,
            'attributeFilters'  => $attributeFilters,
            'selectedAttributes'=> $selectedAttributeIdsEncoded,
            'priceRange'        => $priceRange,
            'sort'              => $sort,
        ]);
    }

    public function product(Request $request, $slug = null, $id = null, $variant = null) {

        $product = Product::where('short_url', $id)->active()->firstOrFail();

        $attributes = $existingAttributes = $categoryHierarchy = [];
        Helper::getProductHierarchy($product?->primaryCategory?->category?->id, $categoryHierarchy);

        $categoryHierarchy = collect($categoryHierarchy);

        if ($categoryHierarchy->count() > 3) {
            $firstTwo = collect([[
                'display' => true,
                'name' => '...'
            ],  $categoryHierarchy->take(-1)->first()])
            ->values()->all();
            
            $categoryHierarchy = $categoryHierarchy->take(2)->merge($firstTwo)->reverse()->values()->all();
        }

        // Initialize pricing and inventory data
        $units = collect();
        $tierPricings = collect();
        $totalStock = 0;
        $variantModel = null;

        if ($product->type == 'variable') {
            if (!(!empty($variant) && ProductVariant::where('product_id', $product->id)->active()->where('short_url', $variant)->exists())) {
                $variantModel = ProductVariant::where('product_id', $product->id)->active()->firstOrFail();

                if (!isset($variantModel->id)) {
                    $variantModel = null;
                    $variant = null;
                } else {
                    return redirect()->route('product.index', ['product_slug' => $product->slug, 'short_url' => $product->short_url, 'variant' => $variantModel->short_url]);
                }
            } else {
                $variantModel = ProductVariant::with('attributes')->where('product_id', $product->id)->where('short_url', $variant)->active()->firstOrFail();

                foreach ($variantModel?->attributes ?? [] as $attributeRelation) {
                    $existingAttributes[] = $attributeRelation->attribute_id;
                }

                $variant = $variantModel->short_url;
            }

            foreach ($product?->variants()?->active()?->with('attributes.attribute')?->get() ?? [] as $eachVariant) {
                foreach ($eachVariant?->attributes ?? [] as $attribute) {
                    if (isset($attribute->attribute->id)) {
                        if (isset($attribute->attribute->title) && isset($attribute->attribute->value)) {
                            if (array_key_exists($attribute->attribute->title, $attributes)) {
                                if (!in_array($attribute->attribute->id, array_column($attributes[$attribute->attribute->title], 'id'))) {
                                    $attributes[$attribute->attribute->title][] = [
                                        'id' => $attribute->attribute->id,
                                        'name' => $attribute->attribute->value,
                                        'is_active' => in_array($attribute->attribute->id, $existingAttributes)
                                    ];
                                }
                            } else {
                                $attributes[$attribute->attribute->title] = [
                                    [
                                        'id' => $attribute->attribute->id,
                                        'name' => $attribute->attribute->value,
                                        'is_active' => in_array($attribute->attribute->id, $existingAttributes)
                                    ]
                                ];
                            }
                        }
                    }
                }
            }

            // Load units and tier pricing for the current variant
            if ($variantModel) {
                // Get base unit
                $baseUnit = ProductBaseUnit::with('unit')
                    ->where('product_id', $product->id)
                    ->where('variant_id', $variantModel->id)
                    ->first();

                // Get additional units
                $additionalUnits = ProductAdditionalUnit::with('unit')
                    ->where('product_id', $product->id)
                    ->where('variant_id', $variantModel->id)
                    ->orderBy('is_default_selling_unit', 'desc')
                    ->get();

                // Build units collection
                $unitsArray = [];

                if ($baseUnit) {
                    $unitsArray[] = [
                        'id' => $baseUnit->id,
                        'unit_type' => 0,
                        'unit_id' => $baseUnit->unit_id,
                        'title' => $baseUnit->unit->title ?? 'Unit',
                        'is_default' => (bool) $baseUnit->is_default_selling_unit,
                    ];
                }

                foreach ($additionalUnits as $addUnit) {
                    $unitsArray[] = [
                        'id' => $addUnit->id,
                        'unit_type' => 1,
                        'unit_id' => $addUnit->unit_id,
                        'title' => $addUnit->unit->title ?? 'Unit',
                        'quantity' => (float) $addUnit->quantity,
                        'is_default' => (bool) $addUnit->is_default_selling_unit,
                    ];
                }

                $units = collect($unitsArray);

                // Get tier pricing for this variant
                $tierPricings = ProductTierPricing::where('product_id', $product->id)
                    ->where('product_variant_id', $variantModel->id)
                    ->orderBy('product_additional_unit_id')
                    ->orderBy('min_qty')
                    ->get()
                    ->map(function ($tier) {
                        $mrp = (float) $tier->price_per_unit;
                        $discountAmount = 0;
                        if ($tier->discount_type == 1) { // Percentage
                            $discountAmount = $mrp * ($tier->discount_amount / 100);
                        } else { // Fixed
                            $discountAmount = (float) $tier->discount_amount;
                        }
                        $yourPrice = max(0, $mrp - $discountAmount);

                        return [
                            'id' => $tier->id,
                            'unit_type' => (int) $tier->unit_type,
                            'product_additional_unit_id' => $tier->product_additional_unit_id,
                            'min_qty' => (float) $tier->min_qty,
                            'max_qty' => (float) $tier->max_qty,
                            'mrp' => $mrp,
                            'your_price' => $yourPrice,
                            'discount_amount' => $discountAmount,
                            'discount_type' => (int) $tier->discount_type,
                            'discount_value' => (float) $tier->discount_amount,
                        ];
                    });

                // Get total inventory stock (sum across all warehouses)

                $totalStock = Inventory::where('product_id', $product->id)
                    ->where('product_variant_id', $variantModel->id)
                    ->sum('quantity');
            }
        } else {
            // Simple Product Logic
            
            // Get base unit
            $baseUnit = ProductBaseUnit::with('unit')
                ->where('product_id', $product->id)
                ->whereNull('variant_id')
                ->first();

            // Get additional units
            $additionalUnits = ProductAdditionalUnit::with('unit')
                ->where('product_id', $product->id)
                ->whereNull('variant_id')
                ->orderBy('is_default_selling_unit', 'desc')
                ->get();

            // Build units collection
            $unitsArray = [];

            if ($baseUnit) {
                $unitsArray[] = [
                    'id' => $baseUnit->id,
                    'unit_type' => 0,
                    'unit_id' => $baseUnit->unit_id,
                    'title' => $baseUnit->unit->title ?? 'Unit',
                    'is_default' => (bool) $baseUnit->is_default_selling_unit,
                ];
            }

            foreach ($additionalUnits as $addUnit) {
                $unitsArray[] = [
                    'id' => $addUnit->id,
                    'unit_type' => 1,
                    'unit_id' => $addUnit->unit_id,
                    'title' => $addUnit->unit->title ?? 'Unit',
                    'quantity' => (float) $addUnit->quantity,
                    'is_default' => (bool) $addUnit->is_default_selling_unit,
                ];
            }

            $units = collect($unitsArray);

            // Get tier pricing for simple product
            $tierPricings = ProductTierPricing::where('product_id', $product->id)
                ->whereNull('product_variant_id')
                ->orderBy('product_additional_unit_id')
                ->orderBy('min_qty')
                ->get()
                ->map(function ($tier) {
                    $mrp = (float) $tier->price_per_unit;
                    $discountAmount = 0;
                    if ($tier->discount_type == 1) { // Percentage
                        $discountAmount = $mrp * ($tier->discount_amount / 100);
                    } else { // Fixed
                        $discountAmount = (float) $tier->discount_amount;
                    }
                    $yourPrice = max(0, $mrp - $discountAmount);

                    return [
                        'id' => $tier->id,
                        'unit_type' => (int) $tier->unit_type,
                        'product_additional_unit_id' => $tier->product_additional_unit_id,
                        'min_qty' => (float) $tier->min_qty,
                        'max_qty' => (float) $tier->max_qty,
                        'mrp' => $mrp,
                        'your_price' => $yourPrice,
                        'discount_amount' => $discountAmount,
                        'discount_type' => (int) $tier->discount_type,
                        'discount_value' => (float) $tier->discount_amount,
                    ];
                });

            // Get total inventory stock for simple product
            $totalStock = Inventory::where('product_id', $product->id)
                ->whereNull('product_variant_id')
                ->sum('quantity');
        }

        return view("front.product-details.{$product->type}", compact(
            'categoryHierarchy', 
            'product', 
            'attributes', 
            'existingAttributes', 
            'variant',
            'units',
            'tierPricings',
            'totalStock'
        ));
    }

    public function getVariantByAttributes(Request $request) {
        $productShortUrl = $request->input('product_short_url');
        $selectedAttributes = $request->input('attributes', []);

        $decodedAttributes = array_map(function($attr) {
            return (int) base64_decode($attr);
        }, $selectedAttributes);

        $product = Product::where('short_url', $productShortUrl)->active()->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $variants = ProductVariant::where('product_id', $product->id)
            ->active()
            ->with('attributes')
            ->get();

        foreach ($variants as $variant) {
            $variantAttributeIds = $variant->attributes->pluck('attribute_id')->toArray();
            
            $matchCount = 0;
            foreach ($decodedAttributes as $attrId) {
                if (in_array($attrId, $variantAttributeIds)) {
                    $matchCount++;
                }
            }

            if ($matchCount === count($decodedAttributes)) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('product.index', [
                        'product_slug' => $product->slug,
                        'short_url' => $product->short_url,
                        'variant' => $variant->short_url
                    ])
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'No matching variant found']);
    }

    /**
     * Get product pricing data for AJAX updates.
     * Returns tier pricing and inventory for a specific variant and unit.
     */
    public function getProductPricingData(Request $request)
    {
        $request->validate([
            'product_short_url'         => 'required|string',
            'product_variant_short_url' => 'nullable|string',
            'unit_type'                 => 'nullable|integer|in:0,1',
            'unit_id'                   => 'nullable|integer',
        ]);

        $product = Product::where('short_url', $request->product_short_url)->active()->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $variantId = null;
        if ($request->filled('product_variant_short_url')) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('short_url', $request->product_variant_short_url)
                ->active()
                ->first();

            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Variant not found'], 404);
            }

            $variantId = $variant->id;
        }

        $unitType = $request->filled('unit_type') ? (int) $request->unit_type : null;
        $unitId = $request->filled('unit_id') ? (int) $request->unit_id : null;

        // Get tier pricing for this variant and unit
        $tierQuery = ProductTierPricing::where('product_id', $product->id);

        if ($variantId) {
            $tierQuery->where('product_variant_id', $variantId);
        }

        if ($unitType !== null) {
            $tierQuery->where('unit_type', $unitType);
        }

        if ($unitId !== null) {
            $tierQuery->where('product_additional_unit_id', $unitId);
        }

        $tierPricings = $tierQuery
            ->orderBy('min_qty')
            ->get()
            ->map(function ($tier) {
                $mrp = (float) $tier->price_per_unit;
                $discountAmount = 0;
                if ($tier->discount_type == 1) { // Percentage
                    $discountAmount = $mrp * ($tier->discount_amount / 100);
                } else { // Fixed
                    $discountAmount = (float) $tier->discount_amount;
                }
                $yourPrice = max(0, $mrp - $discountAmount);

                return [
                    'id' => $tier->id,
                    'unit_type' => (int) $tier->unit_type,
                    'product_additional_unit_id' => $tier->product_additional_unit_id,
                    'min_qty' => (float) $tier->min_qty,
                    'max_qty' => (float) $tier->max_qty,
                    'mrp' => $mrp,
                    'your_price' => $yourPrice,
                    'discount_amount' => $discountAmount,
                    'discount_type' => (int) $tier->discount_type,
                    'discount_value' => (float) $tier->discount_amount,
                ];
            });

        // Get inventory stock
        $stockQuery = Inventory::where('product_id', $product->id);
        if ($variantId) {
            $stockQuery->where('product_variant_id', $variantId);
        }
        $totalStock = $stockQuery->sum('quantity');

        return response()->json([
            'success'       => true,
            'tier_pricings' => $tierPricings->values(),
            'total_stock'   => (int) $totalStock,
        ]);
    }

    public function wishlist(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customerId = auth()->guard('customer')->id();
        $wishlists = Wishlist::with([
            'product.primaryImage', 
            'product.images', 
            'product.primaryCategory.category',
            'productVariant.variantImage'
        ])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();

        return view('front.panel.wishlist', compact('wishlists'));
    }

    public function addToWishlist(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Please login to add items to wishlist'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $customerId = auth()->guard('customer')->id();

        $existing = Wishlist::where('customer_id', $customerId)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Item already in wishlist']);
        }

        Wishlist::updateOrCreate([
            'customer_id' => $customerId,
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Item added to wishlist']);
    }

    public function removeFromWishlist(Request $request, $id)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $wishlist = Wishlist::where('id', $id)
            ->where('customer_id', auth()->guard('customer')->id())
            ->firstOrFail();

        $wishlist->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Item removed from wishlist']);
        }

        return redirect()->route('wishlist')->with('success', 'Item removed from wishlist');
    }

    public function addresses(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customerId = auth()->guard('customer')->id();
        $addresses = Location::with(['country', 'state', 'city'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
        $countries = Country::pluck('name', 'id');

        return view('front.panel.addresses', compact('addresses', 'countries'));
    }

    public function storeAddress(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:locations,code',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zipcode' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'fax' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $location = Location::create([
            'customer_id' => auth()->guard('customer')->id(),
            'name' => $request->name,
            'code' => $request->code,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'zipcode' => $request->zipcode,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'fax' => $request->fax,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Address added successfully', 'address' => $location->load(['country', 'state', 'city'])]);
        }

        return redirect()->route('addresses')->with('success', 'Address added successfully');
    }

    public function updateAddress(Request $request, $id)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $location = Location::where('id', $id)
            ->where('customer_id', auth()->guard('customer')->id())
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:locations,code,' . $location->id,
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zipcode' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'fax' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $location->update([
            'name' => $request->name,
            'code' => $request->code,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'zipcode' => $request->zipcode,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'fax' => $request->fax,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Address updated successfully', 'address' => $location->load(['country', 'state', 'city'])]);
        }

        return redirect()->route('addresses')->with('success', 'Address updated successfully');
    }

    public function deleteAddress(Request $request, $id)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $location = Location::where('id', $id)
            ->where('customer_id', auth()->guard('customer')->id())
            ->firstOrFail();

        $location->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Address deleted successfully']);
        }

        return redirect()->route('addresses')->with('success', 'Address deleted successfully');
    }
    
    public function wishlistStatus(Request $request)
    {
        $loggedIn = auth()->guard('customer')->check();

        if (!$loggedIn) {
            return response()->json([
                'success' => true,
                'logged_in' => false,
                'wishlists' => []
            ]);
        }

        $customerId = auth()->guard('customer')->id();
        $wishlists = Wishlist::with(['product:id,short_url', 'productVariant:id,short_url'])
            ->where('customer_id', $customerId)
            ->get()
            ->map(function ($w) {
                return [
                    'product_id' => $w->product_id,
                    'product_variant_id' => $w->product_variant_id,
                    'product_short_url' => $w->product?->short_url,
                    'product_variant_short_url' => $w->productVariant?->short_url,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'logged_in' => true,
            'wishlists' => $wishlists,
        ]);
    }

    public function toggleWishlist(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'product_short_url' => 'required|string',
            'product_variant_short_url' => 'nullable|string',
        ]);

        $product = Product::where('short_url', $request->product_short_url)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $variantId = null;
        if ($request->filled('product_variant_short_url')) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('short_url', $request->product_variant_short_url)
                ->first();
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Variant not found'], 404);
            }
            $variantId = $variant->id;
        }

        $customerId = auth()->guard('customer')->id();

        $existing = Wishlist::where('customer_id', $customerId)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['success' => true, 'in_wishlist' => false]);
        }

        Wishlist::updateOrCreate([
            'customer_id' => $customerId,
            'product_id' => $product->id,
            'product_variant_id' => $variantId,
        ]);

        return response()->json(['success' => true, 'in_wishlist' => true]);
    }

    public function mergeWishlist(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $items = $request->input('items', []);
        if (!is_array($items)) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        $customerId = auth()->guard('customer')->id();
        $merged = 0;

        foreach ($items as $item) {
            $pShort = $item['product_short_url'] ?? null;
            $vShort = $item['product_variant_short_url'] ?? null;
            if (!$pShort) { continue; }

            $product = Product::where('short_url', $pShort)->first();
            if (!$product) { continue; }

            $variantId = null;
            if ($vShort) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('short_url', $vShort)
                    ->first();
                if (!$variant) { continue; }
                $variantId = $variant->id;
            }

            $exists = Wishlist::withTrashed()
                ->where('customer_id', $customerId)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($exists) {
                if ($exists->trashed()) {
                    $exists->restore();
                    $merged++;
                }
                continue;
            }

            Wishlist::updateOrCreate([
                'customer_id' => $customerId,
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
            ]);
            $merged++;
        }

        return response()->json(['success' => true, 'merged' => $merged]);
    }

    /**
     * Show cart page.
     *
     * For logged-in customers, the cart is loaded from the database.
     * For guests, items are managed fully in localStorage on the frontend.
     */
    public function cart(Request $request)
    {
        $loggedIn = auth()->guard('customer')->check();
        $cartItems = collect();

        if ($loggedIn) {
            $customerId = auth()->guard('customer')->id();
            $cart = Cart::with([
                'items.product.primaryImage',
                'items.productVariant',
            ])
                ->where('customer_id', $customerId)
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if ($cart) {
                $cartItems = $cart->items;
            }
        }

        return view('front.cart', [
            'loggedIn'  => $loggedIn,
            'cartItems' => $cartItems,
        ]);
    }

    /**
     * Return the current cart status.
     *
     * For guests this only indicates that they are not logged in.
     * For logged-in customers, this returns the current DB cart snapshot.
     */
    public function cartStatus(Request $request)
    {
        $loggedIn = auth()->guard('customer')->check();

        if (!$loggedIn) {
            return response()->json([
                'success'   => true,
                'logged_in' => false,
                'items'     => [],
            ]);
        }

        $customerId = auth()->guard('customer')->id();

        $cart = Cart::with([
            'items.product:id,short_url,name,sku',
            'items.productVariant:id,short_url',
        ])
            ->where('customer_id', $customerId)
            ->whereNull('converted_to_order_id')
            ->latest('id')
            ->first();

        $items = $cart
            ? $cart->items->map(function ($item) {
                return [
                    'id'                        => $item->id,
                    'product_id'                => $item->product_id,
                    'product_variant_id'        => $item->product_variant_id,
                    'product_short_url'         => $item->product?->short_url,
                    'product_variant_short_url' => $item->productVariant?->short_url,
                    'name'                      => $item->product?->name,
                    'sku'                       => $item->product?->sku ?? null,
                    'unit_type'                 => $item->unit_type,
                    'unit_id'                   => $item->unit_id,
                    'quantity'                  => $item->quantity,
                ];
            })->values()
            : collect();

        return response()->json([
            'success'   => true,
            'logged_in' => true,
            'items'     => $items,
        ]);
    }

    /**
     * Add an item to the logged-in customer's cart.
     *
     * Guests should store cart items in localStorage and later sync via mergeCart.
     */
    public function addToCart(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Please login to use server-side cart. Use local storage for guest cart.',
                'logged_in' => false,
            ], 401);
        }

        $request->validate([
            'product_short_url'         => 'required|string',
            'product_variant_short_url' => 'nullable|string',
            'quantity'                  => 'required|integer|min:1',
            'unit_type'                 => 'nullable|integer|in:0,1',
            'unit_id'                   => 'nullable|integer',
        ]);

        $product = Product::where('short_url', $request->product_short_url)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $variantId = null;
        if ($request->filled('product_variant_short_url')) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('short_url', $request->product_variant_short_url)
                ->first();

            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Variant not found'], 404);
            }

            $variantId = $variant->id;
        }

        $customerId = auth()->guard('customer')->id();

        $cart = $this->getOrCreateCustomerCart($customerId);

        $quantity = (int) $request->quantity;
        $unitType = $request->filled('unit_type') ? (int) $request->unit_type : null;
        $unitId = $request->filled('unit_id') ? (int) $request->unit_id : null;

        // Find existing cart item with same product, variant, AND unit
        $cartItem = CartItem::withTrashed()
            ->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->where('unit_type', $unitType)
            ->where('unit_id', $unitId)
            ->first();

        if ($cartItem) {
            if ($cartItem->trashed()) {
                $cartItem->restore();
            }

            $cartItem->quantity = $cartItem->quantity + $quantity;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id'            => $cart->id,
                'product_id'         => $product->id,
                'product_variant_id' => $variantId,
                'unit_type'          => $unitType,
                'unit_id'            => $unitId,
                'quantity'           => $quantity,
            ]);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Item added to cart',
            'item_id'   => $cartItem->id,
            'cart_id'   => $cart->id,
            'logged_in' => true,
        ]);
    }

    /**
     * Update the quantity of a cart item for the logged-in customer.
     */
    public function updateCartItemQuantity(Request $request, $id)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $customerId = auth()->guard('customer')->id();

        $cart = Cart::where('customer_id', $customerId)
            ->whereNull('converted_to_order_id')
            ->latest('id')
            ->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
        }

        $cartItem = CartItem::where('id', $id)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart'], 404);
        }

        $quantity = (int) $request->quantity;

        if ($quantity <= 0) {
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
            ]);
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Cart item updated',
            'quantity' => $cartItem->quantity,
        ]);
    }

    /**
     * Remove an item from the logged-in customer's cart.
     */
    public function removeFromCart(Request $request, $id)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $customerId = auth()->guard('customer')->id();

        $cart = Cart::where('customer_id', $customerId)
            ->whereNull('converted_to_order_id')
            ->latest('id')
            ->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
        }

        $cartItem = CartItem::where('id', $id)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }

    /**
     * Merge guest cart (from localStorage) into the logged-in customer's DB cart.
     *
     * Expected payload:
     * items: [
     *   {
     *     product_short_url: string,
     *     product_variant_short_url?: string|null,
     *     unit_type?: int|null,
     *     unit_id?: int|null,
     *     quantity: int
     *   },
     *   ...
     * ]
     */
    public function mergeCart(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $items = $request->input('items', []);

        if (!is_array($items)) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        $customerId = auth()->guard('customer')->id();

        $cart = $this->getOrCreateCustomerCart($customerId);

        $merged = 0;

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $pShort = $item['product_short_url'] ?? null;
                $vShort = $item['product_variant_short_url'] ?? null;
                $unitType = isset($item['unit_type']) ? (int) $item['unit_type'] : null;
                $unitId = isset($item['unit_id']) ? (int) $item['unit_id'] : null;
                $qty    = isset($item['quantity']) ? (int) $item['quantity'] : 0;

                if (!$pShort || $qty <= 0) {
                    continue;
                }

                $product = Product::where('short_url', $pShort)->first();

                if (!$product) {
                    continue;
                }

                $variantId = null;

                if ($vShort) {
                    $variant = ProductVariant::where('product_id', $product->id)
                        ->where('short_url', $vShort)
                        ->first();

                    if (!$variant) {
                        continue;
                    }

                    $variantId = $variant->id;
                }

                $cartItem = CartItem::withTrashed()
                    ->where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->where('unit_type', $unitType)
                    ->where('unit_id', $unitId)
                    ->first();

                if ($cartItem) {
                    if ($cartItem->trashed()) {
                        $cartItem->restore();
                    }

                    $cartItem->quantity = $cartItem->quantity + $qty;
                    $cartItem->save();
                } else {
                    CartItem::create([
                        'cart_id'            => $cart->id,
                        'product_id'         => $product->id,
                        'product_variant_id' => $variantId,
                        'unit_type'          => $unitType,
                        'unit_id'            => $unitId,
                        'quantity'           => $qty,
                    ]);
                }

                $merged++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to merge cart',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'merged'  => $merged,
        ]);
    }

    /**
     * Get or create an active cart for the given customer.
     */
    protected function getOrCreateCustomerCart(int $customerId): Cart
    {
        $cart = Cart::where('customer_id', $customerId)
            ->whereNull('converted_to_order_id')
            ->latest('id')
            ->first();

        if ($cart) {
            return $cart;
        }

        return Cart::create([
            'customer_id' => $customerId,
            'session_id'  => session()->getId(),
        ]);
    }
}
