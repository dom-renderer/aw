<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'status' => 'boolean',
        'in_stock' => 'boolean'
    ];

    public function primaryCategory() {
        return $this->hasOne(ProductCategory::class, 'product_id')->where('is_primary', 1);
    }

    public function primaryBrand() {
        return $this->hasOne(BrandProduct::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->latest('is_primary');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_primary', 1);
    }

    public function secondaryImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->where('is_primary', 0);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_product', 'product_id', 'brand_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeInActive($query){
        return $query->where('status', 0);
    }
}
