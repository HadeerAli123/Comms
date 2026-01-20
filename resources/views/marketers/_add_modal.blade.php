{{-- resources/views/marketers/_add_modal.blade.php --}}
<div class="modal fade" id="addMarketerModal" tabindex="-1" aria-labelledby="addMarketerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content geex-content__form">
      <form action="{{ route('marketers.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addMarketerModalLabel">إضافة مسوّق جديد</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
        <div class="geex-content__form__single">
            <label for="geex-input-name" class="input-label">اسم المسوّق</label>
            <br/>
            <br/>
            <div class="geex-content__form__single__box">
            <div class="input-wrapper">
                <input id="geex-input-name" type="text" name="name" placeholder="أدخل اسم المسوّق" class="form-control" required>
            </div>
            </div>
        </div>
        <div class="geex-content__form__single">
            <label for="geex-input-phone" class="input-label">رقم الهاتف</label>
            <br/>
            <br/>
            <div class="geex-content__form__single__box">
            <div class="input-wrapper">
                <input id="geex-input-phone" type="text" name="phone" placeholder="أدخل رقم الهاتف" class="form-control" required>
            </div>
            </div>
        </div>
        <div class="geex-content__form__single">
            <label for="geex-input-employee" class="input-label">الموظف</label>
            <br/>
            <br/>
            <div class="geex-content__form__single__box">
            <div class="input-wrapper">
                <select id="geex-input-employee" name="employee_id" class="form-control" required>
                @foreach($employees as $id => $emp)
                    <option value="{{ $id }}">{{ $emp }}</option>
                @endforeach
                </select>
            </div>
            </div>
        </div>
        <div class="geex-content__form__single">
            <label for="geex-input-location" class="input-label">الموقع</label>
            <br/>
            <br/>
            <div class="geex-content__form__single__box">
            <div class="input-wrapper">
                <select id="geex-input-location" name="location_id" class="form-control" required>
                @foreach($locations as $id => $location)
                    <option value="{{ $id }}">{{ $location }}</option>
                @endforeach
                </select>
            </div>
            </div>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>
