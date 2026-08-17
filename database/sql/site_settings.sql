-- Share3a site_settings (single row)
-- Run after migrations: mysql -u USER -p DATABASE < database/sql/site_settings.sql
-- Or use: php artisan db:seed --class=SiteSettingSeeder

-- Ensure table exists (created by migrations)
-- Columns match: site_settings table (id=1 singleton)

DELETE FROM `site_settings` WHERE `id` = 1;

INSERT INTO `site_settings` (
  `id`,
  `site_name_ar`, `site_name_en`, `tagline_ar`, `tagline_en`,
  `about_ar`, `about_en`, `vision_ar`, `vision_en`, `mission_ar`, `mission_en`,
  `study_plan_intro_ar`, `study_plan_intro_en`, `regulations_ar`, `regulations_en`,
  `address_ar`, `address_en`, `phone`, `email`, `whatsapp`,
  `facebook`, `twitter`, `instagram`, `youtube`,
  `logo`, `favicon`, `footer_text_ar`, `footer_text_en`,
  `dashboard_institute_name_ar`, `dashboard_institute_name_en`,
  `academic_year_ar`, `academic_year_en`,
  `dashboard_welcome_ar`, `dashboard_welcome_en`,
  `enable_forum`, `enable_live_lessons`, `enable_hifz`, `enable_honor_board`, `enable_wallet`,
  `dashboard_logo`, `dashboard_use_site_logo`,
  `dashboard_primary_color`, `dashboard_sidebar_color`, `dashboard_accent_color`, `dashboard_background_color`,
  `dashboard_style`, `dashboard_layout`, `dashboard_sidebar_style`, `dashboard_show_pattern`, `dashboard_compact_mode`,
  `admin_brand_name_ar`, `admin_brand_name_en`, `admin_logo`, `admin_use_site_logo`,
  `admin_primary_color`, `admin_sidebar_color`, `admin_accent_color`, `admin_background_color`,
  `admin_style`, `admin_layout`, `admin_navigation`, `admin_sidebar_style`,
  `admin_sidebar_collapsible`, `admin_show_pattern`, `admin_compact_mode`,
  `created_at`, `updated_at`
) VALUES (
  1,
  'معهد إعداد دعاة التوحيد والسنة',
  'Institute for Training Preachers of Tawhid and the Sunnah',
  'إعداد علمي .. تأهيل دعوي',
  'A beacon of Islamic knowledge and spiritual refinement',
  'صرح علمي ودعوي يُعنى بتأصيل العقيدة الصحيحة، والدعوة إلى الكتاب والسنة بفهم سلف الأمة، وإعداد الدعاة وتأهيلهم علميًا ودعويًا؛ ليكونوا قادرين على تبليغ دين الله بالحكمة والبصيرة.',
  'An institution dedicated to scholarship and Da''wah, focused on grounding the correct creed and preparing qualified preachers.',
  'أن نكون مرجعاً علمياً موثوقاً في نشر العلوم الشرعية الصحيحة.',
  'To be a trusted scholarly reference for spreading authentic Islamic knowledge.',
  'تقديم تعليم شرعي متميز يجمع بين الأصالة والمعاصرة.',
  'Deliver distinguished Islamic education that combines authenticity with modern methods.',
  'تُقَدَّم الخطة الدراسية في المعهد على مرحلتين: مرحلة التأسيس (السنة التمهيدية والسنة الأولى والثانية) ومرحلة التخصص (السنة الرابعة والخامسة).',
  NULL,
  NULL,
  NULL,
  'مصر',
  NULL,
  '00201007102523',
  'info@share3a.com',
  '00201007102523',
  'https://www.facebook.com/profile.php?id=61591439512746',
  'https://x.com/duaataltawheed',
  NULL,
  'https://www.youtube.com/@duaataltawheed',
  'settings/01KYQ4V32TKP4WJHWGX38DMJXA.jpg',
  'settings/01KYQ4V32V5K8995EH67TFDV3E.jpg',
  '© معهد علم شرعي — جميع الحقوق محفوظة',
  NULL,
  'معهد العلوم الشرعية',
  'Share3a Institute',
  'العام الدراسي ١٤٤٦ هـ',
  'Academic Year 1446 AH',
  'مرحباً بك في لوحة الطالب — نتمنى لك التوفيق في رحلتك العلمية.',
  'Welcome to your student dashboard.',
  1, 1, 1, 1, 1,
  NULL, 1,
  '#004d40', '#0a3d34', '#c9a227', '#f4f7f6',
  'classic', 'wide', 'dark', 1, 1,
  'معهد إعداد دعاة التوحيد والسنة',
  'Share3a Admin',
  NULL, 1,
  '#059669', '#0f172a', '#c9a227', '#f8fafc',
  'classic', 'wide', 'sidebar', 'dark',
  1, 0, 0,
  NOW(), NOW()
);

-- Note: regulations_ar is long HTML — use `php artisan db:seed --class=SiteSettingSeeder` for full content.
