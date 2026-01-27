<nav id="sidebar">
  <div class="sidebar-header">
    <img src="{{ asset('assets/images/new-logos/logo.png') }}" alt="logo">
  </div>
  <ul class="list-unstyled components">
    <li>
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span class="nav-link-text">الرئيسية</span>
      </a>
    </li>
    <li>
      <a href="{{ route('sites.index') }}" class="{{ request()->routeIs('sites.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-link-text">مواقع التسويق</span>
      </a>
    </li>
    <li>
      <a href="{{ route('marketers.index') }}" class="{{ request()->routeIs('marketers.*') ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i>
        <span class="nav-link-text">المسوقين</span>
      </a>
    </li>
    <li>
      <a href="{{ route('influencers.index') }}" class="{{ request()->routeIs('influencers.*') ? 'active' : '' }}">
        <i class="fas fa-lightbulb"></i>
        <span class="nav-link-text">مشاهير الاعلانات</span>
      </a>
    </li>
    <li>
      <a href="{{ route('visits.index') }}" class="{{ request()->routeIs('visits.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span class="nav-link-text">زيارات المشاهير</span>
      </a>
    </li>
    <li>
      <a href="{{ route('commissions.client_visits') }}" class="{{ request()->routeIs('commissions.client_visits') ? 'active' : '' }}">
        <i class="fas fa-industry"></i>
        <span class="nav-link-text">زيارات العملاء</span>
      </a>
    </li>
    <li>
      <a href="{{ route('commissions.index') }}" class="{{ request()->routeIs('commissions.*') && !request()->routeIs('commissions.client_visits') ? 'active' : '' }}">
        <i class="fas fa-gift"></i>
        <span class="nav-link-text">عمولات</span>
      </a>
    </li>
    <li>
      <a href="{{ route('marketing-employees.index') }}" class="{{ request()->routeIs('marketing-employees.*') ? 'active' : '' }}">
        <i class="fas fa-user-friends"></i>
        <span class="nav-link-text">موظفين التسويق</span>
      </a>
    </li>
    <li>
      <a href="{{ route('main-statement.index') }}" class="{{ request()->routeIs('main-statement.*') ? 'active' : '' }}">
        <i class="fas fa-calculator"></i>
        <span class="nav-link-text">كشف حساب رئيسي</span>
      </a>
    </li>
  </ul>
</nav>
<header class="app-header sticky" id="header">
  <div class="main-header-container container-fluid d-flex justify-content-between align-items-center">
    <button class="btn btn-primary d-md-none" id="toggleSidebar">
      ☰ القائمة
    </button>
    @include('partials.profile')
  </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");

  let backdrop = document.createElement("div");
  backdrop.className = "sidebar-backdrop";
  document.body.appendChild(backdrop);

  toggleBtn.addEventListener("click", function () {
    sidebar.classList.toggle("active");
    backdrop.classList.toggle("active");
  });

  backdrop.addEventListener("click", function () {
    sidebar.classList.remove("active");
    backdrop.classList.remove("active");
  });
});
</script>
