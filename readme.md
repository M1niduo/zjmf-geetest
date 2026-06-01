# 魔方财务极验插件 [迷你哆云](https://www.miniduo.cn)

> 免预存招代理 Q:1283187190

### 使用步骤
1. [先注册极验账号](https://docs.geetest.com/gt4/handbook)

2. 在 .env 配置第一步申请得 geetest_captcha_id 与 geetest_captcha_key 后复制 .env 到根目录下

3. 解压到 public 目录下，包含插件 GeetestPlugin 和 模板文件 verify.tpl

4. 安装插件即可使用

> 未使用 ajax 和 jquery 的模板或 *非标准模板* 需要自己改代码，核心逻辑参考 hooks.php 和 verify.tpl

```js
$('input[id^="captcha_"]').closest('.form-group').empty().html(`<label for="code">行为验证</label>`)
captchaObj.appendTo($(capGroup))
```

### 成品展示
![成品展示](https://raw.githubusercontent.com/M1niduo/zjmf-geetest/refs/heads/main/preview.jpeg)