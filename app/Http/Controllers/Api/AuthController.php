<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'education_level' => ['nullable', 'string', 'max:100'],
            'heard_about' => ['nullable', 'string', 'max:255'],
            'works_full_time' => ['nullable', 'boolean'],
            'participates_other_programs' => ['nullable', 'boolean'],
            'daily_hours' => ['nullable', 'string', 'max:50'],
            'accept_terms' => ['accepted'],
        ], [
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
            'accept_terms.accepted' => 'يجب الموافقة على الشروط وسياسة الخصوصية.',
        ]);

        $fullName = trim($validated['first_name'].' '.$validated['last_name']);

        $user = DB::transaction(function () use ($validated, $fullName) {
            $user = User::query()->create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'student',
            ]);

            Student::query()->create([
                'user_id' => $user->id,
                'name' => $fullName,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'whatsapp' => $validated['whatsapp'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'country' => $validated['country'] ?? null,
                'education_level' => $validated['education_level'] ?? null,
                'heard_about' => $validated['heard_about'] ?? null,
                'works_full_time' => $validated['works_full_time'] ?? null,
                'participates_other_programs' => $validated['participates_other_programs'] ?? null,
                'daily_hours' => $validated['daily_hours'] ?? null,
                'terms_accepted_at' => now(),
                'status' => Student::STATUS_ACTIVE,
            ]);

            return $user;
        });

        $token = $user->createToken('student-token')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح',
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! password_verify($validated['password'], $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        if (! in_array($user->role, ['student', 'admin', 'staff'])) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $token = $user->createToken('student-token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }

    private function userPayload(User $user): array
    {
        $user->load('student');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'student_id' => $user->student?->id,
        ];
    }
}
