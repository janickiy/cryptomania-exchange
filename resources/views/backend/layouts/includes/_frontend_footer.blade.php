<footer class="main-footer">
    <div class="container-fluid">
        <strong>Copyright &copy; {{ date("Y", strtotime("-1 year")).'-'.date('Y') }}
            <a href="{{ url('/') }}">{{ env('APP_NAME', 'Cryptomania') }}</a>.
        </strong>
        All rights reserved.
    </div>
</footer>

</div>
@include('errors.flash_message')
<script src="{{ asset('common/vendors/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('common/vendors/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('common/vendors/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('common/vendors/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/adminlte.min.js') }}"></script>
@yield('extraScript')
<script src="{{ asset('backend/assets/js/custom.js') }}"></script>
<script src="{{ asset('backend/assets/js/laravel.js') }}"></script>
@yield('script')
</body>
</html>
