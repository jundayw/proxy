<a id="readme-top"></a>

# Proxy Manager

A lightweight, type-safe, inheritance-based proxy and interception framework for PHP.

[![GitHub Tag][GitHub Tag]][GitHub Tag URL]
[![Total Downloads][Total Downloads]][Packagist URL]
[![Packagist Version][Packagist Version]][Packagist URL]
[![Packagist PHP Version Support][Packagist PHP Version Support]][Repository URL]
[![Packagist License][Packagist License]][Repository URL]

<a id="readme-top"></a>

---

## Overview / 概述

**Jundayw Proxy** is a runtime proxy generation framework for modern PHP applications.

**Jundayw Proxy** 是一个面向现代 PHP 应用的运行时代理生成框架。

It generates **inheritance-based proxy classes** from existing PHP classes and provides a transparent interception layer for cross-cutting concerns such as:

它可以基于现有 PHP 类动态生成**继承式代理类**，为以下横切关注点提供透明的拦截层：

* Logging / 日志
* Distributed tracing / 分布式链路追踪
* Metrics / 指标统计
* Authorization / 权限控制
* Transactions / 事务
* Caching / 缓存
* Idempotency / 幂等控制
* Auditing / 审计
* Performance monitoring / 性能监控
* Custom middleware / 自定义中间件

The original business class does not need to be modified.

原始业务类无需为了代理逻辑而修改。

```text
Application
     │
     ▼
 ProxyManager
     │
     ▼
ProxyGenerator
     │
     ▼
Generated Proxy
     │
     ├── Trace
     ├── Logging
     ├── Metrics
     ├── Authorization
     └── ...
     │
     ▼
Original Business Class
```

> **Build the business logic once. Intercept it anywhere.**

> **业务逻辑只实现一次，横切能力随处接入。**

---

### Type Safety / 类型安全

Generated proxy methods preserve the original method signatures.

生成的代理方法尽可能保持原始方法签名：

```php
public function execute(
    string $name,
    ?int $id = null,
): ?string {
    // ...
}
```

Instead of degrading the method into untyped PHP:

而不是退化成：

```php
public function execute(
    $name,
    $id = null,
) {
    // ...
}
```

---

### Inheritance Compatibility / 继承兼容

The generated proxy is based on the original class:

生成的代理类基于原始类：

```php
class OrderServiceProxy extends OrderService
{
}
```

Therefore:

因此：

```php
$proxy instanceof OrderService;
```

remains valid.

仍然成立。

This allows the proxy to work naturally with existing PHP type declarations.

因此代理对象可以自然地用于已有的 PHP 类型约束。

---

### Attribute-driven Configuration / Attribute 驱动

Proxy behavior can be declared directly on classes and methods.

代理行为可以直接通过 Attribute 声明在类和方法上：

```php
#[Proxy([
    new TraceMiddleware(),
    new LoggingMiddleware(),
])]
class OrderService
{
}
```

---

### Non-invasive Interception / 非侵入式拦截

Business code remains focused on business logic.

业务代码始终关注业务逻辑本身。

```php
class OrderService
{
    public function create(...): Order
    {
        // Business logic
    }
}
```

Logging, tracing, metrics, authorization, transactions and other cross-cutting concerns can be introduced externally.

日志、追踪、指标、权限、事务等横切逻辑可以通过代理层独立注入。

---

# Features / 特性

* Runtime proxy generation / 运行时代理生成
* Inheritance-based proxy / 基于继承的代理
* Attribute-driven configuration / Attribute 驱动配置
* Class-level proxy configuration / 类级代理配置
* Method-level proxy exclusion / 方法级代理排除
* Middleware pipeline / 中间件管道
* Type-safe method generation / 类型安全的方法生成
* PHP 8.2+ type system support / PHP 8.2+ 类型系统支持
* Nullable types / Nullable 类型
* Union types / Union 类型
* Intersection types / Intersection 类型
* Return type preservation / 返回值类型保持
* Parameter type preservation / 参数类型保持
* Default parameter preservation / 默认参数保持
* Dependency Injection compatible / 兼容依赖注入
* Laravel-friendly integration / Laravel 友好集成
* Custom proxy instantiation / 自定义代理实例化
* Proxy class reuse / 代理类复用
* Extensible middleware architecture / 可扩展中间件架构

