![image](https://user-images.githubusercontent.com/21690865/143600061-5d5125e3-48cf-4740-9197-e061c1252b29.png)


# جوجو | jojo

**وب‌سروری در ابعاد :baby_chick: برای کارهای کوچک**

## داستان نوشتن جوجو

وب‌سروری که تنظیمات مودم TP-link TD-8811  توی اتاقم رو serve میکنه اسمش micro_httpd بود. از سر بیکاری اسم وب‌سرورش رو سرچ کردم و به این [مخزن](https://github.com/socram8888/micro_httpd) رسیدم. کدش برام جالب بود و به سرم زد که برای مرور کردن و یادگرفتن چیزهای جدید از مهندسی کامپیوتر یک وب‌سرور ساده دیگه شبیه micro_httpd خودم از صفر بنویسم و چیزهایی که یاد میگیرم رو نیز با بقیه به نحوی به اشتراک بگذارم.

## یک وب‌سرور ساده با PHP

در این مخزن سعی میکنم گام‌به‌گام پیش برم و وب‌سروری ساده طبق قواعد  [Hypertext Transfer Protocol -- HTTP/1.1](https://datatracker.ietf.org/doc/html/rfc2616) بسازم. 
از اون‌جایی که من در حال‌حاضر با زبان PHP بیشتر کار میکنم ترجیح دادم ابتدا با PHP 8.0 این کار رو انجام بدم و بعد در زبان دیگری (احتمالا C) بازنویسی کنم.

شاید بپرسید مگه میشه با PHP هم وب‌سرور نوشت؟ جواب بله میباشد. ولی سوال بهتر اینه که آیا PHP برای این کار مناسب میباشد؟ احتمالا جواب بله یا خیر نیست. جواب همچین سوالی برمیگرده به این که دقیقا داریم برای چه کاری سرمایه‌گذاری میکنیم و میخواهیم ابزار ما چه دقت و قدرتی داشته باشد. پس سخت نگیرید. اگر هدف یادگیری باشه، PHP هم برای این کار خوبه.

## چطور این‌ وب‌سرور را راه‌اندازی کنم؟

برای استفاده در محیط داکری کافیه دستور زیر رو وارد کنید:
```
docker run --name jojo --init --rm \
          -v YOUR_WEB_DIR:/jojo \
          -p 80:8000 \
          ohmydevops/jojo-server:v1
```

به جای YOUR_WEB_DIR کافیه آدرس دایرکتوری وبسایت‌تون رو بزارید (ریشه وبسایت). مثلا اگر در دایرکتوری `/home/user/website` وبسایت استاتیک شما قرار دارد کافیه بدین صورت اجرا کنید:
```
docker run --name jojo --init --rm \
          -v /home/user/website:/jojo \
          -p 80:8000 \
          ohmydevops/jojo-server:v1
```
سپس میتونید با مرورگرتون وبسایت خودتون رو مشاهده کنید.

## برنامه‌های پیش‌رو
#### V1
- [x] Serve basic web files  (html, css, js)
- [x] Serve basic static files (images, videos, sounds)
- [x] Support 200 status code
- [x] Support 404 status code
- [x] Support GET method
- [x] Handle requests in blocking-mode
- [x] Dockerise (upload images in docker hub)
- [x] Can config root directory with ENV
#### V2
- [ ] Handle requests in concurrent-mode (multi-process)
#### V3
- [ ] [Common log format](https://en.wikipedia.org/wiki/Common_Log_Format)
#### V4
- [ ] Directory index
