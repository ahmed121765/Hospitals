<li><a href="{{ url('/') }}">{{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
<li><a href="{{ url('/') }}#departments">{{ app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments' }}</a></li>
<li><a href="{{ url('/') }}#doctors">{{ app()->getLocale() === 'ar' ? 'الأطباء' : 'Doctors' }}</a></li>
<li><a href="{{ url('/') }}#services">{{ app()->getLocale() === 'ar' ? 'الخدمات' : 'Services' }}</a></li>
<li><a href="{{ url('/') }}#appointment">{{ app()->getLocale() === 'ar' ? 'حجز موعد' : 'Book appointment' }}</a></li>
<li class="login-menu-item"><a href="{{ route('login') }}">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Log in' }}</a></li>