---

# Architecture / 架构

The overall execution architecture:

整体执行架构：

```text
┌─────────────────────────────┐
│       Application Code      │
│         应用程序代码         │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│        ProxyManager         │
│          代理管理器           │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│       ProxyGenerator        │
│          代理生成器           │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│      Generated Proxy        │
│          生成代理类           │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│     Middleware Pipeline     │
│          中间件管道           │
│                             │
│ Trace → Log → Metrics → ... │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│    Original Business Class  │
│          原始业务类           │
└─────────────────────────────┘
```

The framework separates responsibilities into several independent components.

框架将不同职责拆分为多个独立组件：

| Component / 组件   | Responsibility / 职责                    |
|------------------|----------------------------------------|
| `Configuration`  | Proxy configuration / 代理配置             |
| `ProxyGenerator` | Proxy class generation / 代理类生成         |
| `ProxyManager`   | Proxy lifecycle and creation / 代理创建与管理 |
| `Middleware`     | Cross-cutting behavior / 横切逻辑          |

---

# Installation / 安装

Install the package via Composer:

通过 Composer 安装：

```bash
composer require jundayw/proxy
```

Requirements / 环境要求：

```text
PHP >= 8.2
```

---

# Quick Start / 快速开始

## 1. Define Middleware / 定义中间件

A middleware can wrap the execution of the original method.

中间件可以包装原始方法的执行过程。

For example, a timing middleware:

例如，一个简单的执行耗时统计中间件：

```php
<?php

namespace App\Proxy\Middleware;

use Jundayw\Proxy\Contracts\Middleware;
use Jundayw\Proxy\Invocation;

class TimingMiddleware implements Middleware
{
    public function __invoke($request, Closure $next)
    {
        return $next($request);
    }
}
```

Middleware can be used for:

中间件可以用于：

* Logging / 日志
* Tracing / 链路追踪
* Metrics / 指标
* Authorization / 权限
* Transactions / 事务
* Caching / 缓存
* Idempotency / 幂等
* Auditing / 审计
* Performance monitoring / 性能监控

---

# 2. Define a Proxy / 定义代理

A realistic application service is more useful than a trivial `Test` class.

相比简单的 `Test` 类，下面使用一个真实业务中的订单服务作为示例：

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Requests\CreateOrderRequest;
use App\Proxy\Middleware\LoggingMiddleware;
use App\Proxy\Middleware\TimingMiddleware;
use App\Proxy\Middleware\TraceMiddleware;
use Jundayw\Proxy\Attributes\IgnoreProxy;
use Jundayw\Proxy\Attributes\Proxy;
use Jundayw\Proxy\Attributes\ProxyIgnore;

#[Proxy([
    new TraceMiddleware(),
    new LoggingMiddleware(),
    new TimingMiddleware(),
])]
class OrderService
{
    public function __construct(
        protected OrderRepository $repository,
    ) {
    }

    public function create(
        CreateOrderRequest $request,
    ): Order {
        return $this->repository->create([
            'user_id' => $request->userId,
            'amount' => $request->amount,
        ]);
    }

    public function find(
        int $id,
    ): ?Order {
        return $this->repository->find($id);
    }

    public function update(
        int $id,
        array $attributes,
    ): Order {
        return $this->repository->update(
            $id,
            $attributes,
        );
    }

    #[IgnoreProxy]
    public function healthCheck(): string
    {
        return 'ok';
    }

    #[ProxyIgnore]
    public function internalCalculation(
        int $amount,
    ): int {
        return $amount * 100;
    }
}
```

The business service does not contain any proxy-specific infrastructure code.

业务服务本身不需要包含任何代理基础设施代码。

There is no need to write:

不需要在业务代码中编写：

```php
$this->logger->...
$this->trace->...
$this->metrics->...
$this->transaction->...
```

The business class remains focused on business behavior.

业务类只负责业务逻辑。

---

# 3. Create the Proxy Infrastructure / 创建代理基础设施

```php
<?php

