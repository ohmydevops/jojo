## جوجو | jojo

جوجو، وب‌سروری در ابعاد جوجه برای کارهای کوچک

## داستان نوشتن جوجو

وب‌سروری که تنظیمات مودم TP-link TD-8811  توی اتاقم رو serve میکنه اسمش micro_httpd بود. از سر بیکاری اسم وب‌سرورش رو سرچ کردم و به این [مخزن](https://github.com/socram8888/micro_httpd) رسیدم. کدش برام جالب بود و به سرم زد که برای مرور کردن و یادگرفتن چیزهای جدید از مهندسی کامپیوتر یک وب‌سرور ساده دیگه شبیه micro_httpd خودم از صفر بنویسم و چیزهایی که یاد میگیرم رو در قالب یک ‌سری محتوای ویدیویی یا متنی منتشر کنم.  همین! من هر روز حدود ۱.۵ ساعت برای این موضوع وقت میزارم و امیدوارم هر وقت به نتیجه خوبی رسیدم شروع کنم به تولید محتوا و اشتراک‌گذاری آنچه که یادم گرفتم.

## ساخت یک وب‌سرور ساده با PHP

در این مخزن سعی میکنم مرحله به مرحله پیش برم و یک وب‌سرور ساده طبق قواعد  [Hypertext Transfer Protocol -- HTTP/1.1](https://datatracker.ietf.org/doc/html/rfc2616) بسازم. 
در این مسیر با مفاهیم TCP/IP و سیستم‌عامل بیشتر آشنا خواهم شد و سعی میکنم هرآنچه که در این مسیر یاد میگیرم رو به نحو خاصی با دیگران به اشتراک بگزارم. 
از اون‌جایی که من در حال‌حاضر با زبان PHP بیشتر کار میکنم ترجیح دادم ابتدا با PHP 8.0 این کار رو انجام بدم و بعد در C بازنویسی کنم.  از طریق بخش Issue ها میتونید کارهایی در حال پیاده‌سازی یا مطالعه‌شون هستم رو مشاهده کنید.

### مطالب بخش‌های زیر دائما در حال به‌روزشدن میباشد. 

## کلیات دانشی که در ساخت یک وب‌سرور خیلی ساده باید بلد باشیم:

- شبکه
- سیستم‌عامل
- برنامه‌نویسی در لایه سوکت
- مطالعه RFC های مربوط به پروتکل HTTP (شناخت پروتکل)


## لیستی از مقالات و ویدیو‌های خوبی که میتونه درباره وب‌سرورها مفید باشه:

- [وب‌سرور Nginx چطور کار میکند؟](https://www.nginx.com/blog/inside-nginx-how-we-designed-for-performance-scale)
- [وب‌سرور Apache چطور کار میکند؟](https://httpd.apache.org/docs/2.4/mpm.html )
- [داستان معماری Nginx](https://www.aosabook.org/en/nginx.html)
- [چالش C10k در دنیای وب‌سرورها](http://www.kegel.com/c10k.html)
