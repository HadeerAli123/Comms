<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-width="fullwidth" data-menu-styles="light" data-toggled="close">

@include('partials.head')

<body>

    <!-- Start Switcher -->
   @include('partials.switcher')
    <!-- End Switcher -->


    <!-- Loader -->
    <div id="loader" >
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
    </div>
    <!-- Loader -->

    <div class="page">
        <!-- app-header -->
       @include('partials.header')
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
     @include('partials.sidebar')
        <!-- End::app-sidebar -->


   @yield('content')

        <!-- Footer -->
    @include('partials.footer')

    </div>


    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow lh-1"><i class="ti ti-arrow-big-up fs-16"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

   @include('partials.scripts')

    <!-- End::app-content -->
</body>
<script>
    if (!localStorage.getItem("zynixrtl") && !localStorage.getItem("zynixltr")) {
       let rtlVar = rtlBtn.addEventListener('click', () => {
        localStorage.setItem("zynixrtl", true);
        localStorage.removeItem("zynixltr");
        rtlFn();
        if (document.querySelector(".noUi-target")) {
            console.log("working");
            document.querySelectorAll(".noUi-origin").forEach((e) => {
                e.classList.add("transform-none");
            });
        }
    });
}
</script>
</html>
