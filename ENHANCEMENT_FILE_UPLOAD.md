# File Upload Enhancement

## Database Migration
```php
// Add to complaints table
$table->json('attachments')->nullable(); 
$table->integer('priority')->default(1); // 1=Low, 2=Medium, 3=High, 4=Critical
```

## View Enhancement (complaint form)
```html
<div class="form-group">
    <label for="attachments">Upload Evidence (Images/Documents):</label>
    <input type="file" id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx">
    <small>Max 5 files, 10MB each</small>
</div>

<div class="form-group">
    <label for="priority">Priority Level:</label>
    <select name="priority" required>
        <option value="1">🟢 Low - General inquiry</option>
        <option value="2">🟡 Medium - Minor issue</option>
        <option value="3">🟠 High - Urgent matter</option>
        <option value="4">🔴 Critical - Emergency</option>
    </select>
</div>
```

## Controller Enhancement
```php
public function store(Request $request) {
    $attachments = [];
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('complaint-attachments', 'public');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize()
            ];
        }
    }
    
    Complaint::create([
        // ... existing fields
        'attachments' => json_encode($attachments),
        'priority' => $request->priority
    ]);
}
```
