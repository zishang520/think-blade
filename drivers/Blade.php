<?php

namespace think\view\driver;

use luoyy\Blade\Compilers\BladeCompiler;
use luoyy\Blade\Contracts\Filesystem\FileNotFoundException;
use luoyy\Blade\Engines\CompilerEngine;
use luoyy\Blade\Engines\EngineResolver;
use luoyy\Blade\Factory;
use luoyy\Blade\Filesystem\Filesystem;
use luoyy\Blade\Engines\FileEngine;
use luoyy\Blade\Engines\PhpEngine;
use luoyy\Blade\FileViewFinder;
use think\App;
use think\contract\TemplateHandlerInterface;
use think\helper\Str;

class Blade implements TemplateHandlerInterface
{
    // 模板引擎实例
    private $template;
    private $app;

    // 模板引擎参数
    protected $config = [
        // 模板目录名
        'view_dir_name'   => 'view',
        // 模板起始路径
        'view_path'       => '',
        // 模板后缀
        'view_suffix'     => 'blade.php',
        // 扩展的模板文件名
        'view_ext_suffix' => ['php', 'css', 'html'],
        // 模板文件名分隔符
        'view_depr'       => DIRECTORY_SEPARATOR,
        // 模板缓存路径，不设置则在runtime/temp下
        'cache_path'      => ''
    ];

    public function __construct(App $app, array $config = [])
    {
        $this->app    = $app;
        $this->config = array_merge($this->config, (array) $config);
    }

    private function _boot()
    {
        if (empty($this->config['cache_path'])) {
            $this->config['cache_path'] = $this->app->getRuntimePath() . 'temp' . DIRECTORY_SEPARATOR;
        }

        if (!is_dir($this->config['cache_path'])) {
            mkdir($this->config['cache_path'], 0755, true);
        }

        $file = new Filesystem;

        $compiler = new BladeCompiler($file, $this->config['cache_path']);

        $resolver = new EngineResolver;
        $resolver->register('file', function () {
            return new FileEngine;
        });
        $resolver->register('php', function () {
            return new PhpEngine;
        });
        $resolver->register('blade', function () use ($compiler) {
            return new CompilerEngine($compiler);
        });

        $finder = new FileViewFinder($file, [$this->app->getRootPath() . $this->config['view_dir_name'] . DIRECTORY_SEPARATOR], [$this->config['view_suffix']] + $this->config['view_ext_suffix']);

        $this->template = new Factory($resolver, $finder);
    }

    /**
     * 检测是否存在模板文件
     * @access public
     * @param string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists(string $template): bool
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }

        return is_file($template);
    }

    /**
     * 渲染模板文件
     * @access public
     * @param string $template 模板文件
     * @param array  $data     模板变量
     * @return void
     */
    public function fetch(string $template, array $data = []): void
    {
        if (empty($this->config['view_path'])) {
            $view = $this->config['view_dir_name'];

            if (is_dir($this->app->getAppPath() . $view)) {
                $path = $this->app->getAppPath() . $view . DIRECTORY_SEPARATOR;
            } else {
                $appName = $this->app->http->getName();
                $path    = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . ($appName ? $appName . DIRECTORY_SEPARATOR : '');
            }

            $this->config['view_path'] = $path;
        }

        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }

        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }

        // 模板不存在 抛出异常
        if (!is_file($template)) {
            throw new FileNotFoundException('template not exists:' . $template);
        }

        $this->_boot();

        echo (string) $this->template->file($template, $data);
    }

    /**
     * 渲染模板内容
     * @access public
     * @param string $content 模板内容
     * @param array  $data    模板变量
     * @return void
     */
    public function display(string $content, array $data = []): void
    {
        $this->_boot();

        echo (string) $this->template->make($content, $data);
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate(string $template): string
    {
        // 分析模板文件规则
        $request = $this->app['request'];

        // 获取视图根目录
        if (strpos($template, '@')) {
            // 跨模块调用
            list($app, $template) = explode('@', $template);
        }

        if (isset($app)) {
            $view     = $this->config['view_dir_name'];
            $viewPath = $this->app->getBasePath() . $app . DIRECTORY_SEPARATOR . $view . DIRECTORY_SEPARATOR;

            if (is_dir($viewPath)) {
                $path = $viewPath;
            } else {
                $path = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR;
            }
            $this->config['view_path'] = $path;
        } else {
            $path = $this->config['view_path'];
        }

        $depr = $this->config['view_depr'];

        if (0 !== strpos($template, '/')) {
            $template   = str_replace(['/', ':'], $depr, $template);
            $controller = $request->controller();

            if (strpos($controller, '.')) {
                $pos        = strrpos($controller, '.');
                $controller = substr($controller, 0, $pos) . '.' . Str::snake(substr($controller, $pos + 1));
            } else {
                $controller = Str::snake($controller);
            }

            if ($controller) {
                if ('' == $template) {
                    // 如果模板文件名为空 按照默认模板渲染规则定位
                    if (2 == $this->config['auto_rule']) {
                        $template = $request->action(true);
                    } elseif (3 == $this->config['auto_rule']) {
                        $template = $request->action();
                    } else {
                        $template = Str::snake($request->action());
                    }

                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                } elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }

        return $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');
    }

    /**
     * 配置模板引擎
     * @access private
     * @param array $config 参数
     * @return void
     */
    public function config(array $config): void
    {
        $this->config = array_merge($this->config, $config);

        $this->_boot();
    }

    /**
     * 获取模板引擎配置
     * @access public
     * @param string $name 参数名
     * @return mixed
     */
    public function getConfig(string $name)
    {
        return $this->config[$name] ?? null;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->template, $method], $params);
    }
}
