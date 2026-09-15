<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+[1-9]\d{6,14}$/'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^\+[1-9]\d{6,14}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today', 'after:1940-01-01'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'education_level' => ['nullable', 'in:primary,preparatory,secondary,diploma,bachelor,master,phd,other'],
            'heard_about' => ['nullable', 'in:friend,social,search,teacher,other'],
            'works_full_time' => ['nullable', 'boolean'],
            'participates_other_programs' => ['nullable', 'boolean'],
            'daily_hours' => ['nullable', 'in:1-2,2-4,4-6,6+'],
            'accept_terms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
            'accept_terms.accepted' => 'يجب الموافقة على الشروط وسياسة الخصوصية.',
            'phone.regex' => 'رقم الجوال غير صالح. اختر كود الدولة وأدخل الرقم بشكل صحيح.',
            'whatsapp.regex' => 'رقم الواتساب غير صالح. اختر كود الدولة وأدخل الرقم بشكل صحيح.',
            'birth_date.before' => 'تاريخ الميلاد يجب أن يكون في الماضي.',
            'birth_date.after' => 'تاريخ الميلاد غير صالح.',
            'gender.in' => 'النوع المحدد غير صالح.',
            'education_level.in' => 'المستوى التعليمي غير صالح.',
            'heard_about.in' => 'اختيار «كيف سمعت عن المعهد» غير صالح.',
            'daily_hours.in' => 'اختيار عدد الساعات غير صالح.',
        ];
    }
}
