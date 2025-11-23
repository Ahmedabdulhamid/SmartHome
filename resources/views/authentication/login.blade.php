<!DOCTYPE html>
<html lang="en">

@section('title', __('web.login'))

<head>
    @include('users_layout.head')


    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Creative Form</title>

    <style>
        :root {
            --color-primary: #5a5c9a;
            --color-secondary: #00bcd4;
            --color-bg-light: #f5f7fa;
            --color-text-dark: #333;
            --color-text-light: #999;
            --color-google: #db4437;
            --color-facebook: #4267b2;
        }

        body {
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--color-bg-light);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .index-page {
            direction: @if (app()->getLocale() == 'ar')
                rtl
            @else
                ltr
            @endif
            ;
        }

        main.main {
            width: 100%;
            padding: 40px 0;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        header,
        footer {
            width: 100%;
            max-width: 100%;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .contact.section {
            width: 100%;
        }

        /*
* --------------------------------
* 2. FORM CARD & INPUT STYLING
* --------------------------------
*/

        .col-lg-8 {
            max-width: 550px;
            width: 100%;
            margin: auto;
        }

        .php-email-form1 {
            background: #ffffff;
            padding: 50px 60px;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* تعديل المسافة السفلية للحقول المتأثرة بكود الأخطاء */
        .form-group-custom {
            margin-bottom: 25px;
        }

        /* تنسيق حاوية خيارات تسجيل الدخول (مثل تذكرني ونسيت كلمة المرور) */
        /* تم إضافة هذا القسم للتحكم في موضع الرابط */
        .login-options {
            display: flex;
            justify-content: flex-end;
            /* لجعله يظهر في نهاية السطر (اليسار لـ LTR واليمين لـ RTL) */
            /* سحب الحاوية للأعلى لتبدو جزءًا من مجموعة حقل كلمة المرور */
            margin-top: -10px;
            margin-bottom: 30px;
        }

        /* تنسيق رابط نسيت كلمة المرور */
        .forgot-password-link {
            color: var(--color-primary);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease, text-decoration 0.2s ease;
        }

        .forgot-password-link:hover {
            color: var(--color-secondary);
            text-decoration: underline;
        }

        .form-group-custom input {
            width: 100%;
            padding: 12px 15px;
            font-size: 17px;
            color: var(--color-text-dark);
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group-custom input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(90, 92, 154, 0.2), 0 0 0 3px rgba(0, 188, 212, 0.1);
        }

        .form-group-custom input::placeholder {
            color: var(--color-text-light);
            font-weight: 300;
        }

        /* تصميم رسالة الخطأ */
        .input-error {
            color: #e3342f;
            /* لون أحمر */
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        /*
* --------------------------------
* 3. BUTTONS STYLING
* --------------------------------
*/

        .col-md-12.text-center {
            /* تم تعديل المارجن ليصبح 30px بعد نقل رابط نسيت كلمة المرور */
            margin-top: 30px;
        }

        .social-signup-divider {
            text-align: center;
            margin: 35px 0 25px;
            color: #ccc;
            position: relative;
        }

        .social-signup-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid #eee;
            z-index: 1;
        }

        .social-signup-divider span {
            background: #ffffff;
            padding: 0 10px;
            position: relative;
            z-index: 2;
            color: var(--color-text-light);
            font-size: 14px;
        }

        /* زر Sign Up الرئيسي */
        .php-email-form1 button[type="submit"] {
            display: inline-block;
            width: 100%;
            padding: 16px 30px;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 19px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background-color: #3b3f5c;
            transition: all 0.3s ease;
        }

        .php-email-form1 button[type="submit"]:hover {
            background-color: var(--color-primary);
            box-shadow: 0 10px 30px rgba(90, 92, 154, 0.5);
            transform: translateY(-2px);
        }

        /* حاوية الأزرار الاجتماعية (افتراضياً: صف أفقي) */
        .social-buttons-container {
            display: flex;
            gap: 15px;
        }

        .social-button {
            flex: 1;
            padding: 14px 10px;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-button i {
            margin-inline-end: 10px;
            font-size: 20px;
        }

        .btn-google {
            background-color: var(--color-google);
        }

        .btn-google:hover {
            opacity: 0.9;
            box-shadow: 0 5px 15px rgba(219, 68, 55, 0.4);
            transform: translateY(-1px);
        }

        .btn-facebook {
            background-color: var(--color-facebook);
        }

        .btn-facebook:hover {
            opacity: 0.9;
            box-shadow: 0 5px 15px rgba(66, 103, 178, 0.4);
            transform: translateY(-1px);
        }

        /* --------------------------------- */
        /* ✅ ميديا كويري لتحسين عرض الجوال (Mobile View) */
        /* --------------------------------- */
        @media (max-width: 768px) {

            .social-buttons-container {
                flex-direction: column;
                gap: 10px;
            }

            .php-email-form1 {
                padding: 40px 30px;
            }

            .col-lg-8 {
                max-width: 95%;
            }
        }
    </style>
</head>

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <div class="container">
            <div class="row gy-4">
                <section id="contact" class="contact section">
                    <div class="col-lg-8 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="100">

                        <form class="php-email-form1" method='POST'id="demo-form" action="{{ route('login.store') }}">
                            @csrf


                            <div class="row gy-4">


                                <div class="form-group-custom">
                                    <input type="email" class="form-control" placeholder="{{ __('web.your_email') }}"
                                        name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror

                                </div>

                                {{-- حقل كلمة المرور --}}
                                <div class="form-group-custom">
                                    <input type="password" class="form-control" placeholder="{{ __('web.password') }}"
                                        name="password">
                                    @error('password')
                                        <span class="input-error">{{ $message }}</span>
                                    @enderror

                                </div>

                                {{-- رابط نسيت كلمة المرور (جديد) --}}
                                <div class="col-md-12 login-options">
                                    {{-- يجب تغيير route('password.request') إلى المسار الفعلي لإعادة تعيين كلمة المرور لديك --}}
                                    <a href="{{ route('password.request') }}" class="forgot-password-link">
                                        {{ __('web.forgot_your_password') }}
                                    </a>
                                </div>

                                {{-- زر الإرسال الرئيسي --}}
                                <div class="col-md-12 text-center">
                                    <button type="submit"class="g-recaptcha"
                                        data-sitekey="6LdEp_QrAAAAAOl7Ex1XOgvEhQXDV_zRkVsCRaBe" data-callback='onSubmit'
                                        data-action='submit'>Login</button>
                                </div>

                                {{-- فاصل الأزرار الاجتماعية --}}
                                <div class="col-md-12 social-signup-divider">
                                    <span>OR</span>
                                </div>

                                {{-- أزرار التواصل الاجتماعي --}}
                                <div class="col-md-12 social-buttons-container">
                                    {{-- زر Google --}}
                                    <a type="button" class="social-button btn-google text-white" href="{{ route('auth.social.login', 'google') }}">
                                        <i class="bi bi-google"></i> Login with
                                        Google
                                    </a>

                                    {{-- زر Facebook --}}
                                    <a type="button" class="social-button btn-facebook text-white" href="{{ route('auth.social.login', 'facebook') }}">
                                        <i class="bi bi-facebook"></i>Login with
                                        Facebook
                                    </a>
                                </div>


                            </div>
                            <script>
                                function onSubmit(token) {
                                    document.getElementById("demo-form").submit();
                                }
                            </script>
                        </form>
                    </div>
                </section>
            </div>

        </div>
    </main>


    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')

</body>

</html>
