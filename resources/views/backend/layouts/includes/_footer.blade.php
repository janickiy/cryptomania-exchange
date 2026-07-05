<footer class="app-footer">
    Copyright &copy; {{ date("Y",strtotime("-1 year")).'-'.date('Y') }} <a href="{{ url('/') }}">{{ env('APP_NAME','Cryptomania') }}</a>. All rights reserved.
</footer>

</div>
@include('errors.flash_message')
<script src="{{ asset('js/app.js') }}?t={{ random_string() }}"></script>
<script src="{{ asset('common/vendors/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('common/vendors/iCheck/icheck.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/js/adminlte.min.js"></script>
@yield('extraScript')
<script src="{{ asset('backend/assets/js/custom.js') }}"></script>
@yield('script')
</body>
</html>
