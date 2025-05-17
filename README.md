# 🕒 Laravel User Time Zone Handler

A plug-and-play solution for handling user time zones in Laravel. Automatically store, retrieve, and display dates/times in each user's preferred time zone—perfect for SaaS, dashboards, and any app with a global user base.

---

## 🚀 Features

- Detects and stores each user's preferred time zone
- Converts all date/times to user's local time for display
- Middleware for automatic time zone switching per request
- Eloquent date casting helpers
- Easy integration with authentication
- Fallback to app default if user time zone is unset

---

## 📦 Installation

```bash
git clone git@github.com:codewithmikee/handling-user-time-zone-in-laravel.git
cd handling-user-time-zone-in-laravel
sc install # or composer install
cp .env.example .env
php artisan key:generate
```

---

## ⚙️ Setup

1. **Add `time_zone` column to your users table:**

```php
// database/migrations/xxxx_xx_xx_add_time_zone_to_users.php
Schema::table('users', function (Blueprint $table) {
    $table->string('time_zone')->nullable()->after('email');
});
```

2. **Run migrations:**
```bash
php artisan migrate
```

3. **Add the middleware:**

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware ...
        \App\Http\Middleware\SetUserTimeZone::class,
    ],
];
```

4. **Publish and customize the middleware if needed:**

```bash
php artisan make:middleware SetUserTimeZone
```

Example middleware logic:
```php
public function handle($request, Closure $next)
{
    if (auth()->check() && auth()->user()->time_zone) {
        date_default_timezone_set(auth()->user()->time_zone);
    } else {
        date_default_timezone_set(config('app.timezone'));
    }
    return $next($request);
}
```

---

## 🛠 Usage

- **Set user time zone:**  
  Let users select their time zone in their profile settings and save it to the `time_zone` column.

- **Display dates in user's time zone:**  
  Use Carbon's `setTimezone()` method:
  ```php
  $userTime = $event->start_at->setTimezone(auth()->user()->time_zone);
  ```

- **Fallback:**  
  If the user has no time zone set, the app's default (`config('app.timezone')`) is used.

---

## 🧑‍💻 Example

```php
// Controller example
public function show(Event $event)
{
    $userTz = auth()->user()->time_zone ?? config('app.timezone');
    $eventTime = $event->start_at->setTimezone($userTz);
    return view('event.show', compact('event', 'eventTime'));
}
```

---

## 📝 Customization

- Change the column name or logic as needed for your user model.
- Extend the middleware to support API tokens or guests.
- Add frontend time zone detection (e.g., using JS Intl API) for auto-suggestions.

---

## 🧪 Testing

- Log in as different users with different time zones.
- Check that all displayed times match the user's selected time zone.

---

## 🤝 Contributing

Pull requests and issues welcome! Please open an issue to discuss your idea before submitting a PR.

---

## 📄 License

MIT
