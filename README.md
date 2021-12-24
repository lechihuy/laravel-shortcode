# Laravel Shortcode
Hỗ trợ Shortcode trong Laravel

## Cài đặt cho nhà phát triển

Tại thư mục gốc của dự án Laravel, chạy dòng lệnh bên dưới để clone gói này.

```sh
git clone https://github.com/lechihuy/laravel-shortcode.git
```

Tiếp đến, bạn có thể sử dụng câu lệnh CLI sau để thêm repository vào file `composer.json`:

```sh
composer config repositories."laravel-shortcode" '{"type": "path", "url": "./laravel-shortcode"}' --file composer.json
```

Thêm gói `lechihuy/laravel-shortcode` trong mục `require` của file `composer.json` :

```json
"require": {
    "lechihuy/laravel-shortcode": "@dev"
},
```

Sau đó chạy lệnh bên dưới để cập nhật `composer.json`

```
composer update
```

### Hướng dẫn
#### Đăng ký shortcode
Để đăng ký một shortcode, ta có thể thực hiện tại Service Provider, thường là `AppServiceProvider` trong method `boot`

```php
use Shortcode\Facades\Shortcode;

Shortcode::register('b', function($shortcode) {
    return "<b>{$shortcode->content}</b>";
});
```

`$shortcode` là một instance từ `Shortcode\Compilers\Shortcode`. Ta có thể lấy một số thông tin như tên shortcode, nội dung.

```php
$shortcode->name
$shortcode->content
```

Ngoài ra, ta có thể truy cập các thuộc tính thông qua `attributes` được khởi tạo từ `Illuminate\View\ComponentAttributeBag`, tham khảo thêm [tại đây](https://laravel.com/api/8.x/Illuminate/View/ComponentAttributeBag.html)

```php
$shortcode->attributes->toHtml()
$shortcode->attributes->get('src')
```

Ta có thể lấy chuỗi thuộc tính gốc được khai báo trong shortcode

```php
$shortcode->rawAttributes
```

#### Compile
Để compile một nội dung có định dạng shortcode sang HTML, ta làm như sau

```php
use Shortcode\Facades\Shortcode;

Shortcode::compile("[b class=\"font-bold\"]Strong[/b]"); // <b class="font-bold">Strong</b>
```