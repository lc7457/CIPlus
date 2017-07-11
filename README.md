# 什么是CIPlus

CIPlus是对CI框架的无损补充，在CI框架的基础上增加了更多本地化的类库和全局工具库。
可以在CI修复性升级后直接将原版框架中的system部分进行替换。

## API_Controller ：**规范API抽象类**

该控制器类中定义了API的基本规范格式。

1、标准API包含code、message、data三个基本要素（详见开发规范）

通过SetCode(int),SetMessage(string),SetData(array)三个方法为接口的三个要素进行赋值。

其中SetCode方法赋值后会在多语言文件api_message_lang中查询是否存在对应的message，若存在则自动赋值。

Respond(int,string,array)方法将输出标准API数据。其中包含任意个可选参数，可以在调用Respond方法的同时为API三要素进行赋值，不同的参数类型对应SetCode(int),SetMessage(string),SetData(array)方法。

**示例1-1：**
```
#任意API控制器实现方法内

#方式一
$this->SetCode(20000);
$this->SetMessage('操作成功');
$this->SetData(array('name'=>'lichao'));
$this->Respond();

#方式二
$this->Respond(20000,'操作成功',array('name'=>'lichao'));

#输出
{
    "code": 20000,
    "message": "操作成功",
    "data": {
            "name": "lichao"
        }
}
```

2、API的输出格式由全局HTTP参数_format控制

随API参数传递_format数据可以修改API输出的数据格式，默认为JSON，同时可以修改为XML，CSV等等。

**示例1-2：**
```
#假设 示例1-1 的访问路径为 http://demo.ciplus.com/api/demo
#以GET方法为例，添加访问参数：
#http://demo.ciplus.com/api/demo?_format=xml

#输出
<xml>
    <code>20000</code>
    <message>操作成功</message>
    <data>
        <name>lichao</name>
    </data>
</xml>
```

3、增加配置参数strict

strict为true时，所有除Respond输出的方法外的echo，print_f等都将被过滤，严格输出API数据。

4、接口参数接收方法Request(array,array,string)

根据API的特性进行封装，可扩展验证API参数。

该方法第一个参数表示接口中的必填参数，可以对传入的参数空值进行报错。

该方法第二个参数表示接口中的选填参数，参数可以为空。所有选填参数若值为空则不生成键值。

该方法第三个参数表示数据提交方法，默认为兼容POST/GET方法，若设置POST或GET则强制使用对应方法获取参数。

Request方法获取的参数都会对应查找对应的参数验证方法，若在子类中实现该方法则进行对应的参数验证操作。

参数验证方法默认前缀为Verify_，后面接参数key。

**示例1-3：**
```
// API方法
public function Demo(){
    $this->Request(array('id'),array('page'),'get');
    ...数据操作
    $this->Respond();
}

// 验证当前接口类中接收到的id参数

public Verify_id($value){
    ...验证value
    // 非法则：
    $this->Respond(40000,'error');
}

// 验证分页参数，当前页：page
// 参数value使用取地址符“&”是为了设置默认值
public Verify_page(&$value){
    if ($value <= 0)
        $value = 1;
    return $value;
}
```

5、返回API参数结果集方法RequestData()

返回所有必填、选填参数的结果集数组，包含键和值

**示例1-4：**
```
#访问接口
/api/demo?a=1&b=2&name=lichao&age=10

// 接口实现方法
public function Index() {
    $this->Request(array('a', 'b'), array('name', 'age'));
    $arr = $this->RequestData();
    print_r($arr);
}

#输出：
Array ( [a] => 1 [b] => 2 [name] => lichao [age] => 10 )

```

6、过滤数据方法FilterData(array,bool)

返回过滤后的数据结果集，方便构造数据库操作。

第一个参数为过滤参数名单数组，填写过滤的参数key

第二个参数为过滤方法，true为滤取交集，false为滤取补集

**示例1-5：**
```
#访问接口
/api/demo?a=1&b=2&name=lichao&age=10

// 接口实现方法 1
public function Index() {
    $this->Request(array('a', 'b'), array('name', 'age'));
    $arr = $this->RequestData();
    $arr1 = $this->FilterData(array('a', 'name'), true);
    print_r($arr1);
}

#输出：
Array ( [a] => 1 [name] => lichao )


// 接口实现方法 2
public function Index() {
    $this->Request(array('a', 'b'), array('name', 'age'));
    $arr = $this->RequestData();
    $arr1 = $this->FilterData(array('a', 'name'), false);
    print_r($arr1);
}
#输出：
Array ( [b] => 2 [age] => 10 )
```

## MY_Controller API安全抽象类

该类库继承了API_Controller类，并在其基础上增加对API的统一授权认证。

授权及认证技术相关文档请阅读“统一认证中心的技术方案”

该类直接为API实现类服务，通过集成该类可以完成API的安全认证，登录认证及权限控制

在子类实现构造方法中传入数组可开关相关功能（alpha版，完善中）

**子类示例：**
```
class Demo extends MY_Controller {

    public function __construct() {
        parent::__construct();
     
    }
}
```

> 可访问属性:

protected $uid = ''; // 当前用户的唯一身份ID
protected $data = array();  // 加密TOKEN中解析出来的数据
public $isLogin = false; // 当前访问用户是否登录
public $isAdmin = false; // 当前访问用户是不是管理员


