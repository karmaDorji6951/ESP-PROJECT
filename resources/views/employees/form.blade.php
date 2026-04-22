@php($model = $employee ?? null)
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
    <input type="text" name="role_title" value="{{ old('role_title', $model->role_title ?? '') }}" class="form-control" required>
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
