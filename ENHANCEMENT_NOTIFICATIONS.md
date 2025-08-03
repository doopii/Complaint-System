# Real-time Notifications Enhancement

## Install Pusher for Real-time
```bash
composer require pusher/pusher-php-server
npm install laravel-echo pusher-js
```

## Database Migration
```php
// Create notifications table
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('complaint_id')->constrained();
    $table->string('type'); // 'status_update', 'new_comment', 'assigned'
    $table->string('title');
    $table->text('message');
    $table->boolean('read')->default(false);
    $table->timestamps();
});
```

## Notification Component
```php
// app/Models/Notification.php
class Notification extends Model {
    protected $fillable = ['user_id', 'complaint_id', 'type', 'title', 'message', 'read'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function complaint() {
        return $this->belongsTo(Complaint::class);
    }
}
```

## Real-time Broadcasting
```php
// When complaint status changes
broadcast(new ComplaintStatusUpdated($complaint, $user))->toOthers();

// In your Blade template
<div id="notifications" class="notification-bell">
    🔔 <span class="notification-count">{{ auth()->user()->unreadNotifications()->count() }}</span>
</div>

<script>
Echo.channel('complaints')
    .listen('ComplaintStatusUpdated', (e) => {
        // Show toast notification
        showToast(`Your complaint #${e.complaint.id} status updated to: ${e.complaint.status}`);
        updateNotificationCount();
    });
</script>
```
