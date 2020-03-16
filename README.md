# Think-Blade
Blade template engine with thinkphp 5. (component & slot support)

# Installation
composer require luoyy/think-blade

view.php:

```php
'template' => [
        // 模板引擎类型使用Blade
        'type'            => 'Blade',
        // 模板目录名
        'view_dir_name'   => 'view',
        // 模板起始路径 不设置则自动寻找
        'view_path'       => '',
        // 模板后缀
        'view_suffix'     => 'blade.php',
        // 扩展的模板文件名
        'view_ext_suffix' => ['php', 'css', 'html'],
        // 模板文件名分隔符
        'view_depr'       => DIRECTORY_SEPARATOR,
        // 模板缓存路径，不设置则在runtime/temp下
        'cache_path'      => ''
    ],
```

# Usage
```html
<header id="navbar">
	<div class="row navbar-inner">
		<div class="col-xs-6 brand-block">
			<h4><a href="{{ url('/admin') }}"><img src="/assets/admin/images/logo.png"></a> · 管理后台
			</h4>
			<a href="javascript:;" class="cd_nav_trigger"><span></span></a>
		</div>
		<div class="col-xs-6 text-right user-block">
			你好，{{ $manage_user->nickname }}({{ $manage_user->username }})
			<span class="gap-line"></span>
			<a href="{{ url('/manage/index/account') }}" class="item">修改资料</a>
			<span class="gap-line"></span>
			<a href="{{ url('/manage/start/logout') }}" class="confirm item" title="确认要退出吗？">退出</a>
		</div>
        {!! $Foo->function() !!}
        @switch($test)
        @case(1)
        1
        @case(2)
        2
        @default
        2
        @endswitch
	</div>
</header>
```

# DOC

https://laravel.com/docs/7.x/blade

https://learnku.com/docs/laravel/7.x/blade/7470 (中文)
