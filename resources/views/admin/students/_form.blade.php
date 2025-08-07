<div class="row">
    <div class="mb-3 col">
        <label for="grade_level" class="form-label">الصف الدراسي</label>
        <select name="grade_level" id="grade_level" class="form-select" required>
            <option value="" disabled {{ old('grade_level', $student->grade_level ?? '') == '' ? 'selected' : '' }}>اختر الصف الدراسي</option>
            <option value="1" {{ old('grade_level', $student->grade_level ?? '') == 1 ? 'selected' : '' }}>الصف الأول الثانوي</option>
            <option value="2" {{ old('grade_level', $student->grade_level ?? '') == 2 ? 'selected' : '' }}>الصف الثاني الثانوي</option>
            <option value="3" {{ old('grade_level', $student->grade_level ?? '') == 3 ? 'selected' : '' }}>الصف الثالث الثانوي</option>
        </select>
    </div>

    <div class="mb-3 col">
        <label for="group_id" class="form-label">المجموعة</label>
        <select name="group_id" id="group_id" class="form-select" required>
            <option value="" disabled {{ old('group_id', $student->group_id ?? '') == '' ? 'selected' : '' }}>اختر المجموعة</option>
            @php
                $selectedGrade = old('grade_level', $student->grade_level ?? '');
                $groups_of_grade = $groups->where('grade_level', $selectedGrade);
            @endphp
            @foreach ($groups_of_grade as $group)
                <option value="{{ $group->id }}" {{ old('group_id', $student->group_id ?? '') == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col mb-3">
        <label for="name" class="form-label">اسم الطالب</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="اسم الطالب"
            value="{{ old('name', $student->name ?? '') }}" required>
    </div>
    <div class="col mb-3">
        <label for="phone" class="form-label">تليفون الطالب</label>
        <input type="text" name="phone" id="phone" class="form-control" placeholder="تليفون الطالب"
            value="{{ old('phone', $student->phone ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col mb-3">
        <label for="parent_phone" class="form-label">تليفون ولي الأمر</label>
        <input type="text" name="parent_phone" id="parent_phone" class="form-control" placeholder="تليفون ولي الأمر"
            value="{{ old('parent_phone', $student->parent_phone ?? '') }}" required>
    </div>
    <div class="col mb-3">
        <label for="national_id" class="form-label">الرقم القومي</label>
        <input type="text" name="national_id" id="national_id" class="form-control" placeholder="الرقم القومي"
            value="{{ old('national_id', $student->national_id ?? '') }}" required>
    </div>
</div>

<div class="mb-3">
    <label for="address" class="form-label">العنوان</label>
    <textarea name="address" id="address" class="form-control" placeholder="العنوان" required>{{ old('address', $student->address ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="discount" class="form-label">الخصم</label>
    <input type="text" name="discount" id="discount" class="form-control" placeholder="الخصم"
        value="{{ old('discount', $student->discount ?? '') }}">
</div>

<div class="mb-3">
    <label for="discount_reason" class="form-label">سبب الخصم</label>
    <textarea name="discount_reason" id="discount_reason" class="form-control" placeholder="سبب الخصم">{{ old('discount_reason', $student->discount_reason ?? '') }}</textarea>
</div>
