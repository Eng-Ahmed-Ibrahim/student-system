<div class="mb-3">
    <label for="grade_level" class="form-label">الصف الدراسي</label>
    <select name="grade_level" id="grade_level" class="form-select" required>
        <option value="" disabled selected>اختر الصف الدراسي</option>
        <option value="1">الصف الأول الثانوي</option>
        <option value="2">الصف الثاني الثانوي</option>
        <option value="3">الصف الثالث الثانوي</option>
    </select>
</div>

<div class="mb-3">
    <label for="group_id" class="form-label">المجموعة</label>
    <select name="group_id" id="group_id" class="form-select" required>
        <option value="" disabled selected>اختر المجموعة</option>
        @foreach ($groups as $group)
            <option value="{{ $group->id }}" data-grade="{{ $group->grade_level }}">{{ $group->name }}</option>
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
