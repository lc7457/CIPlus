<?php
/// region <<< 仿照HTTP1.1错误码扩展，更便于理解 >>>

// waiting
$lang['m10000'] = '请等待，已接受当前操作请求，正在等待后续操作'; // 等待继续
$lang['m10001'] = '请等待，服务器正在进行数据切换'; // 等待切换
// success
$lang['m20000'] = '请求成功，匹配数据成功'; // 成功
$lang['m20001'] = '请求成功，数据创建成功'; // 已创建
$lang['m20002'] = '请求成功，但未进行处理'; // 已接受
$lang['m20003'] = '请求成功，但返回的为站外数据'; // 非授权信息
$lang['m20004'] = '请求成功，但未找到匹配的数据'; // 无内容
$lang['m20005'] = '请求成功，但匹配的数据不合法，请重新提交'; // 重置内容
$lang['m20006'] = '请求成功，但仅完成了部分操作';// 部分内容
// redirect
$lang['m30000'] = '重定向，由用户进行选择'; // 多种选择
$lang['m30001'] = '重定向，当前接口已永久迁移'; // 永久移动
$lang['m30002'] = '重定向，当前接口临时迁移'; // 临时移动
$lang['m30003'] = '重定向，系统自动进行跳转'; // 查看其他位置
$lang['m30004'] = '重定向，请求缓存'; // 未修改
$lang['m30005'] = '重定向，重定向至代理服务器'; // 使用代理
$lang['m30007'] = '重定向，系统二级分发请求'; // 临时重定向
// access error
$lang['m40000'] = '请求失败，操作异常'; // 未知问题【例如：有些懒得解释的错误请求】
$lang['m40001'] = '请求失败，没有对应的访问权限'; // 无权限
$lang['m40003'] = '请求失败，服务器拒绝响应当前请求'; // 禁止【解释，因为操作不合法的禁止调用】
$lang['m40004'] = '请求失败，当前接口不存在'; // 未找到
$lang['m40005'] = '请求失败，当前接口已禁用'; // 方法禁用【解释：系统全面禁止调用】
$lang['m40006'] = '请求失败，系统不接受当前请求的内容'; // 不接受【解释：小范围用户禁止调用】
$lang['m40007'] = '请求失败，需要代理服务器进行授权'; // 需要代理授权
$lang['m40008'] = '请求失败，等待超时'; // 请求超时
$lang['m40009'] = '请求失败，当前操作发生数据冲突'; // 冲突
$lang['m40010'] = '请求失败，当前接口已被永久删除'; // 已删除
$lang['m40011'] = '请求失败，缺少有效参数'; // 需要有效长度【例如：name为必填参数，没有或者为空】
$lang['m40012'] = '请求失败，未满足前置条件'; // 未满足前提条件
$lang['m40013'] = '请求失败，当前请求的数据超过服务器允许范围'; // 请求实体过大【例如：上传的文件过大】
$lang['m40014'] = '请求失败，URI超过允许的长度'; // URI过长
$lang['m40015'] = '请求失败，不支持的媒体类型'; // 不支持的媒体类型【例如：上传的文件不合法】
$lang['m40016'] = '请求失败，当前请求的参数值不在合法范围内'; // 请求范围不符合要求【例如：接口参数设置好了范围，但是请求参数超过了范围】
$lang['m40017'] = '请求失败，未满足参数“期望”'; // 未满足期望值【例如：A、B、C、D都合法，但是当前操作应该填A或B，却填了C或D】
// server error
$lang['m50000'] = '服务器异常，无法完成请求'; // 内部错误
$lang['m50001'] = '服务器异常，服务器无法识别当前请求'; // 尚未实施
$lang['m50002'] = '服务器异常，发生内部通讯错误'; // 网关错误
$lang['m50003'] = '服务器异常，服务器正在维护'; // 服务不可用
$lang['m50004'] = '服务器异常，请求超时'; // 网关超时
$lang['m50005'] = '服务器异常，系统配置异常'; // 版本不支持

/// endregion