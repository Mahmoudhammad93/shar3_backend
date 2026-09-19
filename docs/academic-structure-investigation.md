# SHAR3 Academic Structure — Read-Only Investigation Report

**Date:** 2026-09-19  
**Scope:** Investigation only. No files or data were modified during the investigation.  
**Evidence:** Code + schema + production SELECT-only queries.  
**Local DB note:** Local `chiefcode_share3a` has almost no academic rows (levels=0). Production was used for counts/duplication checks only.

---

## Business requirements (target concepts)

The academy needs **two clearly separated concepts**:

### A) Academic curriculum

```
Academic Level
    ↓
Academic Year
    ↓
Semester
    ↓
Subject
    ↓
Educational content / lessons
```

Example:

```
المستوى التمهيدي
└── السنة الأولى
    ├── الفصل الأول
    │   ├── الفقه
    │   ├── العقيدة
    │   └── ...
    └── الفصل الثاني
        ├── ...
        └── ...
```

Three academic levels:

1. **المستوى التمهيدي** — first year; introductory versions of academy sciences
2. **المستوى المتقدم** — second and third years; deeper academic study
3. **المستوى المتخصص** — fourth and fifth years; specialization begins; a student may voluntarily choose more than one specialization

Subjects belonging to selected specializations should be available without duplicate subjects.

### B) General / additional courses

General/additional/special courses must be conceptually separate from the core academic curriculum.

They can be offered:

- during breaks
- between semesters
- between academic years
- as additional or featured courses

They must **NOT** be confused with academic Subjects.

---

## 1. CURRENT DATABASE STRUCTURE

### Core entities and relationships

| Model | Table | Role today |
|---|---|---|
| `AcademicLevel` | `academic_levels` | Level; `curriculum_type`: `general` \| `specialized` |
| `AcademicYear` | `academic_years` | Belongs to level |
| `Semester` | `semesters` | Belongs to year; unique `(academic_year_id, semester_number)` and `(academic_year_id, slug)` |
| `Specialization` | `specializations` | Belongs to specialized level |
| `Subject` | `subjects` | Curriculum catalog item (name, texts, optional `course_id`) |
| `CurriculumAssignment` | `curriculum_assignments` | Places a Subject in a Semester (+ optional Specialization) |
| `Course` | `courses` | Content/offering container (teacher, publish flags, marketing fields) |
| `Lesson` | `lessons` | Belongs to Course only (incl. Bunny fields) |
| `Teacher` | `teachers` | Linked to Course via `teacher_id` |
| `Enrollment` | `enrollments` | Student ↔ Course |

### Text diagram (CURRENT)

```
AcademicLevel
 ├── AcademicYear
 │    └── Semester
 │         └── CurriculumAssignment ──── Subject ──(optional 1:1)── Course
 │                   │                                              ├── Lesson(+Bunny)
 │                   └── specialization_id?                         └── Teacher
 └── Specialization ─┘ (when curriculum_type = specialized)

Student ──placement──> Level / Year / Semester
Student ──M2M──> Specialization (student_specializations)
Student ──Enrollment──> Course
```

**Important:** Subject is **not** directly FK’d to level/year/semester anymore. Placement is only through `curriculum_assignments`.

### Production snapshot (SELECT only)

| Table | Count |
|---|---|
| academic_levels | 3 |
| academic_years | 5 |
| semesters | 10 |
| specializations | 4 |
| subjects | 174 |
| curriculum_assignments | 157 (95 general / 62 specialized) |
| courses | 3 |
| lessons | 3 |
| teachers | 7 |
| enrollments | 6 |

- Subjects with `course_id`: **2**
- Subjects without course: **172**
- `subjects.course_id` has a **unique** index → at most one Subject per Course

---

## 2. CURRENT ACADEMIC FLOW

How an admin effectively builds curriculum today:

