<?php

namespace LaravelToolkit\ACL;

use Closure;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelToolkit\Facades\ACL;
use StringBackedEnum;

/**
 * @property int $id
 * @property null|Collection<string, \UnitEnum> $roles
 * @property \Illuminate\Support\Carbon $updated_at
 * @method Policy __get(string $name)
 */
abstract class UserPermission extends Model
{
    use ManagementTrait;

    public const CREATED_AT = null;

    /**
     * @var \Illuminate\Support\Collection<string, \LaravelToolkit\ACL\Policy>
     */
    protected Collection $policies;

    protected StringBackedEnum $enum;

    public function __construct(array $attributes = [])
    {
        $this->startup();
        parent::__construct($attributes);
    }

    public static function boot(): void
    {
        self::saving(function (UserPermission $model) {
            foreach ($model->policies as $policy) {
                foreach ($policy->rules as $rule) {
                    if ($rule->isDirty() && !$model->isDirty($policy->column)) {
                        $model->setAttribute($policy->column, $model->{$policy->column});
                    }
                }
            }
        });
        parent::boot();
    }

    /**
     * This method declare policies for your ACL system
     */
    abstract protected function declarePoliciesAndRoles(): void;

    /**
     * @return \Illuminate\Support\Collection<string, \LaravelToolkit\ACL\Policy>
     */
    public function getPolicies(?Closure $filter = null): Collection
    {
        $policies = collect();
        foreach ($this->policies as $policy) {
            $policies->put($policy->column, $this->{$policy->column});
        }
        if ($filter) {
            $policies = $policies->filter($filter);
        }
        return !empty($column) ? $policies->get($column) : $policies;
    }

    final public function casts(): array
    {
        $enum = ACL::rolesEnum();
        $cast = [
            'id' => 'int',
            'roles' => $enum ? AsEnumCollection::of($enum) : 'collection',
        ];
        foreach ($this->policies as $policy) {
            $cast[$policy->column] = PolicyCast::class;
        }
        $cast['updated_at'] = 'datetime';
        return $cast;
    }

    public function permissions(Format $format = Format::COMPLETE, ?Closure $filter = null): array
    {
        return match ($format) {
            Format::COMPLETE => CompleteResource::make($this->getPolicies($filter))->resolve(),
            Format::ONLY_VALUES => OnlyValueResource::make($this->getPolicies($filter))->resolve(),
        };
    }
}
