## جوجو | jojo

جوجو، وب‌سروری در ابعاد جوجه برای کارهای کوچک

## داستان نوشتن جوجو

وب‌سروری که تنظیمات مودم TP-link TD-8811  توی اتاقم رو serve میکنه اسمش micro_httpd بود. از سر بیکاری اسم وب‌سرورش رو سرچ کردم و به این [مخزن](https://github.com/socram8888/micro_httpd) رسیدم. کدش برام جالب بود و به سرم زد که برای مرور کردن و یادگرفتن چیزهای جدید از مهندسی کامپیوتر یک وب‌سرور ساده دیگه شبیه micro_httpd خودم از صفر بنویسم و چیزهایی که یاد میگیرم رو نیز با بقیه به نحوی به اشتراک بگذارم.

## یک وب‌سرور ساده با PHP

در این مخزن سعی میکنم گام‌به‌گام پیش برم و وب‌سروری ساده طبق قواعد  [Hypertext Transfer Protocol -- HTTP/1.1](https://datatracker.ietf.org/doc/html/rfc2616) بسازم. 
از اون‌جایی که من در حال‌حاضر با زبان PHP بیشتر کار میکنم ترجیح دادم ابتدا با PHP 8.0 این کار رو انجام بدم و بعد در زبان دیگری (احتمالا C) بازنویسی کنم.

شاید بپرسید مگه میشه با PHP هم وب‌سرور نوشت؟ جواب بله میباشد. ولی سوال بهتر اینه که آیا PHP برای این کار مناسب میباشد؟ احتمالا جواب بله یا خیر نیست. جواب همچین سوالی برمیگرده به این که دقیقا داریم برای چه کاری سرمایه‌گذاری میکنیم و میخواهیم ابزار ما چه دقت و قدرتی داشته باشد. پس سخت نگیرید. اگر هدف یادگیری باشه، PHP هم برای این کار خوبه.

## برنامه‌های پیش‌رو
#### V1
- [x] Serve basic files  (html, css, js)
- [x] Support 200 status code
- [x] Support 404 status code
- [x] Support GET method
- [x] Handle requests in blocking-mode
- [x] Dockerise (upload images in docker hub)
- [x] Can config root directory with ENV
#### V2
- [ ] Handle requests in concurrent-mode (multi-process)
- [ ] Profiling configuration
#### V3
- [ ] [Common log format](https://en.wikipedia.org/wiki/Common_Log_Format)