use Jundayw\Proxy\Configuration;
use Jundayw\Proxy\ProxyGenerator;
use Jundayw\Proxy\ProxyManager;

require_once 'vendor/autoload.php';

$config = require 'config/proxy.php';

$configuration = new Configuration($config);

$generator = new ProxyGenerator(
    $configuration,
);

$proxyManager = new ProxyManager(
    $configuration,
    $generator,
);
```

---

# 4. Generate the Proxy / 生成代理

Create a proxy for the service:

为服务生成代理：

```php
$service = $proxyManager->create(
    OrderService::class,
);
```

Conceptually, the generated class looks like:

从概念上看，生成的类类似于：

```php
class OrderServiceProxy extends OrderService
{
    // Generated interception logic
}
```

The actual implementation is generated by `ProxyGenerator`.

实际代码由 `ProxyGenerator` 自动生成。

---

# 5. Execute the Proxy / 执行代理

The proxy can be used like the original service:

代理对象可以像原始服务一样使用：

```php
$order = $service->create(
    CreateOrderRequest::class,
    fn(CreateOrderRequest $proxy) => new $proxy(
        userId: 10001,
        amount: 1999,
    ),
);
```

Execution flow:

执行流程：

```text
OrderService::create()
        │
        ▼
┌─────────────────────┐
│ TraceMiddleware     │
│ 链路追踪              │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ LoggingMiddleware   │
│ 日志                  │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ TimingMiddleware    │
│ 性能统计              │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ OrderService::create│
│ 原始业务逻辑           │
└─────────────────────┘
```

---

# Attributes / Attributes 属性

## `#[Proxy]`

`#[Proxy]` enables proxy generation and defines the middleware pipeline.

`#[Proxy]` 用于启用代理并定义中间件管道。

```php
#[Proxy([
    new TraceMiddleware(),
    new LoggingMiddleware(),
    new TimingMiddleware(),
])]
class OrderService
{
}
```

Middleware is executed according to the configured pipeline.

中间件按照定义的顺序执行。

---

## `#[IgnoreProxy]`

Use `#[IgnoreProxy]` to exclude a method from proxy interception.

使用 `#[IgnoreProxy]` 可以让指定方法跳过代理拦截。

```php
#[IgnoreProxy]
public function healthCheck(): string
{
    return 'ok';
}
```

Typical use cases include:

典型使用场景：

* Health checks / 健康检查
* Internal methods / 内部方法
* Framework callbacks / 框架回调
* Lightweight methods / 轻量级方法
* Performance-sensitive methods / 性能敏感方法

---

## `#[ProxyIgnore]`

`#[ProxyIgnore]` provides method-level proxy exclusion.

`#[ProxyIgnore]` 同样用于方法级代理排除。

```php
#[ProxyIgnore]
public function internalCalculation(
    int $amount,
): int {
    return $amount * 100;
}
```

This makes proxy behavior explicit at the method level.

这样可以让代理行为直接表达在方法定义上。

---

# Middleware / 中间件

Middleware is the primary extension point of Jundayw Proxy.

Middleware 是 Jundayw Proxy 最核心的扩展点。

A production application can construct a pipeline such as:

生产环境中可以构建如下中间件链：

```text
Request
   │
   ▼
┌─────────────────────┐
│ Authorization       │
│ 权限校验              │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ Trace               │
│ 链路追踪              │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ Logging             │
│ 日志                  │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ Metrics             │
│ 指标                  │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ Transaction         │
│ 事务                  │
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│ Business Method     │
│ 业务方法              │
└─────────────────────┘
```

Example:

示例：

```php
#[Proxy([
    new AuthorizationMiddleware(),
    new TraceMiddleware(),
    new LoggingMiddleware(),
    new TransactionMiddleware(),
])]
class PaymentService
{
    // ...
}
```

This keeps infrastructure concerns outside the business implementation.

这样可以让基础设施逻辑与业务实现保持隔离。

---

# ProxyManager / 代理管理器

`ProxyManager` is the primary runtime entry point.

`ProxyManager` 是运行时创建代理的主要入口。

```php
$proxyManager = new ProxyManager(
    $configuration,
    $generator,
);
```

Create a proxy:

创建代理：