1. **Level** (`AcademicLevelResource`) — set `general` or `specialized`
2. **Year** under that level
3. **Semester** under that year
4. **Specialization** (specialized levels only)
5. **Subject catalog** — create Subject (optionally link an existing Course)
6. **Assign Subject to plan** via:
   - `ManageStudyPlan` page, or
   - Semester `GeneralCurriculumRelationManager`, or
   - Specialization `CurriculumAssignmentsRelationManager`
7. **Course** (separate) — create content shell + teacher
8. **Lesson** under Course (relation manager or Lessons resource)

### Missing / indirect steps

- There is **no** direct Level→Subject or Semester→Subject FK.
- Lessons are **not** attached to Subject; only to Course.
- To teach a curriculum Subject with video lessons, admin must: create Course → link Subject.course_id → add Lessons to Course.
- On production, **172/174 subjects have no Course**, so most curriculum items have **no lessons**.

---

## 3. COURSE VS SUBJECT FINDINGS

### Issue 1 — precise answers

1. **What does Course represent today?**  
   Content/offering container (lessons, teacher, publish/pricing/featured, category/program). Also optionally the delivery vehicle for a curriculum Subject.

2. **What does Subject represent today?**  
   Academic curriculum catalog entry (plan texts + placement via assignments). Not a lesson container.

3. **Where do Lessons belong today?**  
   `courses` only (`lessons.course_id`).

4. **How is Teacher connected to Course?**  
   `courses.teacher_id` (BelongsTo).

5. **How is Teacher connected to Subject?**  
   **No direct link.** Study-plan “المحاضر” is free text in `subjects.supplementary_text_ar`.

6. **How is Subject connected to level / year / semester / specialization?**  
   Only via `curriculum_assignments` (`semester_id`, nullable `specialization_id`).

7. **How is Course connected to the academic structure?**  
   **Indirect only** through optional `subjects.course_id`. Courses also have optional `program_id` / `category_id` / string `level` (marketing string, not AcademicLevel FK).

8. **Can a Subject exist without a Course?**  
   **Yes** (normal today).

9. **Can a Course exist without a Subject?**  
   **Yes**.

10. **Can multiple Courses belong to one Subject?**  
    **No** (unique `course_id` on subjects).

11. **Can multiple Teachers teach one Subject?**  
    **No** first-class support (only one teacher on a linked Course, or text field).

12. **Does the current implementation treat Course as…?**  
    **Mixture** — content container + optional academic delivery + public/featured offering.

13. **Where are Lesson records actually stored/linked?**  
    `lessons` table, FK `course_id`.

14. **Is Lesson directly linked to Course, Subject, or both?**  
    Course only, not Subject.

15. **Trace one complete example**

```
Level ──hasMany──> Year ──hasMany──> Semester
                                      │
                                      └── CurriculumAssignment ──> Subject
                                                                    │
                                                         (optional unique)
                                                                    ▼
                                                                  Course ──hasMany──> Lesson
```

Relationships that **do not** exist: Subject→Lesson, Subject→Teacher, Course→Semester, Course→AcademicLevel.

### Why the admin experience is confusing

Admin sees **Courses / Subjects / Lessons** as siblings, but domain roles differ:

| UI label | Actual domain role |
|---|---|
| Subject | المقرر في الخطة |
| Course | وعاء المحتوى / دورة قابلة للتسجيل |
| Lesson | محتوى داخل Course |

Stakeholder docs ask exactly this: where do core-subject lessons go, and where are additional/featured courses? Current naming collapses those concepts.

---

## 4. GENERAL COURSES FINDINGS

### Issue 2 — precise answers

1. **Are general courses stored in the same `courses` table as academic courses?**  
   Yes — only `courses`.

2. **Is there currently a `course_type` or equivalent?**  
   **No**.

3. **What exactly does `is_featured` mean in code?**  
   Marketing/homepage flag. `HomeController` returns featured published courses when site settings schedule allows (`HomepageFeaturedCourses`).

