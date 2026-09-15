<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Support\WebsiteNavPages;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /** Default + production site settings (single row in `site_settings`). */
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'site_name_ar' => 'معهد إعداد دعاة التوحيد والسنة',
                'site_name_en' => 'Institute for Training Preachers of Tawhid and the Sunnah',
                'tagline_ar' => 'إعداد علمي .. تأهيل دعوي',
                'tagline_en' => 'A beacon of Islamic knowledge and spiritual refinement',
                'about_ar' => 'صرح علمي ودعوي يُعنى بتأصيل العقيدة الصحيحة، والدعوة إلى الكتاب والسنة بفهم سلف الأمة، وإعداد الدعاة وتأهيلهم علميًا ودعويًا؛ ليكونوا قادرين على تبليغ دين الله بالحكمة والبصيرة.',
                'about_en' => 'An institution dedicated to scholarship and Da‘wah (calling to the faith), focused on grounding the correct creed, promoting the Quran and Sunnah according to the understanding of the early generations of the Ummah, and preparing and qualifying preachers—both academically and practically—to convey the religion of Allah with wisdom and insight.',
                'vision_ar' => 'أن نكون مرجعاً علمياً موثوقاً في نشر العلوم الشرعية الصحيحة.',
                'vision_en' => 'To be a trusted scholarly reference for spreading authentic Islamic knowledge.',
                'mission_ar' => 'تقديم تعليم شرعي متميز يجمع بين الأصالة والمعاصرة، وإعداد دعاة مؤهلين على منهج أهل السنة والجماعة.',
                'mission_en' => 'Deliver distinguished Islamic education that combines authenticity with modern methods, preparing qualified callers upon the methodology of Ahl al-Sunnah wal-Jama‘ah.',
                'study_plan_intro_ar' => 'تُقَدَّم الخطة الدراسية في المعهد على مرحلتين: مرحلة التأسيس (السنة التمهيدية والسنة الأولى والثانية) ومرحلة التخصص (السنة الرابعة والخامسة). يُراعى فيها التدرج العلمي من المتون والكتب الأساسية إلى التكميل والتوسع.',
                'study_plan_intro_en' => null,
                'regulations_ar' => <<<'HTML'
<h2>أحكام عامة</h2>
<p>يسعى المعهد إلى تهيئة بيئة علمية منضبطة تحفظ حقوق الطلاب وتكفل جودة التعليم، ويلتزم الطالب بما يلي:</p>
<ul>
<li>الالتزام بالحضور والمواظبة على الدروس والمحاضرات.</li>
<li>احترام أعضاء هيئة التدريس والإداريين والزملاء.</li>
<li>الالتزام بالزي الشرعي اللائق في جميع الأنشطة.</li>
<li>تجنب الغيبة والنميمة والجدال بغير علم.</li>
</ul>

<h2>القبول والتسجيل</h2>
<p>يُشترط للقبول في المعهد استكمال نموذج التسجيل وتقديم المستندات المطلوبة، ويحق للإدارة قبول أو رفض أي طلب وفق المعايير المعتمدة.</p>

<h2>الاختبارات والتقييم</h2>
<p>يُقيَّم الطالب من خلال الاختبارات الفصلية والنهائية، واجتياز اختبارات الدروس، والالتزام بالواجبات والأنشطة التعليمية.</p>

<h2>الانضباط والجزاءات</h2>
<p>في حال مخالفة اللائحة، يحق للإدارة توجيه إنذار أو إيقاف مؤقت أو فصل نهائي بحسب جسامة المخالفة.</p>

<h2>أحكام ختامية</h2>
<p>تحتفظ إدارة المعهد بحق تعديل هذه اللائحة عند الحاجة، ويُبلَّغ الطلاب بأي تحديث عبر القنوات الرسمية للمعهد.</p>
HTML,
                'regulations_en' => null,
                'address_ar' => 'مصر',
                'address_en' => null,
                'phone' => '00201007102523',
                'email' => 'info@share3a.com',
                'whatsapp' => '00201007102523',
                'facebook' => 'https://www.facebook.com/profile.php?id=61591439512746',
                'twitter' => 'https://x.com/duaataltawheed',
                'instagram' => null,
                'youtube' => 'https://www.youtube.com/@duaataltawheed',
                'telegram' => null,
                'logo' => 'settings/logo.png',
                'favicon' => null,
                'footer_text_ar' => '© معهد علم شرعي — جميع الحقوق محفوظة',
                'footer_text_en' => null,
                'homepage_featured_courses_enabled' => false,
                'homepage_featured_courses_visible_from' => null,
                'homepage_featured_courses_visible_until' => null,
                'website_primary_color' => '#002B5B',
                'website_accent_color' => '#C5A04D',
                'website_background_color' => '#f7f9fc',
                'website_color_palette' => 'institute_navy_gold',
                'website_nav_pages' => WebsiteNavPages::normalizeForStorage(WebsiteNavPages::defaults()),
                'dashboard_institute_name_ar' => 'معهد إعداد دعاة التوحيد والسنة',
                'dashboard_institute_name_en' => 'Institute for Training Preachers of Tawhid and the Sunnah',
                'academic_year_ar' => 'العام الدراسي ١٤٤٦ هـ',
                'academic_year_en' => 'Academic Year 1446 AH',
                'dashboard_welcome_ar' => 'مرحباً بك في لوحة الطالب — نتمنى لك التوفيق في رحلتك العلمية.',
                'dashboard_welcome_en' => 'Welcome to your student dashboard — we wish you success in your learning journey.',
                'enable_forum' => true,
                'enable_live_lessons' => true,
                'enable_hifz' => true,
                'enable_honor_board' => true,
                'enable_wallet' => true,
                'dashboard_logo' => null,
                'dashboard_use_site_logo' => true,
                'dashboard_primary_color' => '#004d40',
                'dashboard_sidebar_color' => '#0a3d34',
                'dashboard_accent_color' => '#c9a227',
                'dashboard_background_color' => '#f4f7f6',
                'dashboard_style' => 'classic',
                'dashboard_layout' => 'wide',
                'dashboard_sidebar_style' => 'dark',
                'dashboard_show_pattern' => true,
                'dashboard_compact_mode' => true,
                'admin_brand_name_ar' => 'معهد إعداد دعاة التوحيد والسنة',
                'admin_brand_name_en' => 'Share3a Admin',
                'admin_logo' => null,
                'admin_use_site_logo' => true,
                'admin_primary_color' => '#059669',
                'admin_sidebar_color' => '#0f172a',
                'admin_accent_color' => '#c9a227',
                'admin_background_color' => '#f8fafc',
                'admin_style' => 'classic',
                'admin_layout' => 'wide',
                'admin_navigation' => 'sidebar',
                'admin_sidebar_style' => 'dark',
                'admin_sidebar_collapsible' => true,
                'admin_show_pattern' => false,
                'admin_compact_mode' => false,
            ],
        );
    }
}