```php
$proxy = $proxyManager->create(
    OrderService::class,
);
```

---

## Custom Instantiation / 自定义实例化

A callback can be supplied when creating a proxy:

创建代理时可以传入自定义实例化回调：

```php
$proxy = $proxyManager->create(
    OrderService::class,
    fn (string $class) => app($class),
);
```

This is especially useful for Laravel applications.

这对于 Laravel 应用尤其有用。

Laravel remains responsible for dependency resolution while the proxy manager handles proxy creation.

Laravel 继续负责依赖解析，而 ProxyManager 负责代理创建。

---

# ProxyGenerator / 代理生成器

`ProxyGenerator` is responsible for generating proxy classes.

`ProxyGenerator` 负责生成代理类。

```php
$generator = new ProxyGenerator(
    $configuration,
);
```

The generator analyzes the original class using PHP reflection.

生成器通过 PHP Reflection 分析原始类。

Conceptually:

概念流程：

```text
ReflectionClass
       │
       ▼
ProxyGenerator
       │
       ├── Class
       ├── Properties
       ├── Methods
       ├── Parameters
       ├── Return Types
       └── Attributes
       │
       ▼
Generated Proxy Class
```

This allows the proxy generator to work with existing application classes without requiring them to implement a special interface.

这样可以直接为现有业务类生成代理，而不要求业务类实现特殊接口。

---

# Configuration / 配置

Proxy behavior is configured through `Configuration`.

代理行为通过 `Configuration` 进行配置。

```php
$config = require 'config/proxy.php';

$configuration = new Configuration(
    $config,
);
```

A typical configuration file may look like:

典型配置文件可以设计为：

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxy Directory
    |--------------------------------------------------------------------------
    */

    'directories' => storage_path(
        'framework/proxy',
    ),

];
```

The exact options depend on the version and implementation of the package.

具体配置项以当前版本实际实现为准。

---

# Type Safety / 类型安全

Type preservation is one of the key design goals of Jundayw Proxy.

类型保持是 Jundayw Proxy 的核心设计目标之一。

Consider:

例如：

```php
class ExampleService
{
    public function execute(
        string $name,
        ?int $id = null,
    ): ?string {
        return $name;
    }
}
```

The generated proxy should preserve:

生成的代理应该保持：

```php
public function execute(
    string $name,
    ?int $id = null,
): ?string
```

rather than:

而不是：

```php
public function execute(
    $name,
    $id = null,
) {
}
```

This is particularly important when applications use strict PHP type declarations.

这对于大量使用 PHP 强类型声明的现代应用尤其重要。

---

## Named Types / Named 类型

```php
public function find(
    UserRepository $repository,
): User {
    // ...
}
```

The parameter and return type declarations are preserved.

参数类型和返回类型保持不变。

---

## Nullable Types / Nullable 类型

```php
public function find(
    ?string $id = null,
): ?User {
    // ...
}
```

---

## Union Types / Union 类型

```php
public function resolve(
    int|string $id,
): User|Order {
    // ...
}
```

---

## Intersection Types / Intersection 类型

```php
public function execute(
    Countable&Iterator $items,
): Result {
    // ...
}
```

---

## PHP Reflection Type System / PHP Reflection 类型系统

The proxy generator is designed around PHP reflection and modern type representations including:

代理生成器基于 PHP Reflection，并针对现代 PHP 类型系统进行处理，包括：

```text
ReflectionNamedType
ReflectionUnionType
ReflectionIntersectionType
```

This allows generated proxy methods to remain compatible with PHP 8.1+ type declarations.

从而让生成的代理方法能够兼容 PHP 8.1+ 的类型声明。

---

# Inheritance-based Proxy / 基于继承的代理

Jundayw Proxy uses inheritance-based proxy generation.

Jundayw Proxy 采用基于继承的代理生成方式。

Instead of:

不同于：

```php
class OrderServiceProxy
{
    public function __construct(
        private OrderService $service,
    ) {
    }
}
```

the proxy is conceptually:

代理在概念上是：

```php
class OrderServiceProxy extends OrderService
{
}
```

Therefore:

因此：

```php
$proxy instanceof OrderService;
```

returns:

返回：

```text
true
```

This allows the proxy to participate in existing type constraints.

这意味着代理对象可以自然参与现有类型约束。

For example:

例如：

```php
function handle(
    OrderService $service,
): void {
    // ...
}
```

The generated proxy can be passed directly:

生成的代理可以直接传入：

```php
handle($proxy);
```

This is one of the fundamental differences between an inheritance-based proxy and a conventional decorator.

这也是继承式 Proxy 与传统 Decorator 模式的重要区别之一。

---

# Laravel Integration / Laravel 集成

Jundayw Proxy is designed to integrate naturally with Laravel's Service Container.

Jundayw Proxy 可以自然地与 Laravel Service Container 集成。

For example:

例如：

```php
$proxy = $proxyManager->create(
    OrderService::class,
    fn (string $class) => app($class),
);
```

The architecture becomes:

整体架构可以形成：

```text
┌──────────────────────────┐
│    Laravel Container     │
│       Laravel 容器         │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│      ProxyManager        │
│        代理管理器          │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│       Proxy Class        │
│          代理类            │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│ Interceptor / Middleware │
│       拦截 / 中间件         │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│    Application Service   │
│        应用服务            │
└──────────────────────────┘
```

This makes it possible to introduce proxy-based cross-cutting concerns without changing the existing dependency injection model.

因此，可以在不改变现有依赖注入模型的情况下，为 Laravel 应用引入代理式横切能力。

---

# Use Cases / 应用场景

## Logging / 日志

Automatically record service method execution.

自动记录服务方法执行信息：

```text
OrderService::create
PaymentService::pay
UserService::register
```

---

## Distributed Tracing / 分布式链路追踪

Track service calls across application boundaries.

跟踪应用内部不同服务之间的调用关系：

```text
HTTP Request
     │
     ▼
