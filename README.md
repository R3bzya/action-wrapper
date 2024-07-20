# Action Wrapper

ActionWrapper is a simple yet flexible action decorator.

## Installation

Via Composer:

```shell
composer require r3bzya/action-wrapper
```

## Available classes

When you need to wrap some actions, and you don't have to create a new action class
use the `FluentAction` class.
You can make a new instance with the `wrapper` function.

> ***Note:*** The FluentAction class has [extended methods](#extended-methods).
>
>```
>$action = new \R3bzya\ActionWrapper\Support\FluentAction;
>
>$action->execute(fn(): bool => true);
>```
The shortest way:
>```
>wrapper()->execute(fn(): bool => true);
>```

## Available methods

### Base methods

> ***Note:*** These methods available in `Support\Traits\Simples\HasActionWrapper`.
>
>```
>class Example
>{
>    use \R3bzya\ActionWrapper\Support\Traits\Simples\HasActionWrapper;
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
To change the input arguments, the given callable should return an array otherwise the `before` method will work like a tap.

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

#### *flushPipes(): ActionWrapper|static*

The `flushPipes` method removes all pipes from the ActionWrapper instance.

```
$this->flushPipes();
```

#### *forgetActionWrapper(): ActionWrapper|static*

The `forgetActionWrapper` method unsets the action wrapper from the action.

```
$this->forgetActionWrapper();
```

#### *getActionWrapper(): ActionWrapper|static*

The `getActionWrapper` method returns a cached ActionsWrapper or makes and caches an ActionWrapper then return.

```
$this->getActionWrapper();
```

#### *makeActionWrapper(): ActionWrapper|static*

The `makeActionWrapper` method creates a new ActionWrapper instance.

```
$this->makeActionWrapper();
```

#### *pipes(): array*

The `pipes` method returns an array of pipes from ActionWrapper instance.

```
$this->pipes();
```

#### *through(callable $decorator): ActionWrapper|static*

The `through` method adds the callable decorator through which the action will be sent.

```
$this->through(fn(array $arguments, \Closure $next): \Closure => $next($attributes));
```

### Extended methods

> ***Note:*** The next methods available in `Support\Traits\HasActionWrapper`.
>```
>class Example
>{
>    use \R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;
>
>    ...
>}
>```

#### *catch(): ActionWrapper|static*

The `catch` method returns an exception to respond without throwing an exception.

```
$this->catch();
```

#### *payload(callable $callable): ActionWrapper|static*

The `payload` method aggregates action data then set it in a payload.
If you need to change a result, change it in the payload by setters.

```
wrapper()->payload(function (\R3bzya\ActionWrapper\Support\Payloads\Payload $payload): void {
    //
});
```
#### *public function payloadWhen(callable $callable, mixed $value): ActionWrapper|static*

The `payloadUnless` method applies the given callable on a payload if the given value is truthy.

```
wrapper()->payload(function (\R3bzya\ActionWrapper\Support\Payloads\Payload $payload): void {
    //
}, true);
```

#### *public function payloadUnless(callable $callable, mixed $value): ActionWrapper|static*

The `payloadUnless` method applies the given callable on a payload if the given value is falsy.

```
wrapper()->payloadUnless(function (\R3bzya\ActionWrapper\Support\Payloads\Payload $payload): void {
    //
}, false);
```

#### *retry(int $attempts): ActionWrapper|static*

The `retry` method retries the action while it has an exception.

```
$this->retry(1);
```


#### *refreshModel(): ActionWrapper|static*

The `refreshModel` method reloads the model instance with fresh attributes from the
database. [see](https://laravel.com/docs/10.x/eloquent#refreshing-models)

```
$this->refreshModel();
```

#### *safe(): ActionWrapper|static*

The `safe` method returns false when an exception thrown.

```
$this->safe();
```

#### *tap(callable $decorator = null): ActionWrapper|static*

The `tap` method calls the given Closure with the action result then return the action result.

```
$this->tap(function (mixed $result): void {
    //
});
```

#### *tapWhen(mixed $condition, callable $decorator = null): ActionWrapper|static*

The `tapWhen` method calls the given Closure when the condition is truthy then return the action result.

```
$this->tapWhen(true, function (mixed $result): void {
    //
});
```

#### *tapUnless(mixed $condition, callable $callable = null): ActionWrapper|static*

The `tapUnless` method calls the given Closure when the condition is falsy then return the action result.

```
$this->tapUnless(false, function (mixed $result): void {
    //
});
```

#### *throwIfNotDone(Throwable $throwable = new NotDoneException): ActionWrapper|static*

The `throwIfNotDone` method will throw the given exception when the action result is false.

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

#### *try(mixed $value): ActionWrapper|static*

The `try` method defines how to respond to a thrown exception.

```
$this->try(false);
$this->try(fn() => false);
$this->try(fn(Throwable $e) => $e);
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

## Artisan commands

The `make:action` command makes the action class.
The command will try to guess the base action on the model to use the template.
Also, you don't have to specify the model,
use the name 'CreateUser' or 'CreateUserAction' with the option `-m` and watch the magic.
If the model has a directory (e.g. 'User'), you will use the name 'User/CreateUser' or 'User/CreateUserAction'.

#### make:action

```
php artisan make:action FooAction
```

You can use the option `-d` to create DTO.

```
php artisan make:action FooAction -d
```

#### make:dto

The `make:dto` command makes the dto class.

```
php artisan make:dto FooDto
```

## Testing

```
composer test
```

## License

The MIT License (MIT). Please see [MIT license file](LICENSE.md) for more information.
