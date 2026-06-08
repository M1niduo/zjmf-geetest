<?php

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use think\facade\Hook;

Hook::add('geetest', function () {
    $captcha_id = env('geetest_captcha_id', '647f5ed2ed8acb4be36784e01556bb71');
    return <<<HTML
        <script>
        (function() {
            // 你的极验配置
            const CAPTCHA_ID = '{$captcha_id}';
            const GEETEST_JS_URL = 'https://static.geetest.com/v4/gt4.js';

            // 加载极验脚本
            function loadGeetest(callback) {
                // 如果已有 script 标签正在加载，避免重复
                if (document.querySelector('script[src="' + GEETEST_JS_URL + '"]')) {
                    // 轮询检查是否加载完成
                    const check = setInterval(function() {
                        if (typeof initGeetest4 !== 'undefined') {
                            clearInterval(check);
                            callback();
                        }
                    }, 100);
                    return;
                }
                \$.getScript(GEETEST_JS_URL)
                    .done(function() {
                        callback();
                    })
                    .fail(function() {
                        alert('验证码组件加载失败，请刷新页面重试');
                    });
            }
            loadGeetest(showCaptcha);
            // 弹出验证码（popup 模式）
            function showCaptcha() {
                var capGroup = \$('input[id^="captcha_"]').closest('.form-group').empty().html(`<label for="code">行为验证</label>`)
                const intersectionObserver = new IntersectionObserver((entries) => {
                    const target = entries.find(e => e.intersectionRatio > 0)
        			if (!target) return;
                    captchaInstance.reset()
        			captchaInstance.target = target.target
                    captchaInstance.appendTo(captchaInstance.target)
                });
                initGeetest4({
                    captchaId: CAPTCHA_ID,
                    product: 'popup',
                    language: 'zho',
                    riskType: 'slide',
                    nativeButton: { width: '100%', height:'40px' }
                }, function(captchaObj) {
                    captchaInstance = captchaObj;
                    capGroup.each((index, target) => {
                        \$(target).addClass('d-flex flex-column')
                        intersectionObserver.observe(target)
                    })
                    captchaObj.onSuccess(function(e) {
                        var result = captchaObj.getValidate();
                        if (!result) {
                            toastr.error('验证失败，请重试')
                            captchaObj.reset();
                            return;
                        }
                        \$(captchaInstance.target).append(`<input type="hidden" name="x-geetest-token" value="${encodeURIComponent(JSON.stringify(result))}">`)
                        \$.ajaxSetup({
                            headers: {
                                'x-geetest-token': JSON.stringify(result)
                            },
                            complete: function(xhr) {
                                captchaObj.reset();
                                delete \$.ajaxSettings.headers['x-geetest-token'];
                            }
                        });
                    });
                });
            }

        })();
        </script>
        HTML;
});

Hook::add('custom_captcha_check', function () {
    $captcha_id = env('geetest_captcha_id', '647f5ed2ed8acb4be36784e01556bb71');
    $captcha_key = env('geetest_captcha_key', 'b09a7aafbfd83f73b35a9b530d0337bf');
    $params = json_decode(request()->header('x-geetest-token'), true);
    if (empty($params))
        return false;
    try {
        $query = [
            'lot_number' => $params['lot_number'],
            'captcha_output' => $params['captcha_output'],
            'pass_token' => $params['pass_token'],
            'gen_time' => $params['gen_time'],
            'sign_token' => hash_hmac('sha256', $params['lot_number'], $captcha_key)
        ];
        $client = new Client(['base_uri' => 'http://gcaptcha4.geetest.com/']);
        $result = Utils::jsonDecode(
            $client->post(
                '/validate',
                [
                    'query' => [
                        'captcha_id' => $captcha_id  // 自动拼接到 URL 后
                    ],
                    'form_params' => $query  // 其他参数放在 body
                ]
            )->getBody(),
            true
        );
        return $result['result'] == 'success';
    } catch (\Exception $e) {
        return false;
    }
});