OrderService
     │
     ├── OrderRepository
     │
     ├── PaymentService
     │
     └── NotificationService
```

---

## Metrics / 指标统计

Collect:

统计：

* Execution count / 执行次数
* Execution duration / 执行耗时
* Error count / 异常次数
* Success count / 成功次数
* Method-level statistics / 方法级统计

---

## Authorization / 权限控制

Centralize authorization behavior:

统一处理权限控制：

```php
#[Proxy([
    new AuthorizationMiddleware(),
])]
class AdminService
{
}
```

---

## Transactions / 事务

Wrap application services with transaction behavior:

为应用服务统一增加事务能力：

```php
#[Proxy([
    new TransactionMiddleware(),
])]
class OrderService
{
}
```

The business implementation does not need to explicitly manage the transaction boundary.

业务代码不需要显式处理事务边界。

---

## Caching / 缓存

Caching can be introduced as middleware:

可以通过中间件引入缓存：

```php
#[Proxy([
    new CacheMiddleware(),
])]
class ProductService
{
}
```

This keeps caching policy separate from business logic.

从而让缓存策略与业务逻辑保持独立。

---

# Complete Example / 完整示例

The following example combines multiple concepts.

下面的示例将多个能力组合起来：

```php
<?php

use Jundayw\Proxy\Attributes\IgnoreProxy;
use Jundayw\Proxy\Attributes\Proxy;
use Jundayw\Proxy\Attributes\ProxyIgnore;
use Jundayw\Proxy\Configuration;
use Jundayw\Proxy\ProxyGenerator;
use Jundayw\Proxy\ProxyManager;

require_once 'vendor/autoload.php';

$config = require 'config/proxy.php';

$configuration = new Configuration($config);

$generator = new ProxyGenerator(
    $configuration,
);

$proxyManager = new ProxyManager(
    $configuration,
    $generator,
);

#[Proxy([
    new TraceMiddleware(),
    new LoggingMiddleware(),
    new TimingMiddleware(),
])]
class OrderService
{
    public function create(
        string $orderNo,
        int|float $amount,
    ): ?Order {
        // Business logic
    }

    public function find(
        int|string $id,
    ): ?Order {
        // Business logic
    }

    #[IgnoreProxy]
    public function healthCheck(): string
    {
        return 'ok';
    }

    #[ProxyIgnore]
    public function internalCalculation(
        int $amount,
    ): int {
        return $amount * 100;
    }
}

