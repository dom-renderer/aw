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

                self::syncCartOnLogin($request);

                $cookie = cookie('guest_cart', '', -1);

                return redirect()->intended(route('home'))->cookie($cookie);
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
                $variantModel = ProductVariant::with(['variantImage', 'variantSecondaryImage'])->where('product_id', $product->id)->active()->firstOrFail();

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
            'variantModel',
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

        $pricingQuery = ProductTierPricing::where('product_id', $product->id);

        if ($variantId) {
            $pricingQuery->where('product_variant_id', $variantId);
        } else {
            $pricingQuery->whereNull('product_variant_id');
        }

        if ($unitType !== null) {
            $pricingQuery->where('unit_type', $unitType);
        }

        if ($unitId !== null) {
            $pricingQuery->where('product_additional_unit_id', $unitId);
        }

        $pricingRecords = $pricingQuery->orderBy('min_qty')->get();

        $singlePricing = null;
        $tierPricings = collect();

        foreach ($pricingRecords as $pricing) {
            $mrp = (float) $pricing->price_per_unit;
            $discountAmount = 0;
            if ($pricing->discount_type == 1) {
                $discountAmount = $mrp * ($pricing->discount_amount / 100);
            } else {
                $discountAmount = (float) $pricing->discount_amount;
            }
            $yourPrice = max(0, $mrp - $discountAmount);

            $pricingData = [
                'id' => $pricing->id,
                'unit_type' => (int) $pricing->unit_type,
                'product_additional_unit_id' => $pricing->product_additional_unit_id,
                'min_qty' => (float) $pricing->min_qty,
                'max_qty' => (float) $pricing->max_qty,
                'mrp' => $mrp,
                'your_price' => $yourPrice,
                'discount_amount' => $discountAmount,
                'discount_type' => (int) $pricing->discount_type,
                'discount_value' => (float) $pricing->discount_amount,
                'pricing_type' => (int) $pricing->pricing_type,
            ];

            if ($pricing->pricing_type == 1) {
                $singlePricing = $pricingData;
            } else {
                $tierPricings->push($pricingData);
            }
        }

        $stockQuery = Inventory::where('product_id', $product->id);
        if ($variantId) {
            $stockQuery->where('product_variant_id', $variantId);
        } else {
            $stockQuery->whereNull('product_variant_id');
        }
        $totalStock = $stockQuery->sum('quantity');

        return response()->json([
            'success' => true,
            'pricing_type' => $singlePricing ? 1 : ($tierPricings->isNotEmpty() ? 0 : null),
            'single_pricing' => $singlePricing,
            'tier_pricings' => $tierPricings->values(),
            'total_stock' => (int) $totalStock,
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
        } else {
            $sessionId = $request->session()->getId();
            $cart = Cart::with([
                'items.product.primaryImage',
                'items.productVariant',
            ])
                ->where('session_id', $sessionId)
                ->whereNull('customer_id')
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

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_short_url' => 'required|string',
            'product_variant_short_url' => 'nullable|string',
            'unit_type' => 'required|integer|in:0,1',
            'unit_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.01',
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

        $loggedIn = auth()->guard('customer')->check();

        if ($loggedIn) {
            $customerId = auth()->guard('customer')->id();
            $cart = Cart::where('customer_id', $customerId)
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'customer_id' => $customerId,
                ]);
            }

            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->where('unit_type', $request->unit_type)
                ->where('unit_id', $request->unit_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity = (float) $existingItem->quantity + (float) $request->quantity;
                $existingItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'unit_type' => $request->unit_type,
                    'unit_id' => $request->unit_id,
                    'quantity' => (float) $request->quantity,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
            ]);
        } else {
            $cartCookie = $request->cookie('guest_cart');
            $cartData = $cartCookie ? json_decode($cartCookie, true) : [];

            $itemKey = $product->id . '_' . ($variantId ?? '0') . '_' . $request->unit_type . '_' . $request->unit_id;
            
            if (isset($cartData[$itemKey])) {
                $cartData[$itemKey]['quantity'] = (float) $cartData[$itemKey]['quantity'] + (float) $request->quantity;
            } else {
                $cartData[$itemKey] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'unit_type' => (int) $request->unit_type,
                    'unit_id' => (int) $request->unit_id,
                    'quantity' => (float) $request->quantity,
                    'product_short_url' => $product->short_url,
                    'product_variant_short_url' => $request->product_variant_short_url,
                ];
            }

            $sessionId = $request->session()->getId();
            $cart = Cart::where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'session_id' => $sessionId,
                ]);
            }

            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->where('unit_type', $request->unit_type)
                ->where('unit_id', $request->unit_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity = (float) $existingItem->quantity + (float) $request->quantity;
                $existingItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'unit_type' => $request->unit_type,
                    'unit_id' => $request->unit_id,
                    'quantity' => (float) $request->quantity,
                ]);
            }

            $cookie = cookie('guest_cart', json_encode($cartData), 60 * 24 * 30);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
            ])->cookie($cookie);
        }
    }

    public function updateCartItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $loggedIn = auth()->guard('customer')->check();

        if ($loggedIn) {
            $customerId = auth()->guard('customer')->id();
            $cart = Cart::where('customer_id', $customerId)
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
            }

            $item = CartItem::where('id', $request->item_id)
                ->where('cart_id', $cart->id)
                ->first();

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $item->quantity = (float) $request->quantity;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
            ]);
        } else {
            $cartCookie = $request->cookie('guest_cart');
            $cartData = $cartCookie ? json_decode($cartCookie, true) : [];

            $sessionId = $request->session()->getId();
            $cart = Cart::where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
            }

            $item = CartItem::where('id', $request->item_id)
                ->where('cart_id', $cart->id)
                ->first();

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $item->quantity = (float) $request->quantity;
            $item->save();

            $itemKey = $item->product_id . '_' . ($item->product_variant_id ?? '0') . '_' . $item->unit_type . '_' . $item->unit_id;
            if (isset($cartData[$itemKey])) {
                $cartData[$itemKey]['quantity'] = (float) $request->quantity;
            }

            $cookie = cookie('guest_cart', json_encode($cartData), 60 * 24 * 30);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
            ])->cookie($cookie);
        }
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $loggedIn = auth()->guard('customer')->check();

        if ($loggedIn) {
            $customerId = auth()->guard('customer')->id();
            $cart = Cart::where('customer_id', $customerId)
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
            }

            $item = CartItem::where('id', $request->item_id)
                ->where('cart_id', $cart->id)
                ->first();

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
            ]);
        } else {
            $cartCookie = $request->cookie('guest_cart');
            $cartData = $cartCookie ? json_decode($cartCookie, true) : [];

            $sessionId = $request->session()->getId();
            $cart = Cart::where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
            }

            $item = CartItem::where('id', $request->item_id)
                ->where('cart_id', $cart->id)
                ->first();

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $itemKey = $item->product_id . '_' . ($item->product_variant_id ?? '0') . '_' . $item->unit_type . '_' . $item->unit_id;
            unset($cartData[$itemKey]);

            $item->delete();

            $cookie = cookie('guest_cart', json_encode($cartData), 60 * 24 * 30);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
            ])->cookie($cookie);
        }
    }

    public function getCartCount(Request $request)
    {
        $loggedIn = auth()->guard('customer')->check();
        $count = 0;

        if ($loggedIn) {
            $customerId = auth()->guard('customer')->id();
            $cart = Cart::where('customer_id', $customerId)
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if ($cart) {
                $count = $cart->items()->sum('quantity');
            }
        } else {
            $sessionId = $request->session()->getId();
            $cart = Cart::where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if ($cart) {
                $count = $cart->items()->sum('quantity');
            }
        }

        return response()->json([
            'success' => true,
            'count' => (int) $count,
        ]);
    }

    public function getCartItemQuantity(Request $request)
    {
        $request->validate([
            'product_short_url' => 'required|string',
            'product_variant_short_url' => 'nullable|string',
            'unit_type' => 'required|integer|in:0,1',
            'unit_id' => 'required|integer',
        ]);

        $product = Product::where('short_url', $request->product_short_url)->active()->first();
        if (!$product) {
            return response()->json(['success' => false, 'quantity' => 0]);
        }

        $variantId = null;
        if ($request->filled('product_variant_short_url')) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('short_url', $request->product_variant_short_url)
                ->active()
                ->first();
            if ($variant) {
                $variantId = $variant->id;
            }
        }

        $loggedIn = auth()->guard('customer')->check();
        $quantity = 0;
        $itemId = null;

        if ($loggedIn) {
            $customerId = auth()->guard('customer')->id();
            $cart = Cart::where('customer_id', $customerId)
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if ($cart) {
                $item = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->where('unit_type', $request->unit_type)
                    ->where('unit_id', $request->unit_id)
                    ->first();

                if ($item) {
                    $quantity = (float) $item->quantity;
                    $itemId = $item->id;
                }
            }
        } else {
            $sessionId = $request->session()->getId();
            $cart = Cart::where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->whereNull('converted_to_order_id')
                ->latest('id')
                ->first();

            if ($cart) {
                $item = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->where('unit_type', $request->unit_type)
                    ->where('unit_id', $request->unit_id)
                    ->first();

                if ($item) {
                    $quantity = (float) $item->quantity;
                    $itemId = $item->id;
                }
            }
        }

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'item_id' => $itemId,
        ]);
    }

    public static function syncCartOnLogin(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return;
        }

        $customerId = auth()->guard('customer')->id();
        $sessionId = $request->session()->getId();

        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->whereNull('converted_to_order_id')
            ->latest('id')
            ->first();

        if (!$guestCart || $guestCart->items()->count() == 0) {
            $cartCookie = $request->cookie('guest_cart');
            if ($cartCookie) {
                $cartData = json_decode($cartCookie, true);
                if ($cartData && count($cartData) > 0) {
                    $userCart = Cart::where('customer_id', $customerId)
                        ->whereNull('converted_to_order_id')
                        ->latest('id')
                        ->first();

                    if (!$userCart) {
                        $userCart = Cart::create([
                            'customer_id' => $customerId,
                        ]);
                    }

                    foreach ($cartData as $itemData) {
                        $existingItem = CartItem::where('cart_id', $userCart->id)
                            ->where('product_id', $itemData['product_id'])
                            ->where('product_variant_id', $itemData['product_variant_id'])
                            ->where('unit_type', $itemData['unit_type'])
                            ->where('unit_id', $itemData['unit_id'])
                            ->first();

                        if ($existingItem) {
                            $existingItem->quantity = (float) $existingItem->quantity + (float) $itemData['quantity'];
                            $existingItem->save();
                        } else {
                            CartItem::create([
                                'cart_id' => $userCart->id,
                                'product_id' => $itemData['product_id'],
                                'product_variant_id' => $itemData['product_variant_id'],
                                'unit_type' => $itemData['unit_type'],
                                'unit_id' => $itemData['unit_id'],
                                'quantity' => (float) $itemData['quantity'],
                            ]);
                        }
                    }
                }
            }
            return;
        }

        $userCart = Cart::where('customer_id', $customerId)
            ->whereNull('converted_to_order_id')
            ->latest('id')
            ->first();

        if (!$userCart) {
            $guestCart->customer_id = $customerId;
            $guestCart->session_id = null;
            $guestCart->save();
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $existingItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->where('unit_type', $guestItem->unit_type)
                ->where('unit_id', $guestItem->unit_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity = (float) $existingItem->quantity + (float) $guestItem->quantity;
                $existingItem->save();
                $guestItem->delete();
            } else {
                $guestItem->cart_id = $userCart->id;
                $guestItem->save();
            }
        }

        $guestCart->delete();
    }
}
