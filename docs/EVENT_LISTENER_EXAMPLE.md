# Event/Listener Communication Between Modules

This guide demonstrates how to implement cross-module communication using Laravel Events and Listeners in this modular architecture.

## Overview

Events and Listeners allow modules to communicate without tight coupling. For example, when a User is created, other modules (like Subscription, Notification, etc.) can react to this event.

## Example: UserCreated Event → Subscription Listener

This example shows how the `User` module dispatches a `UserCreated` event, and the `Subscription` module listens to it.

### 1. Create UserCreated Event (User Module)

The event is already generated when you create a module with `--events` flag:

```php
// app/Modules/User/Application/Events/UserCreated.php
<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Events;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user
    ) {}
}
```

### 2. Dispatch Event in CreateUserAction

Update `app/Modules/User/Application/Actions/CreateUserAction.php`:

```php
<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Exceptions\CreateException;
use App\Modules\User\Application\DTO\CreateUserDTO;
use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Application\Events\UserCreated;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(CreateUserDTO $dto): UserResponseDTO
    {
        $userData = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'email_verified_at' => $dto->emailVerifiedAt,
        ];

        /** @var \App\Modules\User\Infrastructure\Models\User $user */
        $user = $this->userRepository->create($userData);

        if ($user === null) {
            throw new CreateException('Failed to create user');
        }

        // Dispatch event
        Event::dispatch(new UserCreated($user));

        return UserResponseDTO::fromUser($user);
    }
}
```

### 3. Create Listener in Subscription Module

Create a listener in another module (e.g., `Subscription`):

```php
// app/Modules/Subscription/Application/Listeners/SubscribeUserToNewsletter.php
<?php

declare(strict_types=1);

namespace App\Modules\Subscription\Application\Listeners;

use App\Modules\User\Application\Events\UserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SubscribeUserToNewsletter implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        // Subscribe user to newsletter
        // Example: Send welcome email, create subscription record, etc.
        \Log::info("Subscribing user {$user->email} to newsletter");

        // You can also dispatch notifications here
        // $user->notify(new WelcomeEmailNotification($user));
    }
}
```

### 4. Register Listener in EventServiceProvider

Register the listener in `app/Providers/EventServiceProvider.php`:

```php
<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Subscription\Application\Listeners\SubscribeUserToNewsletter;
use App\Modules\User\Application\Events\UserCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserCreated::class => [
            SubscribeUserToNewsletter::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
```

## Auto-Registration (Alternative)

The application also supports auto-registration of events and listeners within the same module. This is handled by `ModularServiceProvider`:

```php
// Automatically registers:
// - app/Modules/User/Application/Events/UserCreated.php
// - app/Modules/User/Application/Listeners/UserCreatedListener.php
```

However, for cross-module communication, you need to manually register listeners in `EventServiceProvider`.

## Using Notifications

You can also send notifications from listeners:

```php
// app/Modules/Subscription/Application/Listeners/SubscribeUserToNewsletter.php
use App\Modules\User\Application\Events\UserCreated;
use App\Modules\User\Application\Notifications\WelcomeEmailNotification;

public function handle(UserCreated $event): void
{
    $user = $event->user;
    
    // Send notification
    $user->notify(new WelcomeEmailNotification($user));
    
    // Or queue it
    $user->notify((new WelcomeEmailNotification($user))->delay(now()->addMinutes(5)));
}
```

## Queued Listeners

Listeners can be queued for asynchronous processing:

```php
class SubscribeUserToNewsletter implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserCreated $event): void
    {
        // This will run in the background queue
    }
}
```

Make sure your queue is running:

```bash
php artisan queue:work
```

## Testing Events and Listeners

### Test Event Dispatch

```php
// tests/Unit/User/CreateUserActionTest.php
use App\Modules\User\Application\Events\UserCreated;
use Illuminate\Support\Facades\Event;

public function test_user_created_event_is_dispatched(): void
{
    Event::fake();

    $dto = CreateUserDTO::fromArray([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $this->createUserAction->execute($dto);

    Event::assertDispatched(UserCreated::class);
}
```

### Test Listener Execution

```php
// tests/Unit/Subscription/SubscribeUserToNewsletterTest.php
use App\Modules\User\Application\Events\UserCreated;
use App\Modules\User\Infrastructure\Models\User;

public function test_user_is_subscribed_to_newsletter(): void
{
    $user = User::factory()->create();

    $listener = new SubscribeUserToNewsletter();
    $listener->handle(new UserCreated($user));

    // Assert subscription was created
    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
    ]);
}
```

## Best Practices

1. **Keep Events Simple**: Events should only contain the data needed by listeners
2. **Use Queued Listeners**: For time-consuming operations, use `ShouldQueue`
3. **Type Safety**: Use type hints for event properties
4. **Documentation**: Document which modules listen to which events
5. **Testing**: Always test event dispatching and listener execution

## Module Generator Support

When generating a module with `--events` flag:

```bash
php artisan make:module Product --events
```

This automatically generates:
- `ProductCreated` event
- `ProductUpdated` event
- `ProductDeleted` event
- Corresponding listeners

Events are automatically dispatched in Actions when using the `--events` flag.