$service = $proxyManager->create(
    OrderService::class,
    fn (string $class) => app($class),
);

$order = $service->create(
    'ORD-202610060001',
    1999.00,
);
```

The important point is that the application code does not need to know whether `$service` is an original object or a generated proxy.

最重要的一点是：应用代码无需关心 `$service` 究竟是原始对象还是动态生成的代理对象。

---

# Recommended Architecture / 推荐架构

For large PHP applications, Jundayw Proxy can be used as an infrastructure layer rather than being coupled directly to the business layer.

对于大型 PHP 应用，建议将 Jundayw Proxy 作为独立基础设施层，而不是直接与业务代码耦合。

```text
┌───────────────────────────────────────────┐
│                Application                │
│                 应用层                    │
├───────────────────────────────────────────┤
│                  Proxy                   │
│              代理 / 拦截层                 │
├───────────────────────────────────────────┤
│              Middleware                  │
│                中间件层                    │
├───────────────────────────────────────────┤
│              Domain / Service            │
│               领域 / 服务层                │
├───────────────────────────────────────────┤
│          Repository / Infrastructure     │
│             基础设施 / 持久化              │
└───────────────────────────────────────────┘
```

This separation is especially useful in:

这种架构特别适合：

* Large Laravel applications / 大型 Laravel 应用
* Modular monoliths / 模块化单体
* SaaS platforms / SaaS 平台
* API platforms / API 平台
* Enterprise applications / 企业级应用
* Multi-tenant systems / 多租户系统
* Microservice-oriented architectures / 微服务架构

---

# Limitations / 限制

Inheritance-based proxies naturally follow PHP's inheritance rules.

基于继承的 Proxy 必须遵循 PHP 本身的继承规则。

For example, `final` classes cannot be extended:

例如，`final` 类无法被继承：

```php
final class Example
{
}
```

Likewise, `final` methods cannot be overridden:

同样，`final` 方法无法被重写：

```php
class Example
{
    final public function execute(): void
    {
    }
}
```

Therefore, such classes or methods cannot participate in normal inheritance-based interception.

因此，这些类或方法无法参与正常的继承式代理拦截。

Other PHP restrictions such as visibility, method compatibility, property compatibility and constructor behavior must also be respected by the generated proxy.

此外，生成代理时还必须遵循 PHP 对可见性、方法签名兼容性、属性以及构造函数等方面的约束。

---

# Roadmap / 路线图

Potential future improvements include:

* [x] Automatic Laravel Container proxying / Laravel Container 自动代理
* [x] Proxy class cache / Proxy 类缓存
* [x] Interface proxy / Interface 代理
* [x] Around interceptor / Around 拦截器
* [x] Before / After interceptor / Before / After 拦截器
* [ ] Method-level middleware / 方法级中间件
* [ ] Proxy debugging utilities / Proxy 调试工具
* [x] PSR-oriented integrations / PSR 生态集成

---

<!-- CONTRIBUTORS -->

## Contributors

Thanks goes to these wonderful people:

<a href="https://github.com/jundayw/proxy/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=jundayw/proxy" alt="contrib.rocks image" />
</a>

Contributions of any kind are welcome!

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- LICENSE -->

## License

Distributed under the MIT License (MIT). Please see [License File] for more information.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

[GitHub Tag]: https://img.shields.io/github/v/tag/jundayw/proxy

[Total Downloads]: https://img.shields.io/packagist/dt/jundayw/proxy?style=flat-square

[Packagist Version]: https://img.shields.io/packagist/v/jundayw/proxy

[Packagist PHP Version Support]: https://img.shields.io/packagist/php-v/jundayw/proxy

[Packagist License]: https://img.shields.io/github/license/jundayw/proxy

[GitHub Tag URL]: https://github.com/jundayw/proxy/tags

[Packagist URL]: https://packagist.org/packages/jundayw/proxy

[Repository URL]: https://github.com/jundayw/proxy

[GitHub Open Issues]: https://github.com/jundayw/proxy/issues

[Composer]: https://getcomposer.org

[License File]: https://github.com/jundayw/proxy/blob/main/LICENSE
