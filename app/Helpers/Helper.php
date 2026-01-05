<?php

namespace App\Helpers;

use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Brand;

class Helper {
    
    public static function title ($title = '') {
        if (!empty($title)) {
            return $title;
        } else if ($name = DB::table('settings')->first()?->name) {
            return $name;
        } else {
            return env('APP_NAME', '');
        }
    }

    public static function logo () {
        if ($name = DB::table('settings')->first()?->logo) {
            return url("settings-media/{$name}");
        } else {
            return url('assets/images/logo.png');
        }
    }

    public static function favicon () {
        if ($name = DB::table('settings')->first()?->favicon) {
            return url("settings-media/{$name}");
        } else {
            return url('assets/images/favicon.ico');
        }
    }

    public static function bgcolor ($bg = null) {
        if (!empty($bg)) {
            return $bg;
        } else if ($color = DB::table('settings')->first()?->theme_color) {
            return $color;
        } else {
            return '#3a082f';
        }
    }

    public function getStatesByCountry(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = State::query()
        ->when(is_numeric(request('country_id')), fn ($builder) => $builder->where('country_id', request('country_id')));
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getCitiesByState(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = City::query()
        ->where('state_id', $request->state_id);
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public static function getIso2ByDialCode($dialCode = null) {
        if (empty(trim($dialCode))) {
            $dialCode = '91';
        }

        $dialCode = trim(str_replace('+', '', $dialCode));
        return strtolower(Country::select('iso2')->where('phonecode', "+{$dialCode}")->orWhere('phonecode', $dialCode)->first()->iso2 ?? 'in');
    }

    public static function isValidEncryption($value) {
        try {
            \Illuminate\Support\Facades\Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getBrands(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = Brand::query()->where('status', 1);

        if (!empty($queryString)) {
            $query->where(function($q) use ($queryString) {
                $q->where('name', 'LIKE', "%{$queryString}%")
                  ->orWhere('slug', 'LIKE', "%{$queryString}%");
            });
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public static function getProductHierarchy($categoryId, &$response = []) {
        $category = Category::find($categoryId);

        if (!($category instanceof Category)) {
            return $response;
        }

        $response[] = [
            'id' => $category->id,
            'slug' => $category->slug,
            'short_url' => $category->short_url,
            'name' => $category->name
        ];

        if (isset($category->parent->id)) {
            self::getProductHierarchy($category->parent->id, $response);
        }

        return $response;
    }

    public static function encrypt(int|string $id): string
    {
        $key = hash('sha256', config('app.key'), true);
        $iv  = random_bytes(16);

        $cipherText = openssl_encrypt(
            (string) $id,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $hmac = hash_hmac('sha256', $iv . $cipherText, $key, true);

        return rtrim(strtr(base64_encode($iv . $cipherText . $hmac), '+/', '-_'), '=');
    }

    public static function decrypt(string $encrypted): int|string|null
    {
        $key = hash('sha256', config('app.key'), true);

        $data = base64_decode(strtr($encrypted, '-_', '+/'));
        if ($data === false || strlen($data) < 48) {
            return null;
        }

        $iv         = substr($data, 0, 16);
        $cipherText = substr($data, 16, -32);
        $hmac       = substr($data, -32);

        $calcHmac = hash_hmac('sha256', $iv . $cipherText, $key, true);
        if (!hash_equals($hmac, $calcHmac)) {
            return null;
        }

        return openssl_decrypt(
            $cipherText,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}