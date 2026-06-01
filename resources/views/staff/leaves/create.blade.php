@extends('layouts.app')

@section('title', 'Request Leave')
@section('page_title', 'Request Leave')

@section('content')
<div class="leave-request-page container-fluid py-3 py-lg-4">
    <div class="leave-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase fw-semibold mb-2 leave-hero-kicker">Leave Request</div>
                <h1 class="leave-hero-title mb-2">Request a Leave</h1>
                <p class="leave-hero-subtitle mb-0">Check your annual allowance and the current weekly or monthly quota window before you submit.</p>
            </div>
            <a href="{{ route('staff.leaves.index') }}" class="btn btn-light leave-back-btn">Back</a>
        </div>
    </div>

    @isset($balance)
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-2-5">
                <div class="metric-card card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="metric-label">Annual Allowance</div>
                        <div class="metric-value">{{ $balance['allowance'] }}</div>
                        <div class="metric-meta">days total</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2-5">
                <div class="metric-card card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="metric-label">Used</div>
                        <div class="metric-value text-danger">{{ $balance['approved'] }}</div>
                        <div class="metric-meta">approved</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2-5">
                <div class="metric-card card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="metric-label">Pending</div>
                        <div class="metric-value text-warning">{{ $balance['pending'] }}</div>
                        <div class="metric-meta">awaiting approval</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2-5">
                <div class="metric-card card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="metric-label">This Request</div>
                        <div class="metric-value text-primary" id="request_days_display">0</div>
                        <div class="metric-meta">selected days</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-2-5">
                <div class="metric-card card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="metric-label">Remaining</div>
                        <div class="metric-value text-success">{{ $balance['remaining'] }}</div>
                        <div class="metric-meta">after approval</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 quota-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-semibold">Leave balance breakdown</div>
                    <div class="text-muted small"><span id="annual_total_label">{{ $balance['allowance'] }}</span> days total</div>
                </div>

                <div class="progress leave-progress mb-2">
                    <div class="progress-bar bg-danger" id="used_bar" style="width: {{ $balance['allowance'] > 0 ? round(($balance['approved'] / $balance['allowance']) * 100, 2) : 0 }}%"></div>
                    <div class="progress-bar bg-warning" id="pending_bar" style="width: {{ $balance['allowance'] > 0 ? round(($balance['pending'] / $balance['allowance']) * 100, 2) : 0 }}%"></div>
                    <div class="progress-bar bg-primary" id="request_bar" style="width: 0%"></div>
                    <div class="progress-bar bg-success" id="remaining_bar" style="width: {{ $balance['allowance'] > 0 ? round(($balance['remaining'] / $balance['allowance']) * 100, 2) : 0 }}%"></div>
                </div>

                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><span class="legend-dot bg-danger"></span> Used (<span id="used_label">{{ $balance['approved'] }}</span>)</span>
                    <span><span class="legend-dot bg-warning"></span> Pending (<span id="pending_label">{{ $balance['pending'] }}</span>)</span>
                    <span><span class="legend-dot bg-primary"></span> This request (<span id="request_label">0</span>)</span>
                    <span><span class="legend-dot bg-success"></span> Remaining (<span id="remaining_label">{{ $balance['remaining'] }}</span>)</span>
                </div>

                <div class="mt-3 small text-muted">
                    Weekly quota: <strong>{{ $balance['weekly_quota'] }}</strong> day{{ $balance['weekly_quota'] === 1 ? '' : 's' }}
                    <span class="mx-2">|</span>
                    Monthly quota: <strong>{{ $balance['monthly_quota'] }}</strong> day{{ $balance['monthly_quota'] === 1 ? '' : 's' }}
                </div>
            </div>
        </div>
    @endisset

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <div class="section-title mb-3">Leave request form</div>

            <form method="POST" action="{{ route('staff.leaves.store') }}" id="leave_request_form">
                @csrf

                <div class="alert alert-info leave-live-notice mb-3" id="quota_notice">
                    Select your dates to see how the request fits within your annual, weekly, and monthly limits.
                </div>

                <div class="mb-3">
                    <label class="form-label" for="leave_type">Leave type <span class="text-danger">*</span></label>
                    <select name="leave_type" id="leave_type" class="form-select @error('leave_type') is-invalid @enderror" required>
                        <option value="">-- select leave type --</option>
                        <option value="Sick Leave" {{ old('leave_type') === 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="Casual Leave" {{ old('leave_type') === 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                        <option value="Earned Leave" {{ old('leave_type') === 'Earned Leave' ? 'selected' : '' }}>Earned Leave</option>
                        <option value="Maternity Leave" {{ old('leave_type') === 'Maternity Leave' ? 'selected' : '' }}>Maternity Leave</option>
                        <option value="Paternity Leave" {{ old('leave_type') === 'Paternity Leave' ? 'selected' : '' }}>Paternity Leave</option>
                        <option value="Other" {{ old('leave_type') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('leave_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 align-items-start">
                    <div class="col-md-6">
                        <label class="form-label" for="from_date">From date <span class="text-danger">*</span></label>
                        <input type="date" name="from_date" id="from_date" class="form-control @error('from_date') is-invalid @enderror" value="{{ old('from_date') }}" required>
                        @error('from_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="to_date">To date <span class="text-danger">*</span></label>
                        <input type="date" name="to_date" id="to_date" class="form-control @error('to_date') is-invalid @enderror" value="{{ old('to_date') }}" required>
                        @error('to_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="leave-preview mt-3 mb-3">
                    <div>
                        <div class="fw-semibold">Working days selected</div>
                        <div class="text-muted small">Excludes weekends</div>
                    </div>
                    <div class="preview-count" id="selected_working_days">0</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="reason">Reason <span class="text-muted">(optional)</span></label>
                    <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" placeholder="Briefly describe the reason for your leave...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                    <a href="{{ route('staff.leaves.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submit_leave_btn">Submit Leave Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.leave-request-page {
    max-width: 1320px;
}

.leave-hero {
    background: linear-gradient(135deg, #0F2044 0%, #16345f 58%, #1D9E75 155%);
    color: #fff;
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 18px 40px rgba(15, 32, 68, 0.18);
    overflow: hidden;
    position: relative;
}

.leave-hero::after {
    content: '';
    position: absolute;
    inset: auto -100px -100px auto;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
}

.leave-hero-kicker {
    letter-spacing: 0.14em;
    opacity: 0.85;
    font-size: 12px;
}

.leave-hero-title {
    font-weight: 800;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
}

.leave-hero-subtitle {
    max-width: 760px;
    color: rgba(255, 255, 255, 0.9);
}

.leave-back-btn {
    border-radius: 999px;
    padding-inline: 18px;
    font-weight: 600;
    color: #0f2044;
    position: relative;
    z-index: 1;
}

.leave-back-btn:hover {
    color: #0f2044;
}

.leave-live-notice {
    border-radius: 14px;
    border: 1px solid rgba(15, 32, 68, 0.08);
}

.metric-card {
    border-radius: 16px;
    background: #fcfbf5;
}

.metric-label {
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 700;
    margin-bottom: 6px;
}

.metric-value {
    font-size: clamp(1.6rem, 2.2vw, 2.2rem);
    font-weight: 800;
    line-height: 1;
    color: #111827;
}

.metric-meta {
    margin-top: 4px;
    color: #6b7280;
}

.quota-card {
    border-radius: 18px;
    background: #fbf8ef;
}

.leave-progress {
    height: 10px;
    border-radius: 999px;
    overflow: hidden;
    background: #e5e7eb;
}

.leave-progress .progress-bar {
    min-width: 0;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
    vertical-align: middle;
}

.section-title {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #6b7280;
}

.leave-preview {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    background: #f8f6ed;
    border: 1px solid rgba(17, 24, 39, 0.08);
}

.preview-count {
    font-size: 2rem;
    font-weight: 800;
    color: #111827;
    line-height: 1;
}

@media (min-width: 992px) {
    .col-lg-2-5 {
        flex: 0 0 auto;
        width: 20%;
    }
}

@media (max-width: 991.98px) {
    .leave-hero {
        padding: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    const selectedWorkingDays = document.getElementById('selected_working_days');
    const requestDaysDisplay = document.getElementById('request_days_display');
    const requestLabel = document.getElementById('request_label');
    const requestBar = document.getElementById('request_bar');
    const remainingBar = document.getElementById('remaining_bar');
    const quotaNotice = document.getElementById('quota_notice');
    const submitButton = document.getElementById('submit_leave_btn');

    const weeklyQuota = {{ $balance['weekly_quota'] ?? 0 }};
    const monthlyQuota = {{ $balance['monthly_quota'] ?? 0 }};
    const annualAllowance = {{ $balance['allowance'] ?? 0 }};
    const baseUsed = {{ $balance['approved'] ?? 0 }} + {{ $balance['pending'] ?? 0 }};
    const annualRemaining = {{ $balance['remaining'] ?? 0 }};
    const weeklyRemaining = {{ $balance['weekly_remaining'] ?? 0 }};
    const monthlyRemaining = {{ $balance['monthly_remaining'] ?? 0 }};

    const parseDate = (value) => {
        if (!value) {
            return null;
        }

        const date = new Date(value + 'T00:00:00');
        return Number.isNaN(date.getTime()) ? null : date;
    };

    const isWeekend = (date) => {
        const day = date.getDay();
        return day === 0 || day === 6;
    };

    const countWorkingDays = (start, end) => {
        if (!start || !end || start > end) {
            return 0;
        }

        let count = 0;
        const cursor = new Date(start);

        while (cursor <= end) {
            if (!isWeekend(cursor)) {
                count += 1;
            }
            cursor.setDate(cursor.getDate() + 1);
        }

        return count;
    };

    const syncLabels = (days) => {
        const clampedDays = Math.max(0, Number(days) || 0);
        const totalAfterRequest = Math.max(0, annualAllowance - baseUsed - clampedDays);
        const issues = [];

        if (selectedWorkingDays) selectedWorkingDays.textContent = clampedDays;
        if (requestDaysDisplay) requestDaysDisplay.textContent = clampedDays;
        if (requestLabel) requestLabel.textContent = clampedDays;

        if (requestBar) {
            requestBar.style.width = annualAllowance > 0 ? `${Math.min(100, (clampedDays / annualAllowance) * 100)}%` : '0%';
        }

        if (remainingBar) {
            remainingBar.style.width = annualAllowance > 0 ? `${Math.min(100, (totalAfterRequest / annualAllowance) * 100)}%` : '0%';
        }

        if (clampedDays < 1) {
            issues.push('Select at least one working day.');
        }

        if (clampedDays > annualRemaining) {
            issues.push(`Annual allowance is finished. You only have ${annualRemaining} day${annualRemaining === 1 ? '' : 's'} left.`);
        }

        if (clampedDays > weeklyRemaining) {
            issues.push(`Weekly quota reached. You only have ${weeklyRemaining} day${weeklyRemaining === 1 ? '' : 's'} left this week.`);
        }

        if (clampedDays > monthlyRemaining) {
            issues.push(`Monthly quota reached. You only have ${monthlyRemaining} day${monthlyRemaining === 1 ? '' : 's'} left this month.`);
        }

        if (quotaNotice) {
            if (issues.length) {
                quotaNotice.className = 'alert alert-danger leave-live-notice mb-3';
                quotaNotice.innerHTML = `<strong>Quota reached</strong><br>${issues.join('<br>')}`;
            } else {
                quotaNotice.className = 'alert alert-success leave-live-notice mb-3';
                quotaNotice.innerHTML = 'This request fits within your current leave quota. You can submit it now.';
            }
        }

        if (submitButton) {
            submitButton.disabled = issues.length > 0;
        }
    };

    const updatePreview = () => {
        const start = parseDate(fromDate?.value);
        const end = parseDate(toDate?.value);
        const days = countWorkingDays(start, end);
        syncLabels(days);
    };

    fromDate?.addEventListener('change', updatePreview);
    toDate?.addEventListener('change', updatePreview);
    updatePreview();
});
</script>

@endsection