4. **Is featured merely presentation/marketing, or a different type?**  
   Presentation/marketing only.

5. **Is there any explicit separation between academic curriculum and optional/general courses?**  
   Soft only:
   - Academic placement = Subject + CurriculumAssignment
   - Optional/public = Course (often without Subject)
   - No enum/type column

6. **How does the frontend determine which courses students can see?**
   - `/courses` API: `is_published=true`
   - Homepage: featured + schedule gate (backend)
   - Student dashboard curriculum: `ResolveStudentCurriculumAction` (assignments), then Course only if Subject linked

7. **Are hidden/featured courses filtered by backend, frontend, or both?**  
   Mostly **backend** (publish + homepage settings). Frontend mainly respects API payload.

8. **Can the existing schema support the required separation safely?**  
   Partially, with convention (Course without Subject = general). Clean long-term separation needs an **additive** flag/type (or separate table later) — not implemented yet.

---

## 5. SEMESTER DUPLICATION ROOT CAUSE

### Verdict

| Hypothesis | Result |
|---|---|
| A) Duplicate DB semester rows | **No** — production `GROUP BY academic_year_id, semester_number HAVING count>1` = **[]** |
| B) Join duplication | Indirect |
| C) Wrong Eloquent relation | Related |
| **D) API Resource transformation** | **YES — primary root cause** |
| E) Frontend rendering | Secondary — renders API as-is |
| F) Specialization joins | Contributing input to the broken groupBy |

### Exact cause

In `AcademicStructureResource::specializationYearsPayload()`:

```php
->groupBy(fn (CurriculumAssignment $a) => $a->semester?->year_id)
```

`Semester` has **`academic_year_id`**, not `year_id`.  
So the group key is always `null` → **all years of a specialization collapse into one year bucket**.

Then semesters are grouped by real `semester_id`, so year-4 “الفصل الأول” (id 17) and year-5 “الفصل الأول” (id 19) appear as **two siblings both named الفصل الأول**.

### Production API evidence (specialization “التفسير وعلوم القرآن”)

One year object returned, with:

- الفصل الأول (id 19)
- الفصل الأول (id 17)
- الفصل الثاني (id 18)
- الفصل الثاني (id 20)

### Contrast

`ProgramCurriculumSubjects::mapYearsFromAssignments()` correctly groups by `$assignment->semester->year->id` — program pages can be fine while the **study-plan API** is wrong.

### Uniqueness already present

Migration already has:

- `unique(academic_year_id, semester_number)`
- `unique(academic_year_id, slug)`

**Do not** add another uniqueness constraint blindly — production may contain edge cases and a blind unique could break deployment.

**Database data is NOT duplicated.** The UI/API repeats semester *labels* because years were collapsed incorrectly.

---

## 6. FILAMENT TRANSLATION AUDIT

### How translations are handled today

Mostly hard-coded `->label('...')`, mixed Arabic/English. Missing labels fall back to Filament English field names.

Approaches in use:

- `->label()` hard-coded (dominant)
- Laravel translation files (limited)
- Filament localization defaults (fallback for unlabeled fields)
- Mixed approaches across resources

### Course (worst)

**Table** (`CoursesTable`) — many columns have **no Arabic label** → English UI:

- `title_en`, `slug`, `duration_hours`, `level`, `price`
- `is_free`, `is_featured`, `is_published`
- `sort_order`, `start_date`, `end_date`
- duplicate unlabeled `image` column

**Form** (`CourseForm`) — unlabeled / English-ish:

- `description_ar`, `description_en` (no label)
- `duration_hours`, `level`, `price`
- `is_free`, `is_featured`, `is_published`, `sort_order`
- `start_date`, `end_date`
- slug labeled `الرابط (Slug)`

### Lesson

Mostly Arabic. Remaining English/odd:

- Google Drive field labels in English
- `created_at` unlabeled in table (toggleable)

