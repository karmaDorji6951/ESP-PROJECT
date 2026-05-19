@php($model = $employee ?? null)
@push('scripts')
<script>
const gewogsByDzongkhag = @json($gewogsByDzongkhag ?? []);

document.addEventListener('DOMContentLoaded', function() {
    const dzongkhagSelect = document.getElementById('dzongkhag_id');
    const gewogSelect = document.getElementById('gewog_id');
    
    dzongkhagSelect.addEventListener('change', function() {
        const dzongkhagId = this.value;
        gewogSelect.innerHTML = '<option value="">Select Gewog</option>';
        
        if (dzongkhagId && gewogsByDzongkhag[dzongkhagId]) {
            gewogsByDzongkhag[dzongkhagId].forEach(function(gewog) {
                const option = document.createElement('option');
                option.value = gewog.id;
                option.textContent = gewog.name;
                gewogSelect.appendChild(option);
            });
        }
    });
    
    // Trigger change on page load if dzongkhag is pre-selected
    if (dzongkhagSelect.value) {
        dzongkhagSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

<div class="col-md-6">
    <label class="form-label">Full Name</label>
    <input type="text" name="name" value="{{ old('name', $model->name ?? '') }}" class="form-control" required>
</div>
<div class="col-md-6">
    <label class="form-label">CID</label>
    <input type="text" name="cid" value="{{ old('cid', $model->cid ?? '') }}" class="form-control" required>
</div>
<div class="col-md-6">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $model->phone ?? '') }}" class="form-control">
</div>
<div class="col-md-6">
    <label class="form-label">Role / Position</label>
    <select name="role_title" class="form-select" required>
        <option value="">Select Role</option>
        <option value="cleaner" @selected(old('role_title', $model->role_title ?? '') === 'cleaner')>Cleaner</option>
        <option value="guard" @selected(old('role_title', $model->role_title ?? '') === 'guard')>Guard</option>
        <option value="gardener" @selected(old('role_title', $model->role_title ?? '') === 'gardener')>Gardener</option>
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">Dzongkhag</label>
    <select name="dzongkhag_id" id="dzongkhag_id" class="form-select" required>
        <option value="">Select Dzongkhag</option>
        @foreach($dzongkhags ?? [] as $dzongkhag)
            <option value="{{ $dzongkhag->id }}" @selected(old('dzongkhag_id', $model->dzongkhag_id ?? '') == $dzongkhag->id)>{{ $dzongkhag->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">Gewog</label>
    <select name="gewog_id" id="gewog_id" class="form-select" required>
        <option value="">Select Gewog</option>
        @if(isset($model->gewog_id))
            <option value="{{ $model->gewog_id }}" selected>{{ $model->gewog->name ?? '' }}</option>
        @endif
    </select>
</div>
<div class="col-12">
    <label class="form-label">Address</label>
    <textarea name="address" rows="3" class="form-control">{{ old('address', $model->address ?? '') }}</textarea>
</div>
<div class="col-md-6">
    <label class="form-label">Joining Date</label>
    <input type="date" name="joining_date" value="{{ old('joining_date', isset($model) && $model->joining_date ? $model->joining_date->format('Y-m-d') : '') }}" class="form-control" required>
</div>
<div class="col-md-6">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
        @foreach(['Active', 'Inactive'] as $status)
            <option value="{{ $status }}" @selected(old('status', $model->status ?? 'Active') === $status)>{{ $status }}</option>
        @endforeach
    </select>
</div>
<div class="col-12">
    <label class="form-label">Profile Photo</label>
    <input type="file" name="photo" class="form-control">
    @if($model?->photo_path)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $model->photo_path) }}" alt="photo" width="120" class="rounded border">
        </div>
    @endif
</div>
