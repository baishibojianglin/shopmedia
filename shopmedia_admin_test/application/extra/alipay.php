<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/7/1
 * Time: 18:39
 */

// 支付宝支付配置
return [
    // AppId
    'app_id' => "2021001168635819",
    // 商户私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAsUm1bgpuspYFWGKBrzW0OuacKq6QxiUxAE0PRqatbVwxHSAcCjPnsvB4PbPnUH74DFzSWc39S5sZ7bd/nkctAQfXBBQVd/aJ6MGQ0R8owN30ILb18l8rme4SKnljg3P2aM3p0mq2so5ya26i/9ZGT6unVA8fjPvxwfWoacTTKwrHKZ21HOled695ZN/3jR5fsFBswiStHbtqVZI0Z9UtEMNM+M+K6bmWqwLQk8ebM3CRR0Dd5G/AL4w+GhXLCT5zME8Y6RkDv9ERsGoa8iqL8lm62vl0G7i9C32VW/FWXkteErlNWvRvzAy38j+1QaapWOn66q0pbmQvgZ9rxc0/OQIDAQABAoIBAQCf8JNRNvAvBNDFr/i3DIgQK0Pv7ZSGbb8LOnnxjyUeZ/GXCQTC35jEAdU4NTkVkbZN0N+kTQWaU87MeqMTM7sSFvSPpV7I3w4Dgb0YLDgj5xj6+pRfmCRJtlGFKAXy7Yb7fejX/5Xa+E+ZRDKhA7pi3cUTPKGGGzU7elf5M6weQxTNJIqw+JYJvLNQU4xbtsmOjNH4AUHzaf1wIMXpCJoNVl7AEf/ECx+iOppiy7Q7VbaN67vGRtDy0IP42HBny4lDgcuD1tUe5bb8uNjJrIivznLqi6wB7Z9G7UIh7uzxS2IaW5m/6edFEGxXKWb3RTVcSqXyRiLVLrD9IMiiLspRAoGBAOphwvJnWKr4YyrNXxYrBDkPYDsu1GF+zfmX7RnFQMmxKn6y/6g7ztxRpN19uhHfDpL5ONiXIJbpZK8BMt7ikre5q1BQSMiz12lJ/tgGZLYJhvNudQMxwZ7ECID8vG5BV1l1pabqJxxE6FjLmyKe5MehiUhrqmh2qmtrQzbARlkjAoGBAMGj12a8aNpr59li1vSccCMJqL+TIGGPzsxnZsMhBMdQ6Hmn/kST7NMyXvxuwSTqRmC7uSzDti5gUWiA3wC5xA/HcIEy4Lb15dp6RJSSWKkM51fCtJh7bMydeyvlNAurTQ9ytK/Ll7D0NFn91JujloLIDm/mBufaXMkKoI9HNIHzAoGAVfbXTpsTDVoe3SMIHhRW7yqxi+NoJ/4fQ80yPEJ5ucAOKvwyAp01CN+1DTvA1C8wpD1eWSpM3KzrrbhN1SvaziH+MG3R1DMJ7eci3k6x/4ZNBdncdvh34GhcChsobXPvurMIt8in4Zlwcqjy1Gbc2E9qD7LVhI0Jgm6L73fkFWsCgYANSwUXfWmpTO0OpFVjV3XvQdN+y0fWyruElricPqEIWcqLx3eSF6GTYgrZQ3Uo5phMPbbZltnj1yLfjLFCaH5IIwXbKLX6eWj9FZWtqVpCyKr9AFXLffWbGliBS+vFvU29+L7krpJMSIdrghxdTt6fPcKX9e+VbQ0flAYvr9Cv9wKBgQDW8YCYtlpTHLLxd6uYICB7ajnrZZJ+INsbS4QrPh1GQqdCEr87+/jJ825W+dsr1cqDju49YRwodFbAgdWKvtBF/Q7g0yAUY0pvpPlgomyoaMuR++p/XZLF9kpQ8rMRwX7B+/8ZFcY9ugrJRjBYE3lSvXuxHpAE/olML/72LK+QEw==",
    // 编码格式
    'charset' => "UTF-8",
    // 签名方式
    'sign_type' => "RSA2",
    // 支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
    // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsUm1bgpuspYFWGKBrzW0OuacKq6QxiUxAE0PRqatbVwxHSAcCjPnsvB4PbPnUH74DFzSWc39S5sZ7bd/nkctAQfXBBQVd/aJ6MGQ0R8owN30ILb18l8rme4SKnljg3P2aM3p0mq2so5ya26i/9ZGT6unVA8fjPvxwfWoacTTKwrHKZ21HOled695ZN/3jR5fsFBswiStHbtqVZI0Z9UtEMNM+M+K6bmWqwLQk8ebM3CRR0Dd5G/AL4w+GhXLCT5zME8Y6RkDv9ERsGoa8iqL8lm62vl0G7i9C32VW/FWXkteErlNWvRvzAy38j+1QaapWOn66q0pbmQvgZ9rxc0/OQIDAQAB",
];