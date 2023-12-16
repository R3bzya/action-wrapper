# Action Wrapper

ActionWrapper is a simple yet flexible action decorator.

## Installation

Via Composer:

```shell
composer require r3bzya/action-wrapper
```

## Available classes

When you need to wrap some actions, and you don't have to create a new action class use the FluentAction class.

> ***Note:*** The FluentAction class has the `Traits\Simples\HasActionWrapper` trait.
>
>```
>$action = new \R3bzya\ActionWrapper\Actions\FluentAction;
>
>$action->execute(fn(): bool => true);
>```

## Available methods

### Base methods

> ***Note:*** These methods available in `Traits\Simples\HasActionWrapper`.
>
>```
>class Example
>{
>    use \R3bzya\ActionWrapper\Traits\Simples\HasActionWrapper;
>
>    ...
>}
>```

#### *after(callable $decorator): ActionWrapper|static*

The `after` method adds a callable function to an ActionWrapper which will be called after executing any of the `decoratedMethods`
methods.

```
$this->after(fn(mixed $result): mixed => $result);
```

#### *before(callable $decorator): ActionWrapper|static*

The `before` method adds a callable function to an ActionWrapper which will be called before executing any of
the `decoratedMethods` methods.
When the given callable function returns false, the action will be interrupted.
To change the input arguments the given callable should return an array otherwise the `before` method will work like a tap.

```
$this->before(fn(mixed $value): array => [$value]);
```

```
$this->before(fn(...$arguments): array => $arguments);
```

```
$this->before(fn(): bool => false);
```

```
$this->before(function (mixed $value): void {
    //
};
```

#### *decoratedMethods(): array*

The `decoratedMethods` method defines function names to be decorated. The default function name is `execute`.

```
$this->decoratedMethods();
```

#### *forgetActionWrapper(): static*

The `forgetActionWrapper` method unsets the action wrapper from the action.

```
$this->forgetActionWrapper();
```

#### *getActionWrapper(): ActionWrapper|static*

The `getActionWrapper` method returns a cached ActionsWrapper or makes and caches an ActionWrapper then return.

```
$this->getActionWrapper();
```

#### *makeActionWrapper(): ActionWrapper*

The `makeActionWrapper` method creates a new ActionWrapper instance.

```
$this->makeActionWrapper();
```

#### *resetActionWrapper(): static*

The `resetActionWrapper` method removes all pipes from the action wrapper.

```
$this->resetActionWrapper();
```

#### *through(callable $decorator): ActionWrapper|static*

The `through` method adds the callable decorator through which the action will be sent.

```
$this->through(fn(array $arguments, \Closure $next): \Closure => $next($attributes));
```

### Extended methods

> ***Note:*** The next methods available in `Traits\HasActionWrapper`.
>```
>class Example
>{
>    use \R3bzya\ActionWrapper\Traits\HasActionWrapper;
>
>    ...
>}
>```

#### *refreshModel(): ActionWrapper|static*

The `refreshModel` method reloads the model instance with fresh attributes from the
database. [see](https://laravel.com/docs/10.x/eloquent#refreshing-models)

```
$this->refreshModel();
```

#### *tap(callable $decorator): ActionWrapper|static*

The `tap` method calls the given Closure with the action result then return the action result.

```
$this->tap(function (mixed $result): void {
    //
});
```

#### *tapWhen(mixed $condition, callable $decorator): ActionWrapper|static*

The `tapWhen` method calls the given Closure then return the action result when the condition is truthy.

```
$this->tapWhen(true, function (mixed $result): void {
    //
});
```

#### *throwIf(mixed $condition, Throwable $throwable = new RuntimeException, bool $strict = false): ActionWrapper|static*

The `throwIf` method will throw the given exception when the action result will be equal to the given condition.

```
$this->throwIf(true);
```

```
$this->throwIf(fn(mixed $result): mixed => true);
```

#### *throwIfNot(mixed $condition, Throwable $throwable = new RuntimeException, bool $strict = false): ActionWrapper|static*

The `throwIfNot` method will throw the given exception when the action result will be not equal to the given condition.

```
$this->throwIfNot(true);
```

```
$this->throwIfNot(fn(mixed $result): mixed => true);
```

#### *throwIfNotDone(Throwable $throwable = new NotDoneException, bool $strict = true): ActionWrapper|static*

The `throwIfNotDone` method will throw the given exception when the action result will be false.

```
$this->throwIfNotDone();
```

#### *throwUnless(mixed $condition, Throwable $throwable = new RuntimeException): ActionWrapper|static*

The `throwUnless` method will throw the given exception when the condition evaluates to false.

```
$this->throwUnless(false);
```

```
$this->throwUnless(fn(mixed $result): mixed => false);
```

#### *throwWhen(mixed $condition, Throwable $throwable = new RuntimeException): ActionWrapper|static*

The `throwWhen` method will throw the given exception when the condition evaluates to true.

```
$this->throwWhen(true);
```

```
$this->throwWhen(fn(mixed $result): mixed => true);
```

#### *transaction(int $attempts = 1): ActionWrapper|static*

The `transaction` method begins a new database transaction with try/catch,
if the code does not throw an exception the transaction will be committed,
else the transaction will be rolled back. [see](https://laravel.com/docs/10.x/database#database-transactions)

```
$this->transaction();
```

#### *unless(mixed $condition, callable $callable): ActionWrapper|static*

The `unless` method will execute the given callback when the condition evaluates to false,
otherwise the `decoratedMethod` execution result will be returned.

```
$this->unless(fn(mixed $result): mixed => false, fn(): mixed => false);
```

```
$this->unless(false, fn(mixed $result): mixed => $result);
```

#### *unsetModelRelations(): ActionWrapper|static*

The `unsetModelRelations` method unsets all the loaded relations from the
model. [see](https://laravel.com/api/10.x/Illuminate/Database/Eloquent/Concerns/HasRelationships.html#method_unsetRelations)

```
$this->unsetModelRelations();
```

#### *when(mixed $condition, callable $callable): ActionWrapper|static*

The `when` method will execute the given callback when the condition evaluates to true,
otherwise the `decoratedMethod` execution result will be returned.

```
$this->when(fn(mixed $result): mixed => true, fn(): mixed => true);
```

```
$this->when(true, fn(mixed $result): mixed => $result);
```

## Testing

```
composer test
```

## License

The MIT License (MIT). Please see [MIT license file](LICENSE.md) for more information.
