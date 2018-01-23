# Think-Blade
Blade template engine with thinkphp 5. (component & slot support)

# Installation
composer require luoyy/think-blade

config.php:

```php
'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Blade',
        // 视图基础目录（集中式）
        'view_base' => '',
        // 模板起始路径
        'view_path' => '',
        // 模板文件名分隔符
        'view_depr' => DIRECTORY_SEPARATOR,
        // 模板缓存目录
        'view_cache_path' => RUNTIME_PATH . 'temp' . DIRECTORY_SEPARATOR,
        // 模板文件后缀
        'view_suffix' => 'blade.php',
        'cache' => [
            'cache_subdir' => false,
            'prefix' => '',
        ],
    ],
```

### tp5.1

- use ^3.0

template.php

```php
return [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Blade',
        // 视图基础目录（集中式）
        'view_base' => '',
        // 模板起始路径
        'view_path' => '',
        // 模板文件名分隔符
        'view_depr' => DIRECTORY_SEPARATOR,
        // 模板缓存目录
        'view_cache_path' => Env::get('RUNTIME_PATH') . 'temp' . DIRECTORY_SEPARATOR,
        // 模板文件后缀
        'view_suffix' => 'blade.php',
        'cache' => [
            'cache_subdir' => false,
            'prefix' => '',
        ],
    ];
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
            @brank
            @case(2)
            2
            @brank
            @default
            2
            @brank
        @endswitch
	</div>
</header>
```

# DOC

https://laravel.com/docs/5.5/blade

http://d.laravel-china.org/docs/5.5/blade (中文)