### Subject / Academic Level / Year / Semester / Specialization

Mostly Arabic. Minor English leftovers like `الاسم (English)`.

### Recommended safest consistent approach (not implemented)

Keep hard-coded Arabic `->label()` (already dominant). Optionally later add `lang/ar` for Filament defaults — but don’t mix strategies mid-resource.

---

## 7. ADMIN NAVIGATION AUDIT

### Current groups

**الهيكل الأكاديمي**

- المستويات
- السنوات الدراسية
- الفصول الدراسية
- التخصصات
- مواد الخطة (متقدم) — **navigation hidden** (`shouldRegisterNavigation = false`)

**المحتوى**

- الخطة الدراسية (`ManageStudyPlan`)
- إعلانات / FAQs / إلخ

**الإدارة الأكاديمية**

- الدورات
- الدروس
- الواجبات
- المعلمون / البرامج / التصنيفات / التسجيلات / الشهادات / الجداول / الطلاب

**Quizzes:** no top-level resource; live under Lesson → Questions relation manager.

### Desired conceptual structure

```
الهيكل الأكاديمي
├── المستويات
├── السنوات الدراسية
├── الفصول الدراسية
└── المقررات الدراسية

المحتوى التعليمي
├── الدروس
├── الاختبارات
└── الواجبات

الدورات
└── الدورات العامة / الإضافية
```

### Current vs desired

| Desired | Current fit |
|---|---|
| الهيكل الأكاديمي → مستويات/سنوات/فصول/مقررات | Mostly present; “مقررات” is Study Plan + hidden Subject resource |
| المحتوى التعليمي → دروس/اختبارات/واجبات | دروس+واجبات under academic admin; quizzes nested |
| الدورات → عامة/إضافية | Only one “الدورات” list; no subtype |

### What can change at UI-only vs backend

| Change | Level needed |
|---|---|
| Navigation groups / labels / sort | **UI only** |
| Splitting academic vs general courses | **Domain / schema** |
| Attaching lessons to subjects directly | **Domain / schema** |
| Multi-teacher subjects | **Domain / schema** |

---

## 8. PRODUCTION DATA RISK

### What existing data depends on

- `lessons.course_id` → courses
- `subjects.course_id` (nullable unique) → courses
- `curriculum_assignments` → semester + subject + optional specialization
- `enrollments.course_id` → courses
- `lesson_progress`, `lesson_questions` → lessons
- `assignments` → (existing assignment model; course-related in product)
- student placement FKs → level/year/semester
- `student_specializations` pivot

### Future change risk classes

| Change | Risk |
|---|---|
| Arabic Filament labels only | **SAFE ADDITIVE** |
| Fix `year_id` → `academic_year_id` in API resource | **SAFE ADDITIVE** (behavior fix; no DB write) |
| Navigation regrouping | **SAFE ADDITIVE** |
| Add nullable `course_type` / `is_general` | **SAFE ADDITIVE** |
| Backfill Subject↔Course links | **REQUIRES CARE** |
| Move lessons from Course to Subject | **HIGH RISK** |
| Drop/merge Course & Subject | **HIGH RISK** |
| Add unique constraints on existing dirty data | **HIGH RISK** |
| migrate:fresh / destructive reseed | **HIGH RISK** |

---

## 9. RECOMMENDED TARGET ARCHITECTURE

Keep two clear domains:

### A) Academic curriculum

```
Level → Year → Semester → CurriculumAssignment → Subject
Subject ──(optional)── ContentCourse ── Lessons
```

### B) General / additional courses

```
GeneralCourse (same courses table + type flag, or separate table later)
 └── Lessons
```

### Rules

- Subject = المقرر الأكاديمي
- Course = وحدة المحتوى القابلة للتسجيل/العرض
- Academic Subject may link 0..1 Course for lessons
- General courses never enter `curriculum_assignments`
- Teacher stays on Course (or later CourseTeacher pivot)
- Specializations remain assignment-scoped; student multi-spec already deduped in `ResolveStudentCurriculumAction`

