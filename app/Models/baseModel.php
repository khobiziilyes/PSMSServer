<?php
namespace App\Models;

use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;

class baseModel extends Model {
    use Filterable;
    
    static $storeIdScope = true;
    static $isPublicProperty = false;

    protected $_hidden = ['store_id', 'created_by_id', 'updated_by_id', 'created_by_obj', 'updated_by_obj'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden ?? [], $this->_hidden ?? []);
    }

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('store_group', function (Builder $builder) {
            $storeIdColumn = (new static)->getTable() . '.store_id';
            $Store = auth()->user()->Store;
        
            if (static::$storeIdScope) {
                $builder->where($storeIdColumn, $Store->id);
            } else {
                $builder->whereHas('Store', function ($query) use ($Store) {
                    $query->whereIn('group_id', [$Store->Group->id, 0]);
                });
            }

            if (in_array(strtoupper(request()->method()), ['GET', 'POST'])) {
                $builder->orWhere($storeIdColumn, 0);
                if (static::$isPublicProperty) $builder->orWhere('is_public', true);
            }
        });

        static::creating(function($model) {
            $user = Auth::user();

            $model->created_by_id = $user->id;
            $model->updated_by_id = $user->id;

            static::caseAttributes($model);

            if (is_null($model->store_id)) $model->store_id = $user->Store->id;
        });
        
        static::updating(function ($model) { 
            $user_id = Auth::user()->id;
            $model->updated_by_id = $user_id;

            static::caseAttributes($model);
        });

        static::deleting(function($model) {
            $user_id = Auth::user()->id;
            $model->updated_by_id = $user_id;

            $model->save();
        });
    }

    public function Store() {
        return $this->belongsTo(Store::class);
    }
    
    public function creatorUpdator($field) {
        return $this->hasOne(\App\Models\User::class, 'id', $field)
            ->withTrashed()
            ->withDefault(['id' => -1, 'name' => 'DELETED']);
    }

    public function created_by_obj() {
        return $this->creatorUpdator('created_by_id');
    }

    public function updated_by_obj() {
        return $this->creatorUpdator('updated_by_id');
    }

    public function getCreatedByAttribute () {
        return $this->created_by_obj->name . ' #' . $this->created_by_obj->id;
    }

    public function getUpdatedByAttribute () {
        return $this->updated_by_obj->name . ' #' . $this->updated_by_obj->id;
    }

    public function getCreatedAtAttribute($value) {
        return $this->convertTime($value);
    }

    public function getUpdatedAtAttribute($value) {
        return $this->convertTime($value);
    }

    public function getDeletedAtAttribute($value) {
        return $this->convertTime($value);
    }

    public function convertTime($value) {
        return $value ? strtotime($value) : null;
    }

    public function getIsWritableAttributeInit() {
        $stores_ids = auth()->user()->StoresForWorker()->pluck('id')->toArray();
        return in_array($this->store_id, $stores_ids);
    }

    public function getIsWritableAttribute() {
        return $this->getIsWritableAttributeInit();
    }

    static function getClassName() {
        return (new \ReflectionClass(new static))->getShortName();
    }

    static function caseAttributes($model) {
        foreach ((static::$case ?? []) as $attributeName) 
            $model->{$attributeName} = ucwords($model->{$attributeName});
    }
}