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

class UserProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bio' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'github' => 'nullable|string|max:100',
            'twitter' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'bio.string' => '个人简介格式不正确',
            'bio.max' => '个人简介不能超过500个字符',
            'website.url' => '个人网站格式不正确',
            'website.max' => '个人网站不能超过255个字符',
            'location.string' => '所在地格式不正确',
            'location.max' => '所在地不能超过100个字符',
            'company.string' => '公司格式不正确',
            'company.max' => '公司不能超过100个字符',
            'github.string' => 'GitHub用户名格式不正确',
            'github.max' => 'GitHub用户名不能超过100个字符',
            'twitter.string' => 'Twitter用户名格式不正确',
            'twitter.max' => 'Twitter用户名不能超过100个字符',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
