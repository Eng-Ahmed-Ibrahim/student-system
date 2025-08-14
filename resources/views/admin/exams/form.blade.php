
<input type="hidden" name="grade_level" value="{{ request('grade_level') }}">

<div class="mb-3">
    <label for="group_id" class="form-label">المجموعة</label>
    <select name="group_id" id="group_id" class="form-select" required>
        <option value="" disabled selected>اختر المجموعة</option>
        @foreach ($groups as $group)
        @if($group->grade_level == request('grade_level'))
            <option value="{{ $group->id }}" data-grade="{{ $group->grade_level }}">{{ $group->name }}</option>
            @endif
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="name" class="form-label">اسم الامتحان</label>
    <input type="text" name="name" id="name" class="form-control">
</div>

<div class="mb-3">
    <label for="exam_date" class="form-label">تاريخ الامتحان</label>
    <input type="date" name="exam_date" id="exam_date" class="form-control" required>
</div>

<div class="mb-3">
    <label for="total_score" class="form-label">الدرجة الكاملة</label>
    <input type="number" name="total_score" id="total_score" class="form-control" min="1" required>
</div>
