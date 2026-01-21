<div id="content">
  <!-- Header -->
  <header class="app-header sticky" id="header">
    <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap">
      <!-- Search Box -->
      <div class="d-flex align-items-center" style="width: 300px;">
        <div class="search-box position-relative w-100">
          <input
            type="text"
            class="form-control ps-5"
            placeholder="بحث..."
            aria-label="بحث"
          />
          <i
            class="fas fa-search position-absolute"
            style="left: 15px; top: 50%; transform: translateY(-50%); color: #888;"
          ></i>
        </div>
      </div>

      <!-- Profile Dropdown -->
      <div class="d-flex align-items-center">
        <div class="dropdown">
          <button class="btn btn-danger dropdown-toggle d-flex align-items-center my-2" type="button"
                  id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle ms-2"></i> {{ auth()->user()->name ?? 'Admin' }}
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li> 
              <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="fas fa-user me-2"></i>الملف الشخصي
              </a>
            </li>
          
            <li><hr class="dropdown-divider" /></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item">
                  <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>

   
  </header>



<style>
/* Header Styling */
.dashboard-header {
  padding: 1rem 1.5rem;
  background: #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Search Box */
.search-box input {
  border-radius: 25px;
  padding-right: 1rem;
  padding-left: 2.5rem;
}

/* Sidebar */
#sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 250px;
  height: 100vh;
  background: #fff;
  box-shadow: -2px 0 8px rgba(0,0,0,0.1);
  z-index: 1050;
  overflow-y: auto;
  transition: right 0.3s ease;
}

/* Sidebar Backdrop */
.sidebar-backdrop {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 1049;
}

.sidebar-backdrop.active {
  display: block;
}

/* Responsive - Mobile */
@media (max-width: 768px) {
  #sidebar {
    right: -250px;
  }
  
  #sidebar.active {
    right: 0;
  }
}

/* Desktop - Sidebar دايماً ظاهر */
@media (min-width: 769px) {
  #sidebar {
    right: 0;
  }
  
  #content {
    margin-right: 250px;
  }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");
  const backdrop = document.getElementById("sidebarBackdrop");

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("active");
      backdrop.classList.toggle("active");
    });
  }

  if (backdrop) {
    backdrop.addEventListener("click", function () {
      sidebar.classList.remove("active");
      backdrop.classList.remove("active");
    });
  }
});
</script>