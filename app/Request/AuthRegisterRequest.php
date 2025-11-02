<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class AuthRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|regex:/^[\w\x{4e00}-\x{9fa5}]{2,20}$/u',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
                'confirmed', // 要求 password_confirmation 字段与 password 匹配
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '用户名不能为空',
            'name.regex' => '用户名需为2-20位，支持中文、数字、字母和下划线',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'password.required' => '密码不能为空',
            'password.min' => '密码至少8位',
            'password.regex' => '密码需包含字母和数字',
            'password.confirmed' => '两次密码输入不一致',
        ];
    }

    /**
     * // 1. 必须定义：是否允许请求通过（权限控制）.
     */
    public function authorize(): bool
    {
        // 注意：默认返回 false，会直接拒绝请求（导致验证不执行）
        // 开发阶段可先返回 true，确保验证逻辑优先生效
        return true;
    }
}
