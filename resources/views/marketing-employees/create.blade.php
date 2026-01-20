@extends('layout.app')

@section('title', 'إضافة موظف تسويق')

@section('content')
<div class="geex-content">
  <div class="geex-content__header">
    <div class="geex-content__header__content">
      <h2 class="geex-content__header__title">إضافة موظف جديد</h2>
      <p class="geex-content__header__subtitle">أنشئ حسابًا لموظف تسويق جديد</p>
      <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb breadcrumb-transparent mb-0">
          @foreach($breadcrumbs as $crumb)
            @if(!$loop->last)
              <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a></li>
            @else
              <li class="breadcrumb-item active" aria-current="page">{{ $crumb['name'] }}</li>
            @endif
          @endforeach
        </ol>
      </nav>
    </div>
  </div>

  <div class="geex-content__section geex-content__form">
    <form action="{{ route('marketing-employees.store') }}" method="POST" class="geex-content__form__wrapper">
      @csrf
      <div class="geex-content__form__single mb-3">
        <label class="input-label">الاسم</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="geex-content__form__single mb-3">
        <label class="input-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="geex-content__form__single mb-3">
        <label class="input-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="geex-content__form__single mb-3">
        <label class="input-label">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">
        <i class="uil-save me-1"></i> حفظ
      </button>
      <a href="{{ route('marketing-employees.index') }}" class="btn btn-secondary ms-2">إلغاء</a>
    </form>
  </div>
</div>
@endsection
