# Action Wrapper

## Introduction

ActionWrapper is a simple and flexible way to decorate your actions.

## Installation

Via Composer:

```shell
composer require r3bzya/action-wrapper
```
# Publishing

The commands below allow you to publish specific components of the package to your Laravel project:

```shell
php artisan vendor:publish --tag=action-wrapper-config
php artisan vendor:publish --tag=action-wrapper-stubs
```

## Available classes

When you need to wrap up some actions, and you don't want to create a new class for the action,
use the `FluentAction` class.
You can create a new instance using the `wrapper` function.

> ***Note:*** The FluentAction class has [extended methods](#extended-methods).
>
>```php
>$action = new \R3bzya\ActionWrapper\Support\FluentAction;
>
>$action->execute(fn(): bool => true);
>```
>The shortest way:
>```php
>wrapper()->execute(fn(): bool => true);
>```

## Available methods

### Base methods

> ***Note:*** These methods available in `\R3bzya\ActionWrapper\Support\Traits\Simples\HasActionWrapper`.
>
>```php
>class Example
>{
>    use \R3bzya\ActionWrapper\Support\Traits\Simples\HasActionWrapper;
>
>    ...
>}
>```

#### *after()*

The `after` method allows you to add a callable function to the ActionWrapper,
which will be executed after any of the decorated methods have been called.

```php
wrapper()->after(fn(mixed $result): mixed => $result);
```

#### *before()*

The `before` method allows you to add a callable function to the ActionWrapper that will be executed before any of
the decorated methods.
If the given callable returns false, the action will stop executing.
To modify the input arguments, the callable should return an array.
Otherwise, the `before` method acts like a tap, executing the callable without changing the input.

```php
wrapper()->before(fn(mixed $value): array => [$value]);
```

```php
wrapper()->before(fn(...$arguments): array => $arguments);
```

```php
wrapper()->before(fn(): bool => false);
```

```php
wrapper()->before(function (mixed $value): void {
    //
};
```

#### *forgetActionWrapper()*

The `forgetActionWrapper` method unsets the action wrapper from an action.

```php
wrapper()->forgetActionWrapper();
```

#### *getActionWrapper()*

The `getActionWrapper` method returns a cached ActionWrapper or creates and caches a new ActionWrapper, then returns.

```php
wrapper()->getActionWrapper();
```

#### *makeActionWrapper()*

The `makeActionWrapper` method creates a new ActionWrapper instance.

```php
wrapper()->makeActionWrapper();
```

#### *pipes()*

The `pipes` method returns an array of pipes from the ActionWrapper instance.

```php
wrapper()->pipes();
```

#### *through()*

The `through` method adds a callable decorator that the action will be sent through.

```php
wrapper()->through(fn(array $arguments, \Closure $next): \Closure => $next($attributes));
```

### Extended methods

> ***Note:*** The next methods available in `\R3bzya\ActionWrapper\Support\Traits\HasActionWrapper`
> or you can extend the `\R3bzya\ActionWrapper\Support` class.
>```php
>class Example
>{
>    use \R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;
>
>    ...
>}
>```

#### *abortIf()*

The `abortIf` method throws an HttpException with the given data if the result is true.

```php
wrapper()->abortIf();
```

#### *abortUnless()*

The `abortUnless` method throws an HttpException with the given data unless the result is true.

```php
wrapper()->abortUnless();
```

#### *throwableInsteadOfThrow()*

The `throwableInsteadOfThrow` method returns an exception without throwing.

```php
wrapper()->throwableInsteadOfThrow();
```

### *each()*

The `each` method runs each element of the array through an action method by default 'execute'.
You can use closure when you need to send multiple elements of a function.

```php
wrapper()->each([1, 2, 3]);
wrapper()->each([fn() => [1, 2, 3], fn() => [4, 5]]);
```

#### *log()*

The `log` method is the default method for logging action data.
Write the log if the given value is truthy.

> ***Note:*** You can use a custom logger to safe logs to a specific file and other channels.

```php
wrapper()->log(function (\R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload $payload) {
    \R3bzya\ActionWrapper\Support\Facades\Log::info('README.md,stack:Payload data', $payload->all());
});
```

#### *logArguments()*

The `logArguments` method logs the action arguments.

```php
wrapper()->logArguments('logArguments');
```

#### *logExceptions()*

The `logExceptions` method logs an exception if an exception occurs.

```php
wrapper()->logExceptions('logExceptions');
```

#### *logIfNotDone()*

The `logIfNotDone` method logs the action data if an exception was thrown NotDoneException, or if the result is false.

```php
wrapper()->logIfNotDone('logIfNotDone');
```

#### *logIfFailed()*

The `logIfFailed` method logs the action data if an exception was thrown or if the result is not present in the payload.

```php
wrapper()->logIfFailed('logIfFailed');
```

#### *logPerformance()*

The `logPerformance` method logs the performance of an action in milliseconds.

```php
wrapper()->logPerformance('logPerformance');
```

#### *logResult()*

The `logResult` method logs the result of an action.
If an exception is thrown during the action, the result will not be logged.

```php
wrapper()->logResult('logResult');
```

#### *payload()*

The `payload` method aggregates action data and sets it in a payload.
If you need to change the result, you can change it in the payload using setters.

```php
wrapper()->payload(function (\R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload $payload): void {
    //
});
```

#### *payloadWhen()*

The `payloadWhen` method applies the given callable to a payload if the given value is truthy.

```php
wrapper()->payloadWhen(function (\R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload $payload): void {
    //
}, true);
```

#### *payloadUnless()*

The `payloadUnless` method applies the given callable to a payload if the given value is falsy.

```php
wrapper()->payloadUnless(function (\R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload $payload): void {
    //
}, false);
```

#### *retry()*

The `retry` method retries an action if it has an exception.

```php
wrapper()->retry(1);
```


#### *refreshModel()*

The `refreshModel` method reloads the model instance with fresh attributes from the
database. [see](https://laravel.com/docs/10.x/eloquent#refreshing-models)

```php
wrapper()->refreshModel();
```

#### *falseInsteadOfThrowable()*

The `falseInsteadOfThrowable` method returns false when an exception is thrown.

```php
wrapper()->falseInsteadOfThrowable();
```

#### *tap()*

The `tap` method calls the given Closure with the action result then returns the action result.

```php
wrapper()->tap(function (mixed $result): void {
    //
});
```

#### *tapWhen()*

The `tapWhen` method calls the given Closure if the given value is truthy then returns the action result.

```php
wrapper()->tapWhen(true, function (mixed $result): void {
    //
});
```

#### *tapUnless()*

The `tapUnless` method calls the given Closure if the given value is falsy then returns the action result.

```php
wrapper()->tapUnless(false, function (mixed $result): void {
    //
});
```

#### *throwIf()*

The `throwIf` method throws the given exception if the result is true.

```php
wrapper()->throwIf();
```

#### *throwIfNotDone()*

The `throwIfNotDone` method throws the given exception if the result is false.

```php
wrapper()->throwIfNotDone();
```

#### *throwUnless()*

The `throwUnless` method throws the given exception unless the result is true.

```php
wrapper()->throwUnless();
```

#### *transaction()*

The `transaction` method begins a new database transaction using try/catch,
if the code does not throw an exception the transaction is committed,
otherwise the transaction will be rolled back.
[see](https://laravel.com/docs/10.x/database#database-transactions)

```php
wrapper()->transaction();
```

#### *catch()*

The `catch` method defines how to handle an exception that is thrown.

```php
wrapper()->catch(false);
wrapper()->catch(fn() => false);
wrapper()->catch(fn(Throwable $e) => $e);
```

#### *unless()*

The `unless` method executes the given callback if the given value is falsy,
or returns the action's result.

```php
wrapper()->unless(fn(mixed $result): mixed => false, fn(): mixed => false);
```

```php
wrapper()->unless(false, fn(mixed $result): mixed => $result);
```

#### *unsetModelRelations()*

The `unsetModelRelations` method unsets all the loaded relations from the
model. [see](https://laravel.com/api/10.x/Illuminate/Database/Eloquent/Concerns/HasRelationships.html#method_unsetRelations)

```php
wrapper()->unsetModelRelations();
```

#### *when()*

The `when` method executes the given callable if the given value is truthy,
or returns the action's result.

```php
wrapper()->when(fn(mixed $result): mixed => true, fn(): mixed => true);
```

```php
wrapper()->when(true, fn(mixed $result): mixed => $result);
```

#### *wrap()*

The `wrap` method wraps the result in the given class.

```php
wrapper()->wrap();
```

## Artisan commands

The `make:action` command makes an action class.
The command tries to guess the action from the model and uses the template.
Also, you don't need to specify the model,
use the name 'CreateUser' or 'CreateUserAction' with the option `-m` and watch the magic happen.
If the model has a directory (e.g. 'User'), you will use the name 'User/CreateUser' or 'User/CreateUserAction'.

#### make:action

```shell
php artisan make:action FooAction
```

You can use the option `-d` to create a DTO.

```shell
php artisan make:action FooAction -d
```

#### make:dto

The `make:dto` command makes the dto class.

```shell
php artisan make:dto FooDto
```

## Testing

```shell
composer test
```

## License

The MIT License (MIT). Please see [MIT license file](LICENSE.md) for more information.