---

## 10. SAFE MIGRATION STRATEGY (conceptual only — do not implement yet)

1. Fix API grouping bug (no DB change).
2. Add nullable additive discriminator on `courses` (`course_kind` / `is_curriculum_content`) — default null = legacy.
3. Do **not** backfill automatically.
4. Gradually link Subjects→Courses only when admin explicitly attaches content.
5. Keep Bunny columns on `lessons`; don’t move media in first phase.
6. Only after inventory, consider UI rename: Subject=مقرر, Course=دورة/محتوى.
7. Never delete old Courses/Subjects; deprecate via flags.

---

## 11. BUNNY COMPATIBILITY

Current media fields on Lesson may include:

- `media_type`
- `video_provider`
- `bunny_library_id`
- `bunny_video_id`
- `bunny_status`
- `google_drive_file_id`
- `google_drive_resource_key`
- `video_url`

Bunny fields live on **`lessons`**, and lessons belong to **`courses`**.

- Restructuring curriculum Subject placement does **not** require Bunny changes.
- Moving lessons off Course later **would** affect Bunny playback paths/admin upload.
- Current Bunny design can remain unchanged while academic modeling is clarified.

**Conclusion:** Bunny Lesson implementation can remain unchanged for the academic restructuring phase.

---

## 12. FILES THAT WOULD EVENTUALLY NEED CHANGES

Do **not** modify these as part of this investigation document.

### Must-fix for semester duplication

- `backend/app/Http/Resources/AcademicStructureResource.php`

### Course/Subject clarity / general courses

- `backend/app/Models/Course.php`
- `backend/app/Models/Subject.php`
- `backend/app/Filament/Resources/Courses/*`
- `backend/app/Filament/Resources/Subjects/*`
- `backend/app/Filament/Pages/ManageStudyPlan.php`
- `backend/app/Http/Controllers/Api/CourseController.php`
- `backend/app/Http/Controllers/Api/HomeController.php`
- `backend/app/Support/HomepageFeaturedCourses.php`
- `backend/app/Actions/ResolveStudentCurriculumAction.php`
- `website/src/components/study-plan/*`
- `website/src/app/courses/*`
- possible new additive migration (nullable only)

### Filament Arabic labels

- `backend/app/Filament/Resources/Courses/Tables/CoursesTable.php`
- `backend/app/Filament/Resources/Courses/Schemas/CourseForm.php`
- related Lesson Drive labels

### Navigation

- Filament `*Resource.php` navigationGroup/sort
- possibly `AdminPanelProvider.php`

---

## 13. INVESTIGATION SAFETY CONFIRMATION

| Check | Result |
|---|---|
| Files created during investigation | **0** |
| Files modified during investigation | **0** |
| Files deleted during investigation | **0** |
| Database INSERTs | **0** |
| Database UPDATEs | **0** |
| Database DELETEs | **0** |
| Migrations executed | **0** |
| Existing records changed | **0** |

### git status (investigation)

**Before:** backend clean / website clean  
**After:** backend clean / website clean  

Working tree remained identical during the investigation itself.

> Note: Creating *this* documentation file is a later, explicit user request and is separate from the read-only investigation.

---

## Bottom line

1. **Subject ≠ Course:** Subject is curriculum; Course is content/offering; Lessons hang only off Course.
2. **No real general-course type** — only publish/featured flags on the same table.
3. **Semester duplication is not bad DB data**; it is an API bug grouping by nonexistent `semester.year_id`, collapsing years and repeating “الفصل الأول/الثاني”.
4. Bunny can stay as-is for now.

---

## Related PDFs reviewed (stakeholder context)

Uploaded stakeholder materials asked where basic curriculum lessons belong vs courses/programs, and where featured/extra courses live. This report maps those questions onto the current technical model.
