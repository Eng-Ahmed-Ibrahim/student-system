<div class="row">
    <div class="col mb-3">
        <label for="name" class="form-label">اسم الطالب</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="اسم الطالب"
            value="{{ old('name', $student->name ?? '') }}" required>
    </div>
    <div class="col mb-3">
        <label for="phone" class="form-label">تليفون الطالب</label>
        <input type="text" name="phone" id="phone" class="form-control" placeholder="تليفون الطالب"
            value="{{ old('phone', $student->phone ?? '') }}" pattern="\d{11}" maxlength="11" minlength="11"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="رقم الهاتف يجب أن يتكون من 11 رقم" required>
    </div>
</div>

<div class="col mb-3">
    <label for="parent_phone" class="form-label">تليفون ولي الأمر</label>
    <input type="text" name="parent_phone" id="parent_phone" class="form-control" placeholder="تليفون ولي الأمر"
        value="{{ old('parent_phone', $student->parent_phone ?? '') }}" pattern="\d{11}" maxlength="11" minlength="11"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="رقم الهاتف يجب أن يتكون من 11 رقم" required>
</div>

<div class="col mb-3">
    <label for="national_id" class="form-label">الرقم القومي</label>
    <input type="text" name="national_id" id="national_id" class="form-control" placeholder="الرقم القومي"
        value="{{ old('national_id', $student->national_id ?? '') }}" pattern="\d{14}" maxlength="14" minlength="14"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="رقم قومي يجب أن يتكون من 14 رقم" required>
</div>

<div class="row">
    <div class="mb-3 col">
        <label for="grade_level" class="form-label">الصف الدراسي</label>
        <select name="grade_level" id="grade_level" class="form-select" required>
            <option value="" disabled
                {{ old('grade_level', $student->grade_level ?? '') == '' ? 'selected' : '' }}>اختر الصف الدراسي</option>
            <option value="1" {{ old('grade_level', $student->grade_level ?? '') == 1 ? 'selected' : '' }}>الصف
                الأول الثانوي</option>
            <option value="2" {{ old('grade_level', $student->grade_level ?? '') == 2 ? 'selected' : '' }}>الصف
                الثاني الثانوي</option>
            <option value="3" {{ old('grade_level', $student->grade_level ?? '') == 3 ? 'selected' : '' }}>الصف
                الثالث الثانوي</option>
        </select>
    </div>

    <div class="mb-3 col">
        <label for="group_id" class="form-label">المجموعة</label>
        <select name="group_id" id="group_id" class="form-select" required>
            <option value="" disabled {{ old('group_id', $student->group_id ?? '') == '' ? 'selected' : '' }}>اختر
                المجموعة</option>
            @php
                $selectedGrade = old('grade_level', $student->grade_level ?? '');
                $groups_of_grade = $groups->where('grade_level', $selectedGrade);
            @endphp
            @foreach ($groups_of_grade as $group)
                <option value="{{ $group->id }}"
                    {{ old('group_id', $student->group_id ?? '') == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="address" class="form-label">العنوان</label>
    <textarea name="address" id="address" class="form-control" placeholder="العنوان" required>{{ old('address', $student->address ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="discount" class="form-label">الخصم</label>
    <input type="number" name="discount" id="discount" class="form-control" placeholder="الخصم" min="0"
        max="100" value="{{ old('discount', $student->discount ?? '') }}"
        oninput="this.value = this.value.replace(/[^0-9]/g, ''); 
                                                        if (this.value > 100) this.value = 100; 
                                                        if (this.value < 0) this.value = 0;">
</div>

<div class="mb-3">
    <label for="discount_reason" class="form-label">سبب الخصم</label>
    <textarea name="discount_reason" id="discount_reason" class="form-control" placeholder="سبب الخصم">{{ old('discount_reason', $student->discount_reason ?? '') }}</textarea>
</div>

    <script>
        // كل بيانات الجروبات من السيرفر بصيغة JSON
        const allGroups = @json($groups);

        // عناصر الـ select
        const gradeSelects = document.querySelectorAll('select[name="grade_level"]');
        const groupSelects = document.querySelectorAll('select[name="group_id"]');

        // لما المستخدم يغير الصف
        gradeSelects.forEach((gradeSelect, index) => {
            gradeSelect.addEventListener('change', function() {
                const selectedGrade = this.value;

                const filteredGroups = allGroups.filter(group => group.grade_level == selectedGrade);

                // حدث قائمة الجروبات المقابلة لهذا الصف
                const groupSelect = groupSelects[index];
                groupSelect.innerHTML = `<option value="" disabled selected>اختر المجموعة</option>`;

                filteredGroups.forEach(group => {
                    groupSelect.innerHTML += `<option value="${group.id}">${group.name}</option>`;
                });
            });
        });
    </script>