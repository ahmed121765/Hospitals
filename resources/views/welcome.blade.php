@extends('WebSite.layouts.master')

@section('content')
    <style>
        .hospital-home .home-hero { background: linear-gradient(120deg, #f2fbfc 0%, #fff 70%); border-radius: 0 0 28px 28px; }
        .hospital-home .home-card { border: 1px solid #e8f0f2; border-top: 3px solid #16a6a1; transition: transform .2s ease, box-shadow .2s ease; }
        .hospital-home .home-card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(19, 67, 78, .1) !important; }
        .hospital-home .home-section { padding-top: 64px; padding-bottom: 64px; }
        .hospital-home .home-section:nth-of-type(even) { background: #f7fbfc; }
        .hospital-home .home-hero h1 { font-size: clamp(2rem, 4vw, 3.25rem); line-height: 1.35; }
        .hospital-home .home-login-link { margin-inline-start: 12px; }
        @media (max-width: 767px) { .hospital-home .home-section { padding-top: 42px; padding-bottom: 42px; } }
    </style>
    <main class="hospital-home">
    <section class="main-slider-three home-hero">
        <div class="auto-container">
            <div class="row align-items-center py-5">
                <div class="col-lg-6">
                    <div class="sec-title">
                        <h1>{{ app()->getLocale() === 'ar' ? 'رعايتك الصحية تبدأ من هنا' : 'Your health, in caring hands' }}</h1>
                        <div class="separator"></div>
                        <p>{{ app()->getLocale() === 'ar' ? 'اكتشف الأقسام الطبية واحجز موعدك مع الطبيب المناسب.' : 'Explore our medical departments and book an appointment with the right doctor.' }}</p>
                        <a href="#appointment" class="theme-btn btn-style-one"><span class="txt">{{ app()->getLocale() === 'ar' ? 'احجز موعدًا' : 'Book an appointment' }}</span></a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ URL::asset('WebSite/images/main-slider/3.png') }}" alt="{{ app()->getLocale() === 'ar' ? 'الرعاية الطبية' : 'Medical care' }}" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    @if($sections->isNotEmpty())
        <section class="department-section-three home-section py-5" id="departments">
            <div class="auto-container">
                <div class="sec-title centered">
                    <h2>{{ app()->getLocale() === 'ar' ? 'الأقسام الطبية' : 'Medical departments' }}</h2>
                    <div class="separator"></div>
                </div>
                <div class="row">
                    @foreach($sections as $section)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <article class="home-card p-4 h-100 bg-white rounded shadow-sm">
                                <h3>{{ $section->name }}</h3>
                                @if($section->description)
                                    <p>{{ $section->description }}</p>
                                @endif
                                <p>{{ app()->getLocale() === 'ar' ? 'الأطباء:' : 'Doctors:' }} {{ $section->doctors->pluck('name')->filter()->implode('، ') ?: (app()->getLocale() === 'ar' ? 'سيتم الإعلان قريبًا' : 'Coming soon') }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($doctors->isNotEmpty())
        <section class="team-section home-section py-5" id="doctors">
            <div class="auto-container">
                <div class="sec-title centered">
                    <h2>{{ app()->getLocale() === 'ar' ? 'أطباؤنا' : 'Our doctors' }}</h2>
                    <div class="separator"></div>
                </div>
                <div class="row">
                    @foreach($doctors as $doctor)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <article class="home-card p-3 h-100 text-center bg-white rounded shadow-sm">
                                @if($doctor->image)
                                    <img src="{{ asset('Dashboard/img/doctors/' . $doctor->image->filename) }}" alt="{{ $doctor->name }}" class="img-fluid rounded-circle mb-3" style="width:140px;height:140px;object-fit:cover">
                                @endif
                                <h3>{{ $doctor->name }}</h3>
                                @if($doctor->section)<p>{{ $doctor->section->name }}</p>@endif
                                @if($doctor->phone)<a href="tel:{{ $doctor->phone }}">{{ $doctor->phone }}</a>@endif
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($services->isNotEmpty())
        <section class="health-section home-section py-5" id="services">
            <div class="auto-container">
                <div class="sec-title centered">
                    <h2>{{ app()->getLocale() === 'ar' ? 'خدماتنا' : 'Our services' }}</h2>
                    <div class="separator"></div>
                </div>
                <div class="row">
                    @foreach($services as $service)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <article class="home-card p-4 h-100 bg-white rounded shadow-sm">
                                <h3>{{ $service->name }}</h3>
                                @if($service->description)<p>{{ $service->description }}</p>@endif
                                <strong>{{ number_format($service->price, 2) }} {{ app()->getLocale() === 'ar' ? 'جنيه' : 'EGP' }}</strong>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($sections->isNotEmpty() && $doctors->isNotEmpty())
    <section class="appointment-section-two py-5" id="appointment">
        <div class="auto-container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="sec-title">
                        <h2>{{ app()->getLocale() === 'ar' ? 'احجز موعدك' : 'Book an appointment' }}</h2>
                        <div class="separator"></div>
                        <p>{{ app()->getLocale() === 'ar' ? 'أرسل بياناتك وسيتواصل معك فريق المستشفى لتأكيد الموعد.' : 'Send your details and our team will contact you to confirm your appointment.' }}</p>
                    </div>
                </div>
                <div class="col-lg-7 appointment-form">
                    <livewire:appointments.create />
                </div>
            </div>
        </div>
    </section>
    @endif
    </main>
@endsection
